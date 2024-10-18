<?php

    namespace App\Routing\Scheme\Helper;

    /**
     * Trait Controller
     *
     * Provides functionality to register and manage controller classes for routing purposes.
     * This trait allows the routing system to associate specific controller classes with routes,
     * enabling organized and maintainable route handling logic. It ensures that only valid
     * controller classes are registered and facilitates method chaining for fluent API design.
     *
     * @package App\Routing\Scheme\Helper
     */
    trait Controller
    {
        /**
         * @var int $totalAdded
         * Keeps track of the number of controller classes added during the current operation.
         */
        private int $totalAdded = 0;

        /**
         * Registers a controller class for routing.
         *
         * This method adds the specified controller class name to the list of controllers.
         * It increments the total count of controllers added and ensures that the class
         * exists before registration. If the class does not exist, an exception is thrown.
         *
         * Example usage:
         * ```php
         * $this->controller(SomeController::class);
         * ```
         *
         * @param string $className The fully qualified class name of the controller.
         *
         * @return $this Returns the current instance for method chaining, allowing further configuration.
         *
         * @throws \InvalidArgumentException If the specified class does not exist.
         */
        public function controller(string $className): self
        {
            if (class_exists($className)) {
                $this->totalAdded += 1;

                // Config name
                $name = $this->configName();

                // Class names
                self::$controllersName[] = $name;

                // Check if not exist
                if ( !(self::$controllers[$name] ?? false) ) {
                    self::$controllers[$name] = [];
                }

                // Register the controller class
                self::$controllers[$name][] = $className;

            } else {
                throw new \InvalidArgumentException("Class `$className` does not exist");
            }

            return $this;
        }

        /**
         * Updates the list of registered controllers by removing the most recently added ones.
         *
         * This method checks if any controllers were added since the last update and removes
         * them from the controller list accordingly. It ensures that the controller list remains
         * accurate and reflects the current state of added controllers.
         *
         * @return void
         */
        protected function updateControllerList(): void
        {
            if ($this->totalAdded) {
                // Config name
                $name = $this->configName();

                // Check if exist
                if (self::$controllers[$name] ?? false) {

                    // Remove the last $totalAdded elements from the controllers array
                    self::$controllers[$name] = array_slice(self::$controllers[$name], 0, -$this->totalAdded);

                    // Remove if empty
                    if (empty(self::$controllers[$name])) {
                        unset(self::$controllers[$name]);
                    }
                }
            }
        }
    }
