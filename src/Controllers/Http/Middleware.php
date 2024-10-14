<?php

    namespace App\Routing\Http;

    use App\Routing\Scheme\Http;
    use App\Routing\Scheme\Helper\{Group,Controller,Prefix,Middleware as Middleware2};

    /**
     * Class Middleware
     *
     * Manages the registration and lifecycle of middleware classes for routing.
     * It extends the Http base class, providing
     * functionality for grouping middlewares, fetching them, and ensuring their
     * proper cleanup.
     */
    class Middleware extends Http {

        use Group;
        use Prefix;
        use Controller;
        use Middleware2;

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
            $this->middleware($action[0] ?? []);
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
