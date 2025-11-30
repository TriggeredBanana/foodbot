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

        // We keep using an array of lines and join them at the end
        $lines = [];

        // Header section
        $lines[] = "<p><strong>Here are some recipes you can try:</strong></p>";

        // Limit how many recipes to show
        $max = min(3, count($recipes));

        // Loop through the recipes and format each entry
        for ($i = 0; $i < $max; $i++) {
            $r = $recipes[$i];

            // Basic safety: escape user-controllable output
            $title = htmlspecialchars($r['title'] ?? 'Unknown title', ENT_QUOTES, 'UTF-8');
            $likes = (int)($r['likes'] ?? 0);
            $image = isset($r['image'])
                ? htmlspecialchars($r['image'], ENT_QUOTES, 'UTF-8')
                : '';

            $number = $i + 1;

            // Optional image output (only if the API provided one)
            $imageHtml = '';
            if ($image !== '') {
                $imageHtml =
                    "<img src=\"{$image}\" alt=\"{$title}\" " .
                    "style=\"width:100%;max-width:260px;border-radius:10px;" .
                    "display:block;margin-bottom:6px;\">";
            }

            // Build a formatted recipe block
            $lines[] =
                "<div class=\"recipe-item\" style=\"margin-bottom:12px;\">" .
                    $imageHtml .
                    "<div><strong>{$number}) {$title}</strong> — {$likes} likes</div>" .
                "</div>";
        }

        // Combine everything into a single HTML string
        return implode("\n\n", $lines);
    }

    // Optional detailed formatter (not used yet)
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