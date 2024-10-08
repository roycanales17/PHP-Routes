<?php

    namespace App\Routing\Scheme;

    use App\Routing\Route;
    use App\Routing\Scheme\Blueprints\Config;
    use ReflectionException;

    /**
     * Abstract class Facade
     *
     * Provides methods for registering routes and managing route instances.
     */
    abstract class Facade
    {
        use Config;

        /**
         * Registers a route based on the provided configuration.
         *
         * This method retrieves the route object, iterates through available actions, and returns a compiled instance
         * if a matching route instance is found based on the configuration.
         *
         * @param array $config The configuration array containing:
         *                     - 'method': The HTTP method (GET, POST, etc.).
         *                     - 'args': Arguments to pass when compiling the route.
         *
         * @return object|null An instance of the compiled route or null if no matching instance is found.
         * @throws ReflectionException
         */
        protected static function registerRoute(array $config): object|null
        {
            $route = self::getObject();
            foreach ($route->getActions() as $http) {

                if ($instance = ($route->getInstance($config['method'] ?? '', $http, $config['args'] ?? []))) {
                    return $instance;
                }
            }
            return null;
        }

        /**
         * Retrieves an instance of the current route object.
         *
         * This method uses late static binding to get the called class and instantiate it.
         *
         * @return Route The instantiated route object.
         */
        private static function getObject(): Route
        {
            $class = get_called_class();
            return new $class();
        }

        /**
         * Gets an instance of a subclass based on the provided HTTP method and abstract class.
         *
         * This method checks if the subclass contains the specified property and if it matches the HTTP method.
         * If a match is found, it returns an instance of that class.
         *
         * @param string $http The HTTP method (e.g., 'GET', 'POST').
         * @param string $abstract The name of the abstract class to search for subclasses.
         * @param array $args Functions args
         *
         * @return object|null An instance of the subclass or null if no matching subclass is found.
         * @throws ReflectionException
         */
        private function getInstance(string $http, string $abstract, array $args): object|null
        {
            $httpLists = $this->getSubclassesOf($abstract);
            $httpName = $this->classAlias;

            foreach ($httpLists as $class) {

                if (property_exists($class, $httpName)) {
                    $reflection = new \ReflectionClass($class);

                    // Class name
                    $property = $reflection->getProperty($httpName);
                    $property->setAccessible(true);
                    $propertyValue = $reflection->getProperty($httpName);

                    // Check if class property name is match
                    if (strtolower($propertyValue->getValue()) === strtolower($http)) {

                        $method = $reflection->getMethod('compile');
                        $method->setAccessible(true);
                        return $method->invoke((new $class), $args);
                    }
                }
            }
            return null;
        }

        /**
         * Gets all subclasses of the specified class.
         *
         * Iterates through all declared classes and returns an array of subclasses that extend the given class name.
         *
         * @param string $className The name of the class to find subclasses of.
         *
         * @return array An array containing the names of the subclasses.
         */
        private function getSubclassesOf(string $className): array
        {
            $subclasses = [];
            foreach (get_declared_classes() as $class) {

                if (is_subclass_of($class, $className)) {
                    $subclasses[] = $class;
                }
            }
            return $subclasses;
        }
    }
