<?php

    namespace App\Routing\Http;

    use App\Routing\Scheme\Blueprints\Config;
    use InvalidArgumentException;
    use App\Routing\Scheme\Helper\{Prefix, Middleware, Controller, Group as Group2};
    use App\Routing\Scheme\Http;
    use ReflectionException;

    /**
     * Class Group
     *
     * This class extends the Http class to provide functionality for grouping routes
     * under shared configurations. It leverages various traits to manage route prefixes,
     * middleware, controllers, and other settings. By organizing routes into logical
     * groups, it enhances the maintainability and readability of the routing setup
     * within the application.
     *
     * @package App\Routing\Http
     */
    class Group extends Http
    {
        use Prefix;
        use Middleware;
        use Controller;
        use Config;
        use Group2;

        private static string $name = 'group'; // The name of the route grouping mechanism

        /**
         * Commences the routing actions based on provided configurations.
         *
         * This method processes the given actions to register routes under the specified
         * HTTP methods and applies any specified group configurations (like prefix,
         * controller, and middleware). It utilizes reflection to inspect the subclasses
         * of the Http class and to dynamically invoke methods corresponding to the
         * defined routes.
         *
         * @param array $actions An array where the first element contains route definitions
         *                       (associative array of routes to actions), and the second
         *                       element is a closure for grouping configurations.
         *
         * @return void
         *
         * @throws InvalidArgumentException If no valid routes are found or if the route definitions are not valid.
         * @throws ReflectionException If there is an issue with reflection while inspecting class properties.
         */
        protected function commence(array $actions): void
        {
            $routes = $actions[0] ?? [];
            $callback = $actions[1] ?? null;

            // Collect all subclasses of the Http class
            $subclasses = [];
            $httpClassName = Http::class;
            $httpAlias = $this->getAlias();

            foreach (get_declared_classes() as $class) {
                if (is_subclass_of($class, $httpClassName)) {
                    $subclasses[] = $class;
                }
            }

            // Check and register the routes
            if ($routes) {
                $isValidRouteFound = false;

                foreach ($routes as $route => $action) {
                    foreach ($subclasses as $subclass) {
                        if (property_exists($subclass, $httpAlias)) {
                            $reflection = new \ReflectionClass($subclass);

                            // Retrieve the property for the current HTTP alias
                            $propertyValue = $reflection->getProperty($httpAlias);
                            $method = $propertyValue->getValue();

                            // Validate and register the route
                            if (strtolower($method) === strtolower($route)) {
                                $isValidRouteFound = true;
                                $this->$method($action); // Register the route
                            }
                        }
                    }
                }

                // Proceed to group the routes if at least one valid route was found
                if ($isValidRouteFound && is_callable($callback)) {
                    $this->group($callback);
                } elseif (!$isValidRouteFound) {
                    throw new InvalidArgumentException("No valid routes found for the given actions.");
                }
            }
        }
    }
