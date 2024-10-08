<?php

    namespace App\Routing\Http;

    use App\Routing\Scheme\Http;
    use Closure;

    /**
     * Class Controller
     *
     * Manages the registration and lifecycle of controller classes for routing.
     * It extends the Http base class. This class
     * provides functionality for grouping routes, fetching controllers, and ensuring
     * the proper cleanup of registered controllers.
     */
    class Controller extends Http {

        /**
         * Class alias name for identifying the type of Http object.
         *
         * @var string
         */
        private static string $name = 'controller';

        /**
         * Registers a controller class based on the provided action.
         *
         * This method processes the action array, expecting a class name (string). It validates
         * if the class exists and, if so, registers it. Throws an exception if the class does not exist.
         *
         * @param array $action The action array containing the controller class name.
         *
         * @throws \InvalidArgumentException if the class does not exist.
         *
         * @return void
         */
        protected function commence(array $action): void
        {
            $action = $action[0] ?? [];

            if (is_string($action)) {

                // Check if class exists
                if (!class_exists($action)) {
                    throw new \InvalidArgumentException("Class `$action` does not exist");
                }

                // Register the controller class
                self::$controllers[] = $action;
            }
        }

        /**
         * Removes the last registered controller when the object is destroyed.
         *
         * This method ensures that the most recently registered controller is removed
         * from the controllers array when the object is no longer in use.
         *
         * @return void
         */
        protected function destroy(): void
        {
            if (self::$controllers) {
                unset(self::$controllers[count(self::$controllers) - 1]);
                self::$controllers = array_values(self::$controllers);
            }
        }

        /**
         * Groups routes under a common configuration using a callback.
         *
         * This method executes a callback function, which can define a group of routes
         * under a shared configuration such as a prefix or middleware.
         *
         * @param Closure $callback A closure that defines the grouped routes.
         *
         * @return $this Returns the current instance for chaining.
         */
        public function group(Closure $callback): self
        {
            $callback();
            return $this;
        }

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

            self::$middlewares[] = $middleware;
            return $this;
        }

        /**
         * Add a prefix to the route URI.
         *
         * This method appends a given prefix to the route's URI, which can be useful for
         * grouping routes under a common namespace or path.
         */
        public function prefix(string $prefix): self
        {
            if ($prefix) {
                self::$prefixes[] = $prefix;
            }
            return $this;
        }

        /**
         * Retrieves the most recently registered controller.
         *
         * This method returns the last controller registered in the buffer.
         * If no controllers are registered, it returns an empty string.
         *
         * @return string The name of the last registered controller or an empty string.
         */
        public static function fetch(): string
        {
            return self::$controllers ? end(self::$controllers) : '';
        }
    }
