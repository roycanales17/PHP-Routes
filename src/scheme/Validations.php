<?php

	namespace App\Routes\Scheme;

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
		 * Assign middleware to the route.
		 *
		 * This method allows you to attach one or multiple middleware to the route. Middleware can be provided
		 * as a string (e.g., 'auth') or as an array. If a string middleware contains a colon, it is split into
		 * an array to handle parameters (e.g., 'auth:admin'). If the middleware is not a global function, the
		 * method will try to find it as a method within the currently fetched controller, allowing for controller-based
		 * middleware assignment.
		 *
		 * @param array $middlewares The middleware to be attached to the route. It can be a single middleware
		 *                                 as a string or an array of middleware.
		 * @return bool
		 */
		protected function validateMiddleware(array $middlewares): bool
		{
			foreach ($middlewares as $middleware) {
				$class = $middleware[0];
				$method = $middleware[1];
				$type = $middleware[2];

				$instance = ($type === 'method') ? new $class : $class;
				if (!($type === 'method' ? $instance->$method() : $instance::$method())) {
					return false;
				}
			}

			return true;
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
		protected function validateURI(string $uri, array $prefix = [], array|null &$params = []): bool
		{
			$matched = 0;
			$url = $_SERVER['REQUEST_URI'] ?? '';
			$uri = $this->URISlashes($uri, $prefix);
			$route_uri = $this->separateSubDirectories($uri);
			$route_url = $this->separateSubDirectories($url);

			if (count($route_uri) === count($route_url)) {
				foreach ($route_uri as $index => $directory) {
					if (isset($route_url[$index])) {
						if (preg_match('/^\{[^{}]+\}$/', $directory)) {
							$params[str_replace(['{', '}'], '', $directory)] = preg_replace('/\?.*/', '', $route_url[$index]);
							$matched++;
						} else {
							if (strtolower($directory) === strtolower(strstr($route_url[$index], '?', true) ?: $route_url[$index])) {
								$matched++;
							}
						}
					}
				}
			} else {
				$matched = -1;
			}

			return ($matched === count($route_uri));
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
			if (empty($uri)) {
				return '';
			}

			$prefixPath = $prefixes ? '/' . implode('/', $prefixes) : '';
			return $prefixPath . '/' . trim($uri, '/');
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
				return $value !== "";
			}));
		}
	}
