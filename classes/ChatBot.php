<?php
// This class handles the main chatbot logic. It receives user input,
// extracts ingredients, sends them to RecipeAPI, and formats the
// results through ResponseFormatter. It returns a final readable
// message for the chat interface.

class ChatBot
{
    private RecipeAPI $recipeApi;
    private ResponseFormatter $formatter;
    private AIProcessor $ai;

    // The chatbot depends on RecipeAPI + ResponseFormatter
    public function __construct(RecipeAPI $recipeApi, ResponseFormatter $formatter, AIProcessor $ai)
    {
        $this->recipeApi = $recipeApi;
        $this->formatter = $formatter;
        $this->ai = $ai;
    }

    // Main handler for user input
    public function handleMessage(string $userMessage): string
    {
        // Trim the input
        $clean = trim($userMessage);

        // Split ingredients on commas
        $ingredients = $this->extractIngredients($clean);

        // If nothing valid found
        if (empty($ingredients)) {
            return "I couldn't understand any ingredients. Try something like: chicken, pasta, garlic.";
        }

        try {
            // Fetch recipes from the API
            $recipes = $this->recipeApi->searchByIngredients($ingredients, 5);

            if (!empty($recipes)) {
                // Normal formatted list
                $recipeListText = $this->formatter->formatRecipeList($recipes);
            
            // Build a short list of recipe titles for the AI prompt (max 3)
                $titles = [];
                for ($i = 0; $i < count($recipes) && $i < 3; $i++) {
                    $titles[] = $recipes[$i]['title'] ?? 'Unknown title';
                }

                // Ask Gemini for 2–3 short tips or comments
                $prompt  = "The user has these ingredients: " . implode(', ', $ingredients) . ". ";
                $prompt .= "We found these recipes: " . implode('; ', $titles) . ". ";
                $prompt .= "Give 2-3 short tips or comments about these suggestions. ";
                $prompt .= "Format the answer as a numbered list with line breaks. ";
                $prompt .= "Keep the answer friendly and easy to read.";


                $aiText = $this->ai->generateText($prompt);

                // Convert markdown bold (**text**) from AI to real <strong> tags
                $aiText = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $aiText);

                // Litt ekstra: legg linjeskift før hvert nummer hvis alt kommer i én blokk
                $aiText = preg_replace('/\s*(\d+\.\s+)/', "<br><br>$1", $aiText);
                $aiText = trim($aiText);

                // Return both: list + AI text under
                return $recipeListText
                . "\n\n"
                . "<p><strong>Tips for these recipes:</strong></p>"
                . $aiText;

            }
            

            // If no recipes were found, ask Gemini for ideas instead
            $prompt  = "The user has these ingredients: " . implode(', ', $ingredients) . ". ";
            $prompt .= "No exact recipes were found in the database. ";
            $prompt .= "Suggest 3-5 simple meal ideas that could work with these ingredients. ";
            $prompt .= "Keep the answer short, clear and practical.";

            return $this->ai->generateText($prompt);

        } catch (RuntimeException $e) {

            // If something goes wrong during API calls
            return "Sorry, I had trouble fetching recipes. Error: " . $e->getMessage();
        }
    }

    // Extract ingredients from a comma-separated string
    private function extractIngredients(string $text): array
    {
        $parts = explode(',', $text);
        $clean = [];

        for ($i = 0; $i < count($parts); $i++) {
            $item = trim($parts[$i]);

            if ($item !== '') {
                $clean[] = $item;
            }
        }

        return $clean;
    }
}