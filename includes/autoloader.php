<?php

// This file automatically loads PHP classes from the /classes directory.
// When a class is used, this autoloader finds the correct file and includes it.
// This removes the need for many manual require_once statements.

spl_autoload_register(function ($class) {
    $path = __DIR__ . '/../classes/' . $class . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});