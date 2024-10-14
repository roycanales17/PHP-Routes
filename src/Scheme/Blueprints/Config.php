<?php

    namespace App\Routing\Scheme\Blueprints;

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
        protected static string $classAlias = 'name';

        /**
         * Array of available actions for routing.
         *
         * This array holds references to classes that represent different actions in routing schemes.
         *
         * @var array
         */
        protected static array $actions = [
            Http::class,
            Requests::class
        ];

        /**
         * Retrieves the array of defined actions.
         *
         * @return array An array of classes representing the available routing actions.
         */
        public static function getActions(): array
        {
            return self::$actions;
        }

        public static function getAlias(): string
        {
            return self::$classAlias;
        }
    }
