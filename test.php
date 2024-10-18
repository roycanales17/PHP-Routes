<?php

    use App\Routing\Route;

    /**
     * Class auth
     *
     * This class is used for testing different authentication levels.
     * It contains methods that simulate user roles and authentication checks.
     */
    class auth
    {
        /**
         * Checks if the user is authenticated.
         *
         * @return bool True if the user is authenticated, false otherwise.
         */
        public function is_authenticated(): bool {
            return true;
        }

        /**
         * Checks if the user is a guest.
         *
         * @return bool True if the user is a guest, false otherwise.
         */
        public function is_guest(): bool {
            return true;
        }

        /**
         * Checks if the user is an admin.
         *
         * @return bool True if the user is an admin, false otherwise.
         */
        public function is_admin(): bool {
            return true;
        }
    }

    /**
     * Class User
     *
     * This class contains methods that return different pages based on the user's role.
     */
    class User {

        /**
         * Returns the admin page content.
         *
         * @return string The content for the admin page.
         */
        function admin_page(): string {
            return 'Hello Admin!';
        }

        /**
         * Returns the guest page content.
         *
         * @return string The content for the guest page.
         */
        function guest_page(): string {
            return 'Hello Guest!';
        }

        /**
         * Returns the admin5 page content.
         *
         * @return string The content for the admin5 page.
         */
        function admin5_page(): string {
            return 'Hello Admin 5!';
        }

        /**
         * Returns the admin6 page content.
         *
         * @return string The content for the admin6 page.
         */
        function admin6_page(): string {
            return 'Hello Admin 6!';
        }
    }

    // Grouping routes for authenticated users with specific middleware.
    Route::group([
        'controller' => auth::class,
        'middleware' => 'is_authenticated'
    ], function () {
        // Route for guests
        Route::get('/', [ User::class, 'guest_page' ])->middleware('is_guest');
        // Route for admin users
        Route::get('/admin', [ User::class, 'admin_page' ])->middleware('is_admin');
    });

    // Group routes with multiple middleware applied.
    Route::middleware([ 'auth:is_authenticated', 'auth:is_admin' ])->group(function () {
        Route::get('/admin2', function() {
            return 'Admin 2 Test!';
        });
    });

    // Group routes controlled by the auth class with middleware for admin users.
    Route::controller(auth::class)->group(function () {
        Route::get('/admin3', function() {
            return 'Admin 3 Test!';
        });
    })->middleware('is_admin');

    // Nested groups with multiple middleware applied.
    Route::controller(auth::class)->group(function () {

        // Group routes that require admin access.
        Route::middleware('is_admin')->group(function () {
            // Nested group that also requires guest access.
            Route::middleware('is_guest')->group(function () {
                Route::get('/admin4', function() {
                    return 'Admin 4 Test!';
                })->middleware('is_authenticated');
            });
        });

        // Group routes that require authentication.
        Route::middleware('is_authenticated')->group(function () {
            // Group routes controlled by the User class.
            Route::controller(User::class)->group(function () {
                Route::get('/admin5', 'admin5_page');
                Route::get('/admin6', 'admin6_page')->middleware([ 'auth:is_admin' ]);
            });
        });
    });
