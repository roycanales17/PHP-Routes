<?php

	use App\Routes\Route;

	// Replace this with `require 'vendor/autoload.php';`
	// when using this package in another project or when Composer is available.
	spl_autoload_register(function (string $class) {
		$namespaces = [
			'App\\Routes\\' => __DIR__ . '/app/',
		];

		foreach ($namespaces as $namespace => $baseDir) {
			if (!str_starts_with($class, $namespace)) {
				continue;
			}

			$relativeClass = str_replace($namespace, '', $class);
			$file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

			if (file_exists($file)) {
				require_once $file;
			}
		}
	});

	try {
		Route::configure(__DIR__, [
			'routes/web.php',
		])->routes(function($routes) {
			// Use this callback to inspect or debug the registered routes.
			// Append `?debug=1` to the URL to print the route definitions.
			if (isset($_GET['debug'])) {
				echo '<pre>';
				print_r($routes);
				echo '</pre>';
			}
		})->captured(function ($content) {
			echo $content;
		});
	} catch (Throwable $exception) {
		var_dump($exception->getMessage());
		// Handle errors here (e.g., logging, reporting, or sending automated error notifications).
	}