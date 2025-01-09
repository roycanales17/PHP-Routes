<?php

	namespace App\Routes\Requests;

	use App\Routes\Route;
	use App\Routes\Scheme\Buffer;
	use App\Routes\Scheme\Pal;
	use App\Routes\Scheme\Reflections;
	use App\Routes\Scheme\Validations;
	use Closure;

	abstract class Http
	{
		use Validations;
		use Reflections;

		protected string $uri = '';
		protected array $middlewares = [];
		protected string|array|Closure $actions = [];

		public function __construct(string $uri, mixed $actions)
		{
			$this->actions = $actions;
			$this->uri = $uri;
		}

		private function getActivePrefix(): array
		{
			$globalPrefix = Buffer::fetch('prefix') ?? [];
			$prefix = $this?->getPrefix() ?? [];

			return array_merge($globalPrefix, $prefix);
		}

		private function setupRouteMiddleware(): void
		{
			$controller = $this?->GetControllerName() ?? '';
			$middlewares = $this?->GetMiddlewares() ?? [];

			if (!$controller) {
				$controllers = Buffer::fetch('controller');
				if ($controllers)
					$controller = end($controllers);
			}

			if ($globalMiddlewares = Buffer::fetch('middleware'))
				$middlewares = array_merge($globalMiddlewares, $middlewares);

			foreach ($middlewares as $middleware) {
				if (is_string($middleware)) {
					if (str_contains($middleware, '::') || str_contains($middleware, '@')) {
						if (str_contains($middleware, '@')) {

							$middleware = explode('@', $middleware);
							$middleware[] = 'method';
						} else {

							$middleware = explode('::', $middleware);
							$middleware[] = 'static';
						}

						$class = $middleware[0];
						$method = $middleware[1];

						if (method_exists($class, $method)) {
							$this->middlewares[] = $middleware;
						} else {
							throw new \Exception("Invalid middleware, method not exist: $method");
						}

					} else {

						if ($controller && method_exists($controller, $middleware)) {
							$this->middlewares[] = [$controller, $middleware, 'method'];
						} else {
							throw new \Exception("Invalid middleware, method/controller not exist: ". json_encode([$controller, $middleware]));
						}
					}
				} else {
					if (count($middleware) == 2) {

						$class = $middleware[0];
						$method = $middleware[1];

						if (method_exists($class, $method)) {
							$this->middlewares[] = [$class, $method, 'method'];
						} else {
							throw new \Exception("Invalid middleware, method not exist: [$method]");
						}
					} else {
						throw new \Exception("Invalid middleware actions: ". json_encode($middleware));
					}
				}
			}
		}

		private function setupRouteAction(): void
		{
			$controller = $this?->GetControllerName();

			if (is_string($this->actions)) {
				if ($controller) {
					$this->actions = [$controller, $this->actions];
				} else {
					if ($controllers = Buffer::fetch('controller')) {
						if ($controller = end($controllers)) {
							$this->actions = [$controller, $this->actions];
						}
					}
				}
			}
		}

		private function setupRouteName(array $prefix): void
		{
			$routeName = $this?->getRouteName();

			if ($routeName)
				Pal::registerRouteName($routeName, $this->URISlashes($this->uri, $prefix));
		}

		private function capture(Closure $closure, int $code = 200, string $type = 'text/html'): void
		{
			ob_start(); $closure();
			Route::register(ob_get_clean(), $code, $type);
		}

		public function __destruct()
		{
			$this->setupRouteName($prefixes = $this->getActivePrefix());
			$this->setupRouteAction();
			$this->setupRouteMiddleware();

			if ($this->validateURI($this->uri, $prefixes, $params)) {

				if (!Pal::requestMethod(Pal::baseClassName(get_called_class())))
					return;

				if (!$this->validateMiddleware($this->middlewares)) {
					$this->capture(function () {
						echo(json_encode(['message' => 'Unauthorized']));
					}, 401, 'application/json');
					return;
				}

				$this->capture(function () {
					echo $this->performAction($this->actions, $params ?? []);
				});
			}
		}
	}