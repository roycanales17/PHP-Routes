<?php

    namespace App\Routing\Scheme\Blueprints;

    /**
     * Trait Reflections
     *
     * This trait provides functionality to perform actions based on class methods, functions, or closures,
     * resolving parameters and invoking them as needed. It uses reflection to dynamically inspect and call
     * methods or functions with the appropriate parameters.
     */
    trait Reflections
    {
        /**
         * Perform an action using a callable, which can be a method, function, or closure.
         *
         * @param string|array|\Closure $action The callable to invoke, can be a method array in the format [ClassName, 'methodName'],
         *                                       a function name as a string, or a closure.
         * @param bool $includeParams Whether to include user-defined parameters when resolving callable parameters. Defaults to true.
         * @return mixed The result of the invoked callable.
         * @throws \InvalidArgumentException|\ReflectionException If the action is invalid or if required constructor parameters are missing.
         */
        protected function performAction(string|array|\Closure $action, bool $includeParams = true): mixed
        {
            $paramsValue = [];

            // Get reflection for class method, function, or closure
            $reflection = $this->getReflection($action);

            if (is_array($action) && isset($action[2])) {
                // Use provided arguments from $action[2]
                $paramsValue = $action[2];
            } else {
                // Resolve parameters for the callable
                foreach ($reflection->getParameters() as $param) {
                    $type = $param->getType();
                    $typeName = $type?->getName();

                    // Instantiate class if it's type-hinted
                    if ($typeName && class_exists($typeName)) {
                        $paramsValue[] = new $typeName();
                    } else {
                        if ($includeParams) {
                            // Fetch value for scalar params (from some user-defined method)
                            $paramsValue[] = $this->getParams($param->getName());
                        } else {
                            // Handle primitive types and assign default values
                            $paramsValue[] = match ($typeName ?? 'default') {
                                'int', 'float' => 0,
                                'string'       => '',
                                'bool'         => false,
                                'array'        => [],
                                default        => null,
                            };
                        }
                    }
                }
            }

            // Call the action based on its type
            if ($reflection instanceof \ReflectionFunction) {
                // For closures or standalone functions
                return $reflection->invokeArgs($paramsValue);
            } elseif ($reflection instanceof \ReflectionMethod) {
                // Get the class reflection
                $classReflection = new \ReflectionClass($reflection->class);

                // Check if the class has a constructor
                $constructor = $classReflection->getConstructor();

                // If the constructor exists and requires parameters, return false
                if ($constructor && $constructor->getNumberOfRequiredParameters() > 0) {
                    throw new \InvalidArgumentException($reflection->getDeclaringClass()->getName() . '::' . $reflection->getName() . " requires construct params.");
                }

                // If no constructor params are required, create an instance of the class
                $instance = $classReflection->newInstance();  // Create an instance of the class

                // Proceed to invoke the method on the instance
                return $reflection->invokeArgs($instance, $paramsValue);
            }

            // Throw exception if action is invalid
            throw new \InvalidArgumentException("Invalid action provided [2]");
        }

        /**
         * Get reflection information for the provided callable (function or method).
         *
         * @param string|array|\Closure $functionOrClosureOrClassMethod The callable to inspect.
         * @return \ReflectionMethod|\ReflectionFunction The reflection object for the callable.
         * @throws \InvalidArgumentException If the provided callable is invalid.
         */
        private function getReflection(string|array|\Closure $functionOrClosureOrClassMethod): \ReflectionMethod|\ReflectionFunction
        {
            // If it's a string, we assume it's a procedural function
            if (is_string($functionOrClosureOrClassMethod) && function_exists($functionOrClosureOrClassMethod)) {
                return new \ReflectionFunction($functionOrClosureOrClassMethod);
            }

            // If it's an object, we assume it's a closure
            if (is_object($functionOrClosureOrClassMethod) && $functionOrClosureOrClassMethod instanceof \Closure) {
                return new \ReflectionFunction($functionOrClosureOrClassMethod);
            }

            // If it's an array, we assume it's a class method array [Class, 'method']
            if (is_array($functionOrClosureOrClassMethod) && count($functionOrClosureOrClassMethod) >= 2) {
                $class = $functionOrClosureOrClassMethod[0];
                $method = $functionOrClosureOrClassMethod[1];

                // Check if the method exists
                if (method_exists($class, $method)) {
                    return new \ReflectionMethod($class, $method); // Class method
                }
            }

            // Throw exception for invalid action
            throw new \InvalidArgumentException("Invalid action provided [1]");
        }
    }
