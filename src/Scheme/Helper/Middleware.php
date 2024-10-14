<?php

    namespace App\Routing\Scheme\Helper;

    use App\Routing\Http\Controller;

    trait Middleware
    {
        private int $totalAdded = 0;

        /**
         * Assign middleware to the route.
         *
         * This method allows you to attach one or multiple middleware to the route.
         * Middleware can be specified as a string (e.g., 'auth') or as an array.
         * If a string middleware contains a colon, it will be split into an array
         * to handle parameters (e.g., 'auth:admin').
         *
         * If the middleware is not a global function, the method will attempt to find
         * the corresponding method within the currently fetched controller, enabling
         * controller-based middleware assignment.
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

                // Increment
                $this->totalAdded += 1;

                // Append
                self::$middlewares[] = $middleware;
            }

            // Return
            return $this;
        }

        protected function updateMiddlewareList(): void
        {
            if ($this->totalAdded) {
                // Remove the last $totalElementToRemove elements from the middlewares array
                self::$middlewares = array_slice(self::$middlewares, 0, -$this->totalAdded);
            }
        }
    }