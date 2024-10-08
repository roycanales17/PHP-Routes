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
         * Holds all registered global class controllers.
         *
         * This array stores all the controllers that are registered during the routing process.
         *
         * @var array
         */
        private static array $controllers = [];

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

        public function middleware(array|string $actions): self
        {

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
