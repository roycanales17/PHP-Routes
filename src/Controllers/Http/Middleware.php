<?php

    namespace App\Routing\Http;

    use App\Routing\Scheme\Http;
    use App\Routing\Scheme\Helper\{Group, Controller, Prefix, Middleware as Middleware2};

    /**
     * Class Middleware
     *
     * This class manages the registration, organization, and lifecycle of middleware components within the routing system.
     * It extends the base Http class and provides additional functionality to support grouping, prefixing,
     * and managing controllers for middleware usage. This enables a flexible and modular approach for handling
     * middleware in the application.
     *
     * @package App\Routing\Http
     */
    class Middleware extends Http
    {
        use Group;
        use Prefix;
        use Controller;
        use Middleware2;

        /**
         * Class alias name for identifying the type of Http object.
         *
         * This static property is used as an identifier or alias for middleware within the routing system.
         *
         * @var string
         */
        private static string $name = 'middleware';

        /**
         * Registers a middleware component based on the provided action.
         *
         * This method interprets the action array, typically expecting the name of a middleware class (as a string).
         * It supports handling middleware with parameters using a colon syntax (e.g., "ClassName:param1,param2").
         * If the action refers to a function, it validates its existence. When it is a method within a controller,
         * it binds it to the appropriate controller class for execution.
         *
         * @param array $action An array containing the middleware class name or function.
         *
         * @return void
         */
        protected function commence(array $action): void
        {
            $this->middleware($action[0] ?? []);
        }

        /**
         * Removes the last registered middleware.
         *
         * This method removes the most recently registered middleware from the internal middlewares array,
         * ensuring that middleware components are cleaned up when they are no longer in use.
         * It helps maintain the integrity and consistency of the middleware stack.
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
         * Retrieves all registered middlewares currently in the buffer.
         *
         * This method returns an array containing all the middlewares that have been registered,
         * allowing inspection or manipulation of the middleware stack at runtime.
         *
         * @return array An array of all currently registered middlewares.
         */
        public static function fetch(): array
        {
            return self::$middlewares;
        }
    }
