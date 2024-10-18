<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    function includeAllFiles($directory) {
        // Create a RecursiveIterator to iterate through all files and subdirectories
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

        // Loop through each item in the directory
        foreach ($iterator as $file) {
            // Only include PHP files
            if ($file->isFile() && $file->getExtension() === 'php') {
                require_once $file->getPathname();
            }
        }
    }

    spl_autoload_register(function ($class) {
        // Define your namespaces and their corresponding directory paths
        $namespaces = [
            'App\\Routing\\' => __DIR__ . '/src/',
            'App\\Routing\\Http\\' => __DIR__ . '/src/Controllers/Http/',
            'App\\Routing\\Controllers\\Requests\\' => __DIR__ . '/src/Controllers/Requests/',
            'App\\Routing\\Interfaces\\' => __DIR__ . '/src/Interfaces/',
            'App\\Routing\\Scheme\\Scheme\\Blueprints\\' => __DIR__ . '/src/Scheme/Blueprints/',
            'App\\Routing\\Scheme\\Helper\\' => __DIR__ . '/src/Scheme/Helper/',
            'App\\Routing\\Scheme\\' => __DIR__ . '/src/Scheme/'
        ];

        // Loop through each namespace and check if the class belongs to it
        foreach ($namespaces as $namespace => $baseDir) {
            // Does the class use this namespace?
            if (strpos($class, $namespace) === 0) {
                // Remove the namespace prefix
                $relativeClass = str_replace($namespace, '', $class);
                // Replace namespace separators with directory separators in the relative class name
                $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

                // If the file exists, require it
                if (file_exists($file)) {
                    require_once $file;
                }
            }
        }
    });

    // Example usage: include all files in the "src" directory
    includeAllFiles(__DIR__ . '/src/Controllers/Http');
    includeAllFiles(__DIR__ . '/src/Controllers/Requests');

    require_once 'test.php';