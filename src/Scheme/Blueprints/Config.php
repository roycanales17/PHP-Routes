<?php

    namespace App\Routing\Scheme\Blueprints;

    use App\Routing\Http\{Constraint, Middleware};
    use App\Routing\Scheme\Http;
    use App\Routing\Scheme\Requests;

    /**
     * Trait Config
     *
     * Provides a configuration blueprint for routing schemes, including actions and route guards.
     * This trait can be used by classes to define and retrieve routing actions and guards.
     */
    trait Config
    {
        /**
         * Alias used for class identification.
         *
         * This alias is used within the class to identify or map to a specific property name.
         *
         * @var string
         */
        protected string $classAlias = 'name';

        /**
         * Array of available actions for routing.
         *
         * This array holds references to classes that represent different actions in routing schemes.
         *
         * @var array
         */
        protected array $actions = [
            Http::class,
            Requests::class
        ];

        /**
         * Guard settings for route validation.
         *
         * This array defines which classes serve as guards (e.g., middleware, constraints) and
         * whether they are enabled or required (boolean value).
         *
         * @var array
         */
        protected array $routesGuard = [
            Middleware::class   => true,
            Constraint::class   => true
        ];

        /**
         * Retrieves the array of defined actions.
         *
         * @return array An array of classes representing the available routing actions.
         */
        public function getActions(): array
        {
            return $this->actions;
        }
    }
