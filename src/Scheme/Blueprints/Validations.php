<?php

namespace App\Routing\Scheme\Blueprints;

    use ReflectionException;

    /**
     * Trait RouteValidation
     *
     * This trait provides methods for validating routing constraints, middleware, and URIs.
     * It is designed to be used in routing contexts where constraints and middleware checks are required
     * to ensure that incoming requests match the expected patterns and rules defined for the routes.
     */
    trait Validations
    {
        /**
         * The default prefix for routes.
         *
         * @var string
         */
        protected string $prefix = '';

        /**
         * Validate constraints against provided parameters.
         *
         * This method checks if the parameters meet the defined constraints. If the parameters
         * do not match the constraints, the validation fails.
         *
         * @param array $constraints An associative array where keys are parameter names and values are regex patterns.
         * @param array $params The parameters to validate against the constraints.
         * @return bool Returns true if all parameters match their respective constraints, otherwise false.
         */
        private function validateConstraint(array $constraints, array $params): bool
        {
            $data = $params; // Store the provided parameters
            foreach ($constraints as $key => $pattern) {
                $valueExists = array_key_exists($key, $data); // Check if the parameter exists
                $matchesPattern = $valueExists && preg_match('/^' . $pattern . '$/', $data[$key]); // Match the parameter against the pattern

                // Analyze constraints for logging or debugging
                if ($this->analyze()) {
                    self::$evaluation['constraints'][] = [
                        'key' => $key,
                        'pattern' => $pattern,
                        'value' => $valueExists ? $data[$key] : null,
                        'status' => intval($matchesPattern)
                    ];
                } elseif (!$matchesPattern) {
                    return false; // Validation fails if a parameter does not match
                }
            }
            return true;
        }

        /**
         * Validate the specified middleware.
         *
         * This method checks if the middleware actions can be performed. It tracks which middleware has already been performed
         * to avoid redundant checks.
         *
         * @param array $middlewares An array of middleware actions to validate.
         * @return bool Returns true if all middleware validations pass, otherwise false.
         * @throws ReflectionException
         */
        private function validateMiddlewares(array $middlewares): bool
        {
            $performed = []; // Track performed middleware actions
            foreach ($middlewares as $middleware) {
                $temp = is_string($middleware) ? $middleware : "$middleware[0]:$middleware[1]";

                // Skip if this middleware has already been performed
                if (in_array($temp, $performed, true)) {
                    continue;
                }

                $performed[] = $temp; // Add to performed list
                $status = $this->performAction($middleware, false); // Validate the middleware action

                // Analyze middleware actions for logging or debugging
                if ($this->analyze()) {
                    self::$evaluation['middlewares'][] = ['action' => $temp, 'status' => intval($status)];
                } else {
                    // Exit loop if failed
                    if (!intval($status)) {
                        return false;
                    }
                }
            }

            return true; // Return the overall status of middleware validation
        }

        /**
         * Validate the provided URI against the current request URI.
         *
         * This method checks if the requested URI matches the defined route URI, accounting for dynamic segments.
         *
         * @param string $uri The route URI to validate against the request URI.
         * @param array $prefix Optional prefixes to prepend to the URI for validation.
         * @return bool Returns true if the request URI matches the defined route URI, otherwise false.
         */
        private function validateURI(string $uri, array $prefix = []): bool
        {
            $matched = 0;
            $url = $_SERVER['REQUEST_URI'];
            $uri = $this->URISlashes($uri, $prefix);
            $route_uri = $this->separateSubDirectories($uri);
            $route_url = $this->separateSubDirectories($url);

            // Compare the number of segments in route and request URIs
            if (count($route_uri) === count($route_url)) {
                foreach ($route_uri as $index => $directory) {
                    if (isset($route_url[$index])) {
                        // Match dynamic parameters
                        if (preg_match('/^\{[^{}]+\}$/', $directory)) {
                            $this->params[str_replace(['{', '}'], '', $directory)] = preg_replace('/\?.*/', '', $route_url[$index]);
                            $matched++;
                        } else {
                            // Match static segments, case-insensitive
                            if (strtolower($directory) === strtolower(strstr($route_url[$index], '?', true) ?: $route_url[$index])) {
                                $matched++;
                            }
                        }
                    }
                }
            } else {
                $matched = -1; // Mismatch in segment count
            }

            return ($matched === count($route_uri)); // Validate if all segments matched
        }

        /**
         * Ensure the URI has the correct leading and trailing slashes.
         *
         * This method normalizes the URI by ensuring it starts and ends with a slash,
         * and appends the base prefix if necessary.
         *
         * @param string|null $uri The URI to normalize.
         * @param array $prefixes Optional prefixes to prepend to the URI.
         * @return string Returns the normalized URI.
         */
        private function URISlashes(?string $uri, array $prefixes = []): string
        {
            if ($uri === null || $uri === '') {
                return ''; // Return empty if the URI is null or empty
            }

            if ($uri[0] !== '/') {
                $uri = '/' . $uri; // Ensure URI starts with a slash
            }

            if ($uri[-1] !== '/') {
                $uri .= '/'; // Ensure URI ends with a slash
            }

            $uri = trim($uri, '/');
            $basePath = rtrim($this->prefix, '/');
            return $basePath . '/' . implode('/', $prefixes) . '/' . $uri;
        }

        /**
         * Separate a string into subdirectories.
         *
         * This method splits a URI string into its constituent segments, filtering out any empty segments.
         *
         * @param string|null $value The URI string to separate.
         * @return array Returns an array of non-empty segments.
         */
        private function separateSubDirectories(?string $value): array
        {
            return array_values(array_filter(explode('/', $value), function ($value) {
                return $value !== ""; // Filter out empty segments
            }));
        }
    }
