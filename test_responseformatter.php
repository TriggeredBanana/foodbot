<?php

// This file tests the ResponseFormatter class together with RecipeAPI.
// It fetches real recipes from the Spoonacular API and formats them
// into readable chatbot-friendly output.

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/includes/autoloader.php';
require_once __DIR__ . '/config/api_keys.php';

// Create API + formatter objects
$api = new RecipeAPI(SPOONACULAR_API);
$formatter = new ResponseFormatter();

// Ingredients to test with
$ingredients = ['beef', 'potatoes', 'carrots'];

try {
    // Fetch raw recipes
    $recipes = $api->searchByIngredients($ingredients, 5);

    // Format the readable message
    $formatted = $formatter->formatRecipeList($recipes);

    echo "<h2>Formatter Test Successful ✔️</h2>";
    echo "<pre>{$formatted}</pre>";

} catch (RuntimeException $e) {
    echo "<h2>Formatter Test Failed ❌</h2>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}