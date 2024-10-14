<?php

    namespace App\Routing\Http;

    use App\Routing\Scheme\Helper\Group;
    use App\Routing\Scheme\Helper\Middleware;
    use App\Routing\Scheme\Helper\Controller;
    use App\Routing\Scheme\Http;

    /**
     * Class Prefix
     *
     * Handles route prefixing with support for controllers, middlewares, and group functionalities.
     * Extends the Http class and provides methods to manage route prefixes.
     *
     * @package App\Routing\Http
     */
    class Prefix extends Http
    {
        use Group;
        use Controller;
        use Middleware;

        /**
         * @var string $name The type of the route handler (prefix in this case).
         */
        private static string $name = 'prefix';

        /**
         * Adds a new prefix to the list if provided.
         *
         * @param array $action An array containing the prefix to be added. Only the first element is used.
         * @return void
         */
        protected function commence(array $action): void
        {
            $action = $action[0] ?? '';
            if ($action) {
                self::$prefixes[] = $action;
            }
        }

        /**
         * Removes the most recently added prefix from the list.
         *
         * This function updates the internal prefix array by removing the last element.
         * It ensures the integrity of the prefix array by re-indexing it.
         *
         * @return void
         */
        protected function destroy(): void
        {
            if (self::$prefixes) {
                unset(self::$prefixes[count(self::$prefixes) - 1]);
                self::$prefixes = array_values(self::$prefixes);
            }
        }

        /**
         * Retrieves the list of current prefixes.
         *
         * @return array The array of all prefixes currently set.
         */
        public static function fetch(): array
        {
            return self::$prefixes;
        }
    }
