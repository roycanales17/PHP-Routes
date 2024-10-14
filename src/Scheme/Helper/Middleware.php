<?php

    namespace App\Routing\Scheme\Helper;

    use App\Routing\Http\Controller;

    /**
     * Trait Middleware
     *
     * Provides functionality for assigning and managing middleware within the routing scheme.
     * This trait allows routes to be assigned middleware either as standalone functions
     * or as methods within a controller. It also handles middleware with parameters.
     *
     * @package App\Routing\Scheme\Helper
     */
    trait Middleware
    {
        /**
         * @var int $totalAdded
         * Tracks the number of middleware instances added during the current operation.
         */
        private int $totalAdded = 0;

        /**
         * Assign middleware to the route.
         *
         * This method allows you to attach one or multiple middleware to the route. Middleware can be provided
         * as a string (e.g., 'auth') or as an array. If a string middleware contains a colon, it is split into
         * an array to handle parameters (e.g., 'auth:admin'). If the middleware is not a global function, the
         * method will try to find it as a method within the currently fetched controller, allowing for controller-based
         * middleware assignment.
         *
         * @param array|string $middleware The middleware to be attached to the route. It can be a single middleware
         *                                 as a string or an array of middleware.
         *
         * @return self Returns the current instance for method chaining.
         */
        public function middleware(array|string $middleware): self
        {
            if ($middleware) {
                if (is_string($middleware)) {

                    // Handle middleware with parameters using colon syntax
                    if (strpos($middleware, ':') !== false) {
                        $middleware = explode(':', $middleware);

                    } else {

                        // Check if function exists or is a method within a controller
                        if (!function_exists($middleware)) {
                            $className = Controller::fetch();

                            if ($className) {
                                $middleware = [$className, $middleware];
                            }
                        }
                    }
                }

                // Increment the count of added middleware
                $this->totalAdded += 1;

                // Append middleware to the middlewares array
                self::$middlewares[] = $middleware;
            }

            // Return the current instance for chaining
            return $this;
        }

        /**
         * Updates the middleware list by removing middleware instances added during the current operation.
         *
         * This protected method ensures that any middleware added during the current operation is removed
         * when it is no longer needed. It reduces the `self::$middlewares` array by slicing off the number of
         * middleware instances specified by `$totalAdded`.
         *
         * @return void
         */
        protected function updateMiddlewareList(): void
        {
            if ($this->totalAdded) {
                // Remove the last $totalAdded elements from the middlewares array
                self::$middlewares = array_slice(self::$middlewares, 0, -$this->totalAdded);
            }
        }
    }
