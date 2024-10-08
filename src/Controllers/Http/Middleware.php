<?php

    namespace App\Routing\Http;

    use App\Routing\Scheme\Http;
    use Closure;

    /**
     * Class Middleware
     *
     * Manages the registration and lifecycle of middleware classes for routing.
     * It extends the Http base class, providing
     * functionality for grouping middlewares, fetching them, and ensuring their
     * proper cleanup.
     */
    class Middleware extends Http {

        /**
         * Class alias name for identifying the type of Http object.
         *
         * @var string
         */
        private static string $name = 'middleware';

        /**
         * Registers a middleware class based on the provided action.
         *
         * This method processes the action array, expecting a class name (string). It can also
         * handle middleware with parameters using a colon syntax. If the action is a function, it verifies
         * its existence. If it is a method within a controller, it associates it with the relevant class.
         *
         * @param array $action The action array containing the middleware class name or function.
         *
         * @return void
         */
        protected function commence(array $action): void
        {
            $action = $action[0] ?? [];

            if (is_string($action)) {
                // Handle middleware with parameters using colon syntax
                if (strpos($action, ':') !== false) {
                    $action = explode(':', $action);
                } else {
                    // Check if function exists or is a method within a controller
                    if (!function_exists($action)) {
                        $className = Controller::fetch();
                        if ($className) {
                            $action = [$className, $action];
                        }
                    }
                }
            }
            self::$middlewares[] = $action;
        }

        /**
         * Removes the last registered middleware when the object is destroyed.
         *
         * This method ensures that the most recently registered middleware is removed
         * from the middlewares array when the object is no longer in use.
         *
         * @return void
         */
        protected function destroy(): void
        {
            if (self::$middlewares) {
                unset(self::$middlewares[count(self::$middlewares) - 1]);
                self::$middlewares = array_values(self::$middlewares);
            }
        }

        /**
         * Groups routes under a common configuration using a callback.
         *
         * This method executes a callback function, which can define a group of routes
         * under a shared configuration such as a prefix or middleware.
         *
         * @param Closure $callback A closure that defines the grouped routes.
         * @return $this Returns the current instance for chaining.
         */
        public function group(Closure $callback): self
        {
            $callback();
            return $this;
        }

        /**
         * Retrieves all the buffered middlewares.
         *
         * This method returns all the middlewares currently registered in the buffer.
         *
         * @return array An array of all registered middlewares.
         */
        public static function fetch(): array
        {
            return self::$middlewares;
        }
    }
