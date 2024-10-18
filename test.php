<?php

    use App\Routing\Route;

    class auth
    {
        public function is_authenticated(): bool {
            return true;
        }
        public function is_guest(): bool {
            return true;
        }

        public function is_admin(): bool {
            return true;
        }
    }

    class User {

        function admin_page(): string {
            return 'Hello Admin!';
        }

        function guest_page(): string {
            return 'Hello Guest!';
        }

        function admin5_page() : string {
            return 'Hello Admin 5!';
        }

        function admin6_page() : string {
            return 'Hello Admin 6!';
        }
    }

    Route::group([
        'controller' => auth::class,
        'middleware' => 'is_authenticated'
    ], function () {
        Route::get('/', [ User::class, 'guest_page' ])->middleware('is_guest');
        Route::get('/admin', [ User::class, 'admin_page' ])->middleware('is_admin');
    });

    Route::middleware([ 'auth:is_authenticated', 'auth:is_admin' ])->group(function () {
       Route::get('/admin2', function() {
           return 'Admin 2 Test!';
       });
    });

    Route::controller(auth::class)->group(function () {
        Route::get('/admin3', function() {
            return 'Admin 3 Test!';
        });
    })->middleware('is_admin');

    Route::controller(auth::class)->group(function () {

        Route::middleware('is_admin')->group(function () {
            Route::middleware('is_guest')->group(function () {
                Route::get('/admin4', function() {
                    return 'Admin 4 Test!';
                })->middleware('is_authenticated');
            });
        });

        Route::middleware('is_authenticated')->group(function () {
            Route::controller(User::class)->group(function () {
                Route::get('/admin5', 'admin5_page' );
                Route::get('/admin6', 'admin6_page' )->middleware([ 'auth:is_admin' ]);
            });
        });

    });