<?php

// This class handles all communication with the Spoonacular API.
// It sends ingredient-based recipe requests and returns simplified recipe data.
// Used by the chatbot to fetch real recipes from the internet.

class RecipeAPI
{
    // Base URL for the Spoonacular API
    private string $baseUrl = 'https://api.spoonacular.com';

    // The API key used for all requests
    private string $apiKey;

    // Constructor stores the key
    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    // Search for recipes based on a list of ingredients
    public function searchByIngredients(array $ingredients, int $limit = 5): array
    {
        // Clean the ingredient list
        $cleanIngredients = [];
        for ($i = 0; $i < count($ingredients); $i++) {
            $item = trim((string)$ingredients[$i]);
            if ($item !== '') {
                $cleanIngredients[] = $item;
            }
        }

        if (empty($cleanIngredients)) {
            return [];
        }

        // Build the query for the API
        $params = [
            'ingredients' => implode(',', $cleanIngredients),
            'number'      => $limit,
            'apiKey'      => $this->apiKey,
        ];

        $url = $this->baseUrl . '/recipes/findByIngredients?' . http_build_query($params);

        // Send the HTTP request with cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);

        // Handle connection errors
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new RuntimeException('Error calling Recipe API: ' . $error);
        }

        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Handle HTTP errors (4xx, 5xx)
        if ($status >= 400) {
            throw new RuntimeException('Recipe API returned HTTP status ' . $status);
        }

        // Decode the JSON response
        $data = json_decode($response, true);

        if (!is_array($data)) {
            throw new RuntimeException('Recipe API returned invalid JSON.');
        }

        // Convert API response to a simple structure
        $recipes = [];
        for ($i = 0; $i < count($data); $i++) {
            $item = $data[$i];

            $recipes[] = [
                'id'    => $item['id']    ?? null,
                'title' => $item['title'] ?? '',
                'image' => $item['image'] ?? '',
                'likes' => $item['likes'] ?? 0,
            ];
        }

        return $recipes;
    }
}