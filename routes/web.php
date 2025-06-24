<?php

	use App\Routes\Route;

    /*
    |--------------------------------------------------------------------------
    | Web Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register web routes for your application.
    | Now create something great!
    |
    */

	Route::get('/', function () {
		$name = 'Robroy';
		$accountUrl = Route::link('account', ['name' => $name]);

		echo '<h1>Hello, World!</h1>';
		echo '<p>Welcome to our startup platform. We\'re glad you\'re here.</p>';
		echo '<p><a href="' . htmlspecialchars($accountUrl) . '">Who built this?</a></p>';
	});

	Route::get('/account/{name}', function ($name) {
		$safeName = htmlspecialchars($name);

		echo "<h1>👋 Hey there!</h1>";
		echo "<p>This startup app was handcrafted by <strong>{$safeName}</strong> — a passionate programmer who dreams in code.</p>";
		echo "<p>Built with ❤️, caffeine, and a dash of PHP magic.</p>";
	})
	->name('account');
