<?php

    namespace App\Routing;

    use App\Routing\Http\{Controller, Group, Middleware, Prefix};
    use App\Routing\Interfaces\Registrar;
    use App\Routing\Scheme\Facade;
    use ReflectionException;

    /**
     * Class Route
     *
     * This class extends the Facade and provides static methods for route registration and configuration.
     * It allows defining routes for different HTTP methods (GET, POST, PUT, PATCH, DELETE) as well as
     * additional configuration options like controllers, middleware, and prefixes.
     *
     * @method static Registrar put(string $uri, string|array|\Closure $action = []) Defines a PUT route.
     * @method static Registrar patch(string $uri, string|array|\Closure $action = []) Defines a PATCH route.
     * @method static Registrar delete(string $uri, string|array|\Closure $action = []) Defines a DELETE route.
     * @method static Registrar get(string $uri, string|array|\Closure $action = []) Defines a GET route.
     * @method static Registrar post(string $uri, string|array|\Closure $action = []) Defines a POST route.
     * @method static Group group(\Closure $action) Registers a group of routes with shared configurations and middleware, enhancing route organization and reusability.
     * @method static Controller controller(string $className) Registers a controller.
     * @method static Middleware middleware(string|array $action) Registers middleware for the route.
     * @method static Prefix prefix(string $prefix) Adds a prefix to the route URI.
     */
    class Route extends Facade
    {
        /**
         * Dynamically handles static method calls for route registration.
         *
         * This method captures calls to static methods like `get`, `post`, `put`, etc., and registers
         * the corresponding routes using the `registerRoute` method. The method name indicates the HTTP method,
         * and the arguments contain the URI and action to be executed.
         *
         * @param string $name The HTTP method name (e.g., 'get', 'post', 'put', etc.).
         * @param array $arguments The arguments passed, typically including the URI and action.
         *
         * @return object|null An instance of the registered route or null if no instance is found.
         *
         * @throws ReflectionException If there is an issue with reflecting the registered route.
         */
        public static function __callStatic(string $name, array $arguments): object|null
        {
            return self::registerRoute([
                'method' => $name,
                'args' => $arguments
            ]);
        }
    }
