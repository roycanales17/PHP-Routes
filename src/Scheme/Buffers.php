<?php

    namespace App\Routing\Scheme;

    class Buffers
    {
        /**
         * @var array This array stores all the controllers that are registered during the routing process.
         */
        protected static array $controllers = [];

        /**
         * @var array Middlewares associated with the route.
         */
        protected static array $middlewares = [];

        /**
         * @var array Holds all the global class constraints.
         */
        protected static array $constraints = [];

        /**
         * @var array $prefixes The prefixes for the route method.
         */
        protected static array $prefixes = [];

        /**
         * @var array $groups Holds the route group routes.
         */
        protected static array $groups = [];
    }