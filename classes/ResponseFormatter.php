<?php
// This class converts raw recipe data from the API into readable text.
// It formats lists of recipes into clean, user-friendly messages for the chatbot.
// Makes API output easy to display in the chat interface.

class ResponseFormatter
{
    // Formats a list of recipes into readable text for the chatbot
    public function formatRecipeList(array $recipes): string
    {
        // If the API returned no results
        if (empty($recipes)) {
            return "I couldn't find any recipes with those ingredients.";
        }

        $lines = [];

        // Add a small header
        $lines[] = "Here are some recipes you can try:";

        // Limit how many recipes we show (for a cleaner message)
        $max = min(3, count($recipes));

        // Loop through each recipe and add a formatted line
        for ($i = 0; $i < $max; $i++) {
            $r = $recipes[$i];

            $title = $r['title'] ?? 'Unknown title';
            $likes = $r['likes'] ?? 0;

            // Example: "1) Chicken Alfredo — 2 likes"
            $number = $i + 1;
            $lines[] = $number . ") " . $title . " — " . $likes . " likes";
        }

        // Join all lines with line breaks so it becomes a readable block of text
        return implode("\n", $lines);
    }

    // Optionally: format one recipe in detail (not required yet)
    public function formatSingleRecipe(array $recipe): string
    {
        $title = $recipe['title'] ?? 'Unknown title';
        $image = $recipe['image'] ?? '';
        $likes = $recipe['likes'] ?? 0;

        $text  = "Recipe: {$title}\n";
        $text .= "Likes: {$likes}\n";
        $text .= "Image: {$image}\n";

        return $text;
    }
}