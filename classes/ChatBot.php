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

    // The chatbot depends on RecipeAPI + ResponseFormatter + AIProcessor
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

        // Extract ingredients from user message
        $ingredients = $this->ai->extractIngredients($clean);

        // If nothing valid found
        if (empty($ingredients)) {
            return "I couldn't understand any ingredients. Try something like: chicken, pasta, garlic.";
        }

        try {
            // Fetch recipes from the API
            $recipes = $this->recipeApi->searchByIngredients($ingredients, 3);

            // If we found recipes, show list + AI tips
            if (!empty($recipes)) {
                // Normal formatted recipe list
                $recipeListText = $this->formatter->formatRecipeList($recipes);
            
                // Build a short list of recipe titles for the AI prompt (max 3)
                $titles = [];
                for ($i = 0; $i < count($recipes) && $i < 3; $i++) {
                    $titles[] = $recipes[$i]['title'] ?? 'Unknown title';
                }

                // Ask Gemini for 2–3 short tips or comments
                $prompt  = "The user has these ingredients: " . implode(', ', $ingredients) . ". ";
                $prompt .= "We found these recipes: " . implode('; ', $titles) . ". ";
                $prompt .= "Give 2–3 short tips or comments about these suggestions. ";
                $prompt .= "Format the answer as a numbered list (1., 2., 3.) with line breaks. ";
                $prompt .= "Use **bold** only for recipe names or important phrases. ";
                $prompt .= "Keep the answer friendly and easy to read.";

                $aiText = $this->ai->generateText($prompt);
                $aiText = $this->formatAiText($aiText);

                // Return both: list + AI text under
                $linebreak = "<br><br>";
                return $recipeListText . $linebreak . $aiText;
            }

            // If no recipes were found, ask Gemini for ideas instead
            $prompt  = "The user has these ingredients: " . implode(', ', $ingredients) . ". ";
            $prompt .= "No exact recipes were found in the database. ";
            $prompt .= "Suggest 3-5 simple meal ideas that could work with these ingredients. ";
            $prompt .= "Format the answer as a numbered list (1., 2., 3., ...) with each idea on its own line. ";
            $prompt .= "Use **bold** for the meal titles. ";
            $prompt .= "Keep the answer short, clear and practical.";

            $aiText = $this->ai->generateText($prompt);
            $aiText = $this->formatAiText($aiText);

            // Same look & feel as the recipe list block
            return "<p><strong>Here are some simple ideas you can try:</strong></p>" . $aiText;

        } catch (RuntimeException $e) {
            // If something goes wrong during API calls
            return "Sorry, I had trouble fetching recipes. Error: " . $e->getMessage();
        }
    }

    // Helper: format AI-generated text so it looks nice in the chat UI
    private function formatAiText(string $text): string
    {
        // Convert markdown bold (**text**) to real <strong> tags
        $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);

        // Add line breaks before each numbered item (1., 2., 3., ...)
        $text = preg_replace('/\s*(\d+\.\s+)/', "<br><br>$1", $text);

        // Trim whitespace at the start and end
        return trim($text);
    }
}