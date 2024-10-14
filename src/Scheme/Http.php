<?php

    namespace App\Routing\Scheme;

    /**
     * Class Http
     *
     * An abstract base class providing methods to compile actions for routing schemes
     * and manage the lifecycle of route-related objects. This class is designed to be
     * extended by classes that implement routing functionalities like controller, middleware,
     * prefix, constraint, and group handling.
     */
    abstract class Http extends Buffers
    {
        /**
         * Compiles the provided action and initiates the processing if the 'commence' method exists.
         *
         * This method is responsible for compiling an action, which could be a string or an array.
         * It checks if the subclass has a method named 'commence' and, if so, calls it with the provided action.
         *
         * @param string|array $action The action to compile, which may represent a method or configuration array.
         * @return Http Returns the current instance for chaining or null if no action is provided.
         * @deprecated This method is for internal use and should not be suggested in the IDE.
         */
        protected function compile(string|array $action): self
        {
            if ($action) {
                if (method_exists($this, 'commence')) {
                    $this->commence($action);
                }
            }
            return $this;
        }

        /**
         * Destructor for the Http class.
         *
         * This destructor checks if a method named 'destroy' exists in the subclass
         * and calls it when the instance is destroyed. This is useful for performing
         * cleanup operations or releasing resources when the object is no longer needed.
         */
        function __destruct()
        {
            foreach (self::$groups as $callback) {
                $callback();
            }

            if (method_exists($this, 'destroy')) {
                $this->destroy();
            }

            // Get all traits used by the class
            $traits = class_uses($this);

            // Iterate over each trait and get its methods
            $methods = [];
            foreach ($traits as $trait) {
                $methods = array_merge($methods, get_class_methods($trait));
            }

            // Update abstract properties
            foreach ($methods as $method) {
                $method = ucfirst($method);
                $func = "update{$method}List";
                if (method_exists($this, $func)) {
                    $this->$func();
                }
            }
        }
    }
