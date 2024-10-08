<?php

    namespace App\Routing\Http;

    use App\Routing\Scheme\Http;

    class Constraint extends Http {

        /**
         * Class alias name.
         *
         * @var string Required.
         */
        public static string $name = 'constraint';

        /**
         * Note: Only accept class name (string).
         *
         * @param array $action
         * @return void
         */
        protected function commence(array $action): void
        {
            // todo: params constraint...
        }

        /**
         * Auto remove if this class object is about to end.
         *
         * @return void
         */
        protected function destroy(): void
        {
            if (self::$constraints) {
                unset(self::$constraints[count(self::$constraints) - 1]);
                self::$constraints = array_values(self::$constraints);
            }
        }

        /**
         * Group callback...
         *
         * @param \Closure $callback
         * @return $this
         */
        public function group(\Closure $callback): self
        {
            $callback();
            return $this;
        }

        /**
         * Retrieves all the constraints buffered.
         *
         * @return array
         */
        public static function fetch(): array {
            return self::$constraints;
        }
    }