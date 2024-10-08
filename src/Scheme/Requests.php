<?php

    namespace App\Routing\Scheme;

    use App\Routing\Interfaces\Registrar;
    use App\Routing\Http\{Controller, Middleware, Prefix};
    use App\Routing\Scheme\Blueprints\Action;
    use App\Routing\Scheme\Blueprints\Extensions;
    use Closure;
    use ReflectionException;

    /**
     * Abstract class Method
     *
     * This class represents a routing method that handles route definitions, including URIs, actions,
     * middleware, and constraints. It provides a structure for defining and launching HTTP routes.
     */
    abstract class Requests implements Registrar
    {
        use Action; // Trait that provides action-related methods
        use Extensions; // Trait that adds additional route functionality

        /**
         * @var string $uri The URI for the route.
         */
        protected string $uri = '';

        /**
         * @var string $alias An alias for the route, which can be used for reference.
         */
        protected string $alias = '';

        /**
         * @var array $constraints Holds all constraints applied to the route.
         */
        protected array $constraints = [];

        /**
         * @var array $middlewares The middlewares associated with the route.
         */
        protected array $middlewares = [];

        /**
         * @var array $prefixes The prefixes for the route method.
         */
        protected array $prefixes = [];

        /**
         * @var array $routeNames Holds the names of routes for easy reference.
         */
        protected static array $routeNames = [];

        /**
         * @var array|string|Closure $action The action to be executed for the route,
         * which can be a callable, a string representing a function name, or a Closure.
         */
        public array|string|Closure $action = [];

        /**
         * Compile the route by registering its URI and action.
         *
         * This method processes an array of actions, initializing the route's middleware,
         * URI, and action based on the provided inputs.
         *
         * @param array $actions The actions associated with the route, where:
         *                      - $actions[0]: The URI of the route.
         *                      - $actions[1]: The action to be executed (function name or callable).
         *
         * @return Requests Returns the current instance for method chaining, or null on failure.
         */
        public function compile(array $actions): self
        {
            // Register middlewares and set the URI
            $this->middlewares = Middleware::fetch();
            $this->uri = $actions[0] ?? '';

            // Determine the action to execute
            $action = $actions[1] ?? '';
            if ($actions && $this->initialize($action) === false) {
                switch (true) {
                    case is_string($action):
                        $class = Controller::fetch();
                        if ($class) {
                            $this->action = [$class, $action]; // Set action as an array with controller and action name
                        } elseif (function_exists($action)) {
                            $this->action = $action; // Set action as a callable function
                        }
                        break;

                    case is_callable($action):
                    case is_array($action):
                        $this->action = $action; // Set action if it is callable or an array
                        break;
                }
            }
            return $this;
        }

        /**
         * Destructor method that launches the route when the object is destroyed.
         *
         * This method checks for the existence of a 'launch' method and invokes it,
         * passing relevant route information such as URI, alias, prefixes, action,
         * middlewares, and constraints etc.
         * @throws ReflectionException
         */
        function __destruct()
        {
            if (method_exists($this, 'launch')) {
                $this->launch([
                    'uri' => $this->uri,
                    'alias' => $this->alias,
                    'prefix' => $this->prefixes,
                    'semi_prefix' => Prefix::fetch(),
                    'action' => $this->action,
                    'middlewares' => $this->middlewares,
                    'constraints' => $this->constraints
                ]);
            }
        }
    }
