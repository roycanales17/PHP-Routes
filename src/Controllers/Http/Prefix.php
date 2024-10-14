<?php

    namespace App\Routing\Http;

    use App\Routing\Scheme\Helper\Group;
    use App\Routing\Scheme\Helper\Middleware;
    use App\Routing\Scheme\Helper\Controller;
    use App\Routing\Scheme\Http;
    use Closure;

    class Prefix extends Http {

        use Group;
        use Controller;
        use Middleware;

        private static string $name = 'prefix';

        protected function commence(array $action): void
        {
            $action = $action[0] ?? '';
            if ($action) {
                self::$prefixes[] = $action;
            }
        }

        protected function destroy(): void
        {
            if (self::$prefixes) {
                unset(self::$prefixes[count(self::$prefixes) - 1]);
                self::$prefixes = array_values(self::$prefixes);
            }
        }

        public static function fetch(): array
        {
            return self::$prefixes;
        }
    }
