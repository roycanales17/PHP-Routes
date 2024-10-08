<?php

    use App\Routing\Route;

    class test {
        function index(): void {
            echo 'Hello Index!';
        }

        function is_authenticated(): bool {
            return true;
        }
    }

    Route::controller( test::class )->group(function () {
        Route::middleware('is_authenticated')->group(function () {

            Route::get('/', function () {
                echo 'Hello World!';
            });

            Route::get('/test/{name}', function ($name) {
                echo "Hello World! [$name]";
            })
            ->prefix('api')
            ->prefix('v1')
            ->name('test');

        });
    });