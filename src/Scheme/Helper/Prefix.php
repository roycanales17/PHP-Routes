<?php

    namespace App\Routing\Scheme\Helper;

    /**
     * Trait Prefix
     *
     * Provides functionality for managing route prefixes within the routing scheme. This trait allows
     * routes to be grouped under a common URI segment, making it easier to organize and manage
     * routes with a shared path prefix.
     *
     * @package App\Routing\Scheme\Helper
     */
    trait Prefix
    {
        /**
         * @var int $totalAdded
         * Keeps track of the number of prefixes added during the current operation.
         */
        private int $totalAdded = 0;

        /**
         * Adds a prefix to the route URI.
         *
         * This method appends the specified prefix to the route's URI. It is useful for grouping routes under
         * a shared namespace or path, which can help in managing similar routes together. The method
         * also increments the `$totalAdded` count each time a prefix is added to track the number of prefixes
         * added in the current instance.
         *
         * @param string $prefix The prefix to be added to the route URI.
         *
         * @return self Returns the current instance for method chaining.
         */
        public function prefix(string $prefix): self
        {
            if ($prefix) {
                $this->totalAdded += 1;
                self::$prefixes[] = $prefix;
            }
            return $this;
        }

        /**
         * Updates the prefix list by removing prefixes added during the current operation.
         *
         * This protected method ensures that any prefixes added by the `prefix` method are removed
         * when they are no longer needed. It reduces the `self::$prefixes` array by slicing off
         * the number of prefixes specified by `$totalAdded`.
         *
         * @return void
         */
        protected function updatePrefixList(): void
        {
            if ($this->totalAdded) {
                self::$prefixes = array_slice(self::$prefixes, 0, -$this->totalAdded);
            }
        }
    }
