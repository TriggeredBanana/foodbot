<?php
/** 
 * Sanitize user input for safe display and processing 
 */
function sanitize_input($data) {
    $data = trim($data);

    // Protects against XSS, quotes are handled correctly and invalid chars are safely replaced.
    $data = htmlspecialchars($data, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    
    return $data;
}

/**
 * Validates ingredient list & checks if list is within limits
 */
function validate_ingredients($ingredients_string) {
    $ingredients = explode(',', $ingredients_string); // Break string into array by ","
    $ingredients = array_map('trim', $ingredients); // Uses "trim" on all array elements (get rid of spaces)
    $ingredients = array_filter($ingredients); // Removes potential empty values in array

    // Check if ingredient limit is hit
    if (count($ingredients) > MAX_INGREDIENTS) {

        // Displays an error message if over limit
        return [
            'valid' => false,
            'error' => "Too many ingredients. Maximum at once is: " . MAX_INGREDIENTS,
            'ingredients' => []
        ];
    }

    // Otherwise returns valid
    return [
        'valid' => true,
        'error' => null,
        'ingredients' => $ingredients
    ];
}
?>