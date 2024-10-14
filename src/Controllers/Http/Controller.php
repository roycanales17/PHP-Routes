<?php

    namespace App\Routing\Http;

    use App\Routing\Scheme\Http;
    use App\Routing\Scheme\Helper\{Group, Middleware, Prefix};

    /**
     * Class Controller
     *
     * This class manages the registration, validation, and lifecycle of controller classes used within the routing system.
     * It extends the base Http class and provides additional functionality for grouping routes, handling middleware,
     * and prefixing routes associated with controllers.
     *
     * @package App\Routing\Http
     */
    class Controller extends Http
    {
        use Group;
        use Middleware;
        use Prefix;

        /**
         * Class alias name for identifying the type of Http object.
         *
         * This static property is used as an identifier or alias for controllers within the routing system.
         *
         * @var string
         */
        private static string $name = 'controller';

        /**
         * Registers a controller class based on the provided action.
         *
         * This method processes the action array and expects a class name as a string. It validates
         * the existence of the class and, if valid, registers it in the controllers array. If the class
         * does not exist, it throws an InvalidArgumentException to ensure proper handling.
         *
         * @param array $action The action array containing the controller class name as its first element.
         *
         * @throws \InvalidArgumentException If the class does not exist.
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
         * Removes the last registered controller.
         *
         * This method ensures that the most recently registered controller is removed from the internal controllers array,
         * maintaining the integrity of the controller stack when controllers are no longer needed.
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
         * Retrieves the most recently registered controller.
         *
         * This method returns the name of the last controller registered in the buffer. If no controllers
         * are registered, it returns an empty string.
         *
         * @return string The name of the last registered controller or an empty string if none exist.
         */
        public static function fetch(): string
        {
            return self::$controllers ? end(self::$controllers) : '';
        }
    }
