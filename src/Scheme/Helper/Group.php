<?php

    namespace App\Routing\Scheme\Helper;

    use Closure;

    trait Group
    {
        private int $totalAdded = 0;

        /**
         * Groups routes under a common configuration using a callback.
         *
         * This method executes a callback function, which can define a group of routes
         * under a shared configuration such as a prefix or middleware.
         *
         * @param Closure $callback A closure that defines the grouped routes.
         *
         * @return $this Returns the current instance for chaining.
         */
        public function group(Closure $callback): self
        {
            self::$groups[] = $callback;
            return $this;
        }

        protected function updateGroupList(): void
        {
            if ($this->totalAdded) {
                self::$groups = array_slice(self::$groups, 0, -$this->totalAdded);
            }
        }
    }