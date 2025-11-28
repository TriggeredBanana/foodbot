<?php

// Show all errors for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Load autoloader (loads classes from /classes)
require_once __DIR__ . '/includes/autoloader.php';

// Load API keys file (defines constants)
require_once __DIR__ . '/config/api_keys.php';

// Read Spoonacular key from constant
$apiKey = defined('SPOONACULAR_API') ? SPOONACULAR_API : '';

// Create RecipeAPI object with the key
$api = new RecipeAPI($apiKey);

// Test ingredients
$ingredients = ['chicken', 'rice', 'tomatoes'];

try {
    $recipes = $api->searchByIngredients($ingredients, 5);

    echo "<h2>API Test Successful ✔️</h2>";
    echo "<p>Here are the recipes returned:</p>";
    echo "<pre>";
    var_dump($recipes);
    echo "</pre>";

} catch (RuntimeException $e) {
    echo "<h2>API Test Failed ❌</h2>";
    echo "<p>Error message:</p>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}