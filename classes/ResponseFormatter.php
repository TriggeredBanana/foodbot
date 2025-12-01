<?php
// This class converts raw recipe data from the API into clean,
// user-friendly HTML that displays well inside the chat UI.

class ResponseFormatter
{
    // Formats a list of recipes into readable HTML for the chatbot
    public function formatRecipeList(array $recipes): string
    {
        // No results returned by the API
        if (empty($recipes)) {
            return "I couldn't find any recipes with those ingredients.";
        }

        $lines = [];

        // Header section
        $lines[] = "<p><strong>Here are some recipes you can try:</strong></p>";

        // Limit how many recipes to show
        $max = min(3, count($recipes));

        // Loop through the recipes and format each entry (no images)
        for ($i = 0; $i < $max; $i++) {
            $r = $recipes[$i];

            $title = htmlspecialchars($r['title'] ?? 'Unknown title', ENT_QUOTES, 'UTF-8');
            $likes = (int)($r['likes'] ?? 0);
            $number = $i + 1;

            // singular/plural
            $likeWord = ($likes === 1) ? 'like' : 'likes';

            // Example: "1) Spaghetti Bolognese — 12 likes"
            $lines[] = "<strong>{$title}</strong> — {$likes} {$likeWord}";
        }

        // Join with HTML line breaks so it renders nicely in the bubble
        return implode("<br><br>", $lines);
    }

    // Optional detailed formatter (kept simple, no image handling)
    public function formatSingleRecipe(array $recipe): string
    {
        $title = $recipe['title'] ?? 'Unknown title';
        $likes = $recipe['likes'] ?? 0;

        $text  = "Recipe: {$title}\n";
        $text .= "Likes: {$likes}\n";

        return $text;
    }
}