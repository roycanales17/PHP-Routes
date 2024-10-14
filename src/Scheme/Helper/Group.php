<?php

    namespace App\Routing\Scheme\Helper;

    use Closure;

    /**
     * Trait Group
     *
     * Provides functionality to group routes under a shared configuration using closures.
     * This trait allows routes to be organized logically by grouping them based on common attributes,
     * such as a prefix, middleware, or any other configuration, enhancing the readability and
     * maintainability of the routing setup.
     *
     * @package App\Routing\Scheme\Helper
     */
    trait Group
    {
        /**
         * @var int $totalAdded
         * Keeps track of the number of group configurations added during the current operation.
         */
        private int $totalAdded = 0;

        /**
         * Groups routes under a common configuration using a callback.
         *
         * This method allows the definition of multiple routes under a shared configuration block
         * (such as a prefix or middleware). The closure provided as a parameter is executed,
         * and within it, developers can define the routes that belong to this group.
         * Grouping routes this way allows for applying settings collectively, improving consistency
         * and reducing code duplication.
         *
         * Example usage:
         * ```php
         * $this->group(function() {
         *     $this->prefix('admin')->middleware('auth')->route('dashboard', ...);
         * });
         * ```
         *
         * @param Closure $callback A closure that defines and configures the grouped routes.
         *
         * @return $this Returns the current instance for method chaining, allowing for
         *               further route or configuration definition.
         */
        public function group(Closure $callback): self
        {
            // Append the callback defining the group to the groups array
            self::$groups[] = $callback;

            // Return the current instance for method chaining
            return $this;
        }

        /**
         * Updates the group list by removing groups added during the current operation.
         *
         * This protected method ensures that any group configurations added during the current
         * operation are removed when they are no longer needed. It slices the `self::$groups`
         * array, removing the number of group instances specified by `$totalAdded`, effectively
         * cleaning up temporary or scoped groups.
         *
         * @return void
         */
        protected function updateGroupList(): void
        {
            if ($this->totalAdded) {
                // Remove the last $totalAdded elements from the groups array
                self::$groups = array_slice(self::$groups, 0, -$this->totalAdded);
            }
        }
    }
