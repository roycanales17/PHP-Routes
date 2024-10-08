<?php

    namespace App\Routing\Scheme\Blueprints;

    use ReflectionException;

    trait Action
    {
        use Config;
        use Validations;
        use Reflections;

        /**
         * URI Parameters.
         *
         * @var array
         */
        protected array $params = [];

        /**
         * Debugging evaluation information.
         *
         * @var array
         */
        protected static array $evaluation = [];

        /**
         * Development Trace.
         *
         * @return bool Indicates whether the analysis is enabled or not.
         */
        protected function analyze(): bool
        {
            return false;
        }

        /**
         * Validate route if matched and perform the action.
         *
         * @param array $config Configuration settings for the route including:
         *                      - 'uri': The URI of the route.
         *                      - 'alias': The route alias.
         *                      - 'action': The action to be performed.
         *                      - 'prefix': Any URI prefix for the route.
         *                      - 'middlewares': Array of middlewares to apply.
         *                      - 'constraints': Array of constraints for route parameters.
         * @return void
         * @throws ReflectionException
         */
        protected function launch(array $config): void
        {
            $uri = $config['uri'];
            $alias = $config['alias'];
            $action = $config['action'];
            $prefix = $config['prefix'];
            $semi_prefix = $config['semi_prefix'];
            $middlewares = $config['middlewares'];
            $constraints = $config['constraints'];

            // Register route attributes
            if ($alias) {
                self::$routeNames[$alias] = $config;
            }

            // Validate URI/URL
            if ($this->validateURI($uri, array_merge($semi_prefix, $prefix))) {


                // URI parameters.
                $params = $this->params;

                // Validate request method...
                if ($_SERVER['REQUEST_METHOD'] != strtoupper($this::$name)) {
                    return;
                }

                // Validate route parameters constraints...
                if (!$this->validateConstraint($constraints, $params)) {
                    return;
                }

                // Validate middlewares...
                if (!$this->validateMiddlewares($middlewares)) {
                    return;
                }

                // Route ends here...
                exit($this->capture($this->performAction($action)));
            }
        }

        /**
         * Get the value of a specific URI parameter.
         *
         * @param string $name The name of the parameter.
         * @return string The value of the parameter, or an empty string if not set.
         */
        public function getParams(string $name): string
        {
            return $this->params[$name] ?? '';
        }

        /**
         * Capture the output of a callback and return it as a string.
         *
         * @param mixed $callback The callback function whose output should be captured.
         * @return mixed The output of the callback.
         */
        private function capture(mixed $callback): mixed
        {
            ob_start();
            echo $callback;
            return ob_get_clean();
        }
    }
