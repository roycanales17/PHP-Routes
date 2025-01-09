<?php

	use App\Routes\Route;

    spl_autoload_register(function ($class) {
        $namespaces = [
            'App\\Routes\\' => __DIR__ . '/src/'
        ];
        foreach ($namespaces as $namespace => $baseDir) {
            if (strpos($class, $namespace) === 0) {
                $relativeClass = str_replace($namespace, '', $class);
                $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

                if (file_exists($file)) {
                    require_once $file;
                }
            }
        }
    });

	Route::configure(__DIR__, [
		'tests/web.php',
		'tests/test.php'
	])->routes(function (array $routes) {
		// Retrieve here all the routes listed...
	})->captured(function(mixed $content, int $code, string $type) {
		http_response_code($code);
		header('Content-Type: ' . $type);
		echo $content;
	});