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
		echo 'Hello World!';
	});