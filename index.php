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
		'web.php'
	])->captured(function(mixed $content, int $code) {
		echo $content;
	});