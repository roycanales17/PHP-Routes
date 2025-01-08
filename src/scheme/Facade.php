<?php

	namespace App\Routes\Scheme;

	use Closure;
	use Exception;

	abstract class Facade
	{
		use Properties;

		/**
		 * Constructor for initializing the route handling class.
		 *
		 * @param string $method The HTTP method (e.g., GET, POST, PUT) associated with the route.
		 * @param array $params The parameters for the route.
		 * @param array $routes An array of routes to be registered.
		 * @param string $root The root path for the routes.
		 * @throws Exception
		 */
		function __construct(string $method = '', array $params = [], array $routes = [], string $root = '')
		{
			$this->setMethod($method);
			$this->setParams($params);

			if ($routes)
				$this->setRoutes($routes);

			if ($root)
				$this->setRoot($root);

			if ($routes)
				$this->loadRoutes();
		}

		/**
		 * Dynamically handles static method calls for route registration.
		 *
		 * @param string $name The HTTP method name (e.g., 'get', 'post', 'put', etc.).
		 * @param array $arguments The arguments passed, typically including the URI and action.
		 *
		 * @return object|null An instance of the registered route or null if no instance is found.
		 * @throws Exception
		 */
		public static function __callStatic(string $name, array $arguments):? object
		{
			return self::registerRoute([
				'method' => $name,
				'args' => $arguments
			]);
		}

		/**
		 * Register the new instance of route.
		 *
		 * @param array $args
		 * @return object|null
		 * @throws Exception
		 */
		protected static function registerRoute(array $args):? object
		{
			return Pal::performPrivateMethod(new static($args['method'], $args['args']), 'commence');
		}

		/**
		 * Executes the appropriate route logic based on the request method.
		 *
		 * This method determines whether the current HTTP method matches any of the registered routes
		 * in either the "requests" or "configurations" categories. If a match is found, it delegates
		 * the routing logic to the `performRoute` method.
		 *
		 * @return object|null Returns an object representing the route execution, or null if no route is matched.
		 */
		private function commence(): ?object
		{
			foreach ($this->getProtocols() as $protocol) {
				if (in_array($this->getMethod(), Pal::getRoutes($protocol))) {
					return $this->performRoute($this->getMethod(), $protocol);
				}
			}

			return null;
		}

		/**
		 * Loads and includes route files defined in the routes array.
		 *
		 * @throws Exception If a route file does not exist.
		 */
		private function loadRoutes(): void
		{
			foreach ($this->getRoutes() as $route) {
				$path = $this->buildPath($route);
				if (file_exists($path)) {
					require_once $path;
				} else {
					throw new Exception("[Route] File not exist: $path");
				}
			}

			if (!$this->isResolved()) {
				$this->setHttpCode(404);
				$this->setContent(json_encode(['message' => '404 Page']));
			}
		}

		/**
		 * Executes a closure with the current content and response code.
		 *
		 * @param Closure $closure The closure to be executed.
		 */
		public function captured(Closure $closure): void
		{
			$closure($this->getContent(), $this->getResponseCode());
		}

		/**
		 * Handles the execution of a route by creating an instance of the corresponding route builder.
		 *
		 * This method dynamically creates an instance of a route builder class based on the method
		 * and type (e.g., "requests" or "configurations"), passing the URI and action as parameters.
		 *
		 * @param string $method The HTTP method (e.g., GET, POST, etc.).
		 * @param string $type The type of route configuration (e.g., "requests" or "configurations").
		 *
		 * @return object The instance of the route builder responsible for handling the route.
		 */
		private function performRoute(string $method, string $type): object
		{
			return Pal::createInstance("App\\Routes\\$type\\Builder\\$method", $this->params[0] ?? '', $this->params[1] ?? []);
		}

		/**
		 * Registers the content and HTTP response code for the current route.
		 *
		 * @param mixed $content The content to be registered.
		 * @param int $code The HTTP response code to be set.
		 */
		public static function register(mixed $content, int $code): void
		{
			self::setStaticResolved(true);
			self::setStaticContent($content);
			self::setStaticHttpCode($code);
		}
	}