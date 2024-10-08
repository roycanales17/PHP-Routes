<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

spl_autoload_register(function ($class) {
    // Replace the namespace separator with directory separator
    $file = str_replace('\\', DIRECTORY_SEPARATOR, $class);

    // Assuming classes are in the 'src' directory
    $filePath = __DIR__ . '/src/' . str_replace('App/Routing','', $file) . '.php';

    // Check if the file exists before requiring it
    if (file_exists($filePath)) {
        require_once $filePath;
    }
});

function loadAllFiles($directory): void
{
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

    foreach ($iterator as $file) {
        if ($file->isFile() && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            require_once $file->getPathname();
        }
    }
}

loadAllFiles(__DIR__ . '/src/Controllers');

require_once 'web.php';