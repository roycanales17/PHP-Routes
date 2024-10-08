<?php

    use App\Routing\Route;

    class test {
        function index(): void {
            echo 'Hello Index!';
        }

        function is_authenticated(): bool {
            return true;
        }

        function is_guest(): bool {
            return false;
        }
    }

    Route::controller( test::class )->prefix('v1')->middleware('is_authenticated')->group(function () {

            Route::get('/', function () {
                echo 'Hello World!';
            })->middleware('is_guest');

            Route::get('/test/{name}', function ($name) {
                echo "Hello World! [$name]";
            })
            ->prefix('api')
            ->where('name', '[a-z]+')
            ->name('test');
    });