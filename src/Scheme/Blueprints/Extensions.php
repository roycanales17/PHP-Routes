<?php

    namespace App\Routing\Scheme\Blueprints;

    use App\Routing\Http\Controller;
    use App\Routing\Scheme\Requests;

    /**
     * Trait RouteExtensions
     *
     * This trait provides additional functionalities for routing, including methods to
     * set URI prefixes, constraints for URI parameters, and route names. It can be used
     * by classes that manage routing behavior to extend their capabilities.
     */
    trait Extensions
    {
        /**
         * Add a prefix to the route URI.
         *
         * This method appends a given prefix to the route's URI, which can be useful for
         * grouping routes under a common namespace or path.
         *
         * @param string $prefix The prefix to be added to the route URI.
         * @return Requests|Extensions Returns the current instance for method chaining.
         */
        public function prefix(string $prefix): self
        {
            $this->prefixes[] = $prefix; // Add the prefix to the prefixes array
            return $this;
        }

        /**
         * Add a constraint for URI parameters.
         *
         * This method allows you to define constraints on the parameters in the route's URI.
         * Constraints can be set for a single parameter or for multiple parameters at once.
         *
         * @param string|array $params The parameter(s) for which to apply the constraint.
         *                             This can be a single parameter name (string) or
         *                             an associative array of parameters and their constraints.
         * @param string $constraint The constraint to apply to the specified parameter(s).
         *                           If omitted, the constraint will default to an empty string.
         * @return Requests|Extensions Returns the current instance for method chaining.
         */
        public function where(string|array $params, string $constraint = ''): self
        {
            if (is_string($params)) {
                $this->constraints[$params] = $constraint; // Set constraint for a single parameter
            }

            if (is_array($params)) {
                foreach ($params as $key => $const) {
                    $this->constraints[$key] = $const; // Set constraints for multiple parameters
                }
            }
            return $this;
        }

        /**
         * Add a name to the route.
         *
         * This method sets an alias for the route, which can be used for referencing
         * the route elsewhere in the application, such as in route generation or redirection.
         *
         * @param string $name The name (alias) to be assigned to the route.
         * @return Requests|Extensions Returns the current instance for method chaining.
         */
        public function name(string $name): self
        {
            $this->alias = $name; // Assign the provided name to the alias property
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
         *
         * @param string|array $middleware The middleware to be attached. This can be either a string or an array.
         * @return Requests|Extensions Returns the current instance for method chaining.
         */
        public function middleware(string|array $middleware): self
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

            $this->middlewares[] = $middleware;
            return $this;
        }
    }
