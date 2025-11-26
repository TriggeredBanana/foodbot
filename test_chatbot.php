<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/includes/autoloader.php';
require_once __DIR__ . '/config/api_keys.php';

$api = new RecipeAPI(SPOONACULAR_API);
$formatter = new ResponseFormatter();
$bot = new ChatBot($api, $formatter);

echo "<pre>";
echo $bot->handleMessage("chicken, rice, tomato");
echo "</pre>";