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

        // If the user wrote nothing
        if ($clean === '') {
            return "Please write some ingredients, for example: chicken, rice, tomatoes.";
        }

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
                return $this->formatter->formatRecipeList($recipes);
            }

            // If no recipes were found, ask Gemini for ideas instead
            $prompt = "The user has these ingredients: " . implode(', ', $ingredients) . ". "
                . "Suggest 3–5 simple meal ideas that could work with these ingredients. "
                . "Keep the answer short and easy to read.";

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