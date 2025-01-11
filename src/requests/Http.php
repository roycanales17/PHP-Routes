<?php

	namespace App\Routes\Requests;

	use App\Routes\Route;
	use App\Routes\Scheme\Buffer;
	use App\Routes\Scheme\Pal;
	use App\Routes\Scheme\Protocol;
	use App\Routes\Scheme\Reflections;
	use App\Routes\Scheme\Validations;
	use Closure;
	use ReflectionException;

	abstract class Http
	{
		use Protocol;
		use Validations;
		use Reflections;

		public function __construct(string $uri, mixed $actions)
		{
			$this->registerAction($actions);
			$this->registerURI($uri);
		}

		private function setupDomain(): void
		{
			$globalDomain = Buffer::fetch('domain') ?? [];
			$domains = method_exists($this, 'getDomain') ? $this->getDomain() : [];
			$allowedDomains = array_merge($globalDomain, $domains);

			if ($allowedDomains) {
				$this->registerDomainName($allowedDomains);
			}
		}

		private function setupRouteMiddleware(): void
		{
			$middlewares = method_exists($this, 'GetMiddlewares') ? $this->GetMiddlewares() : [];
			if ($globalMiddlewares = Buffer::fetch('middleware'))
				$middlewares = array_merge($globalMiddlewares, $middlewares);

			if ($middlewares) {
				$this->registerMiddlewares($middlewares);
			}
		}

		private function setupRouteParamsExpression(): void
		{
			$expressions = method_exists($this, 'getWhereExpression') ? $this->getWhereExpression() : [];
			$globalExpressions = Buffer::fetch('where') ?? [];

			$activeExpressions = array_merge($globalExpressions, $expressions);
			if ($activeExpressions) {
				$this->registerExpressions($activeExpressions);
			}
		}

		private function setupRouteAction(): void
		{
			if (is_string($this->getActions())) {
				$controller = method_exists($this, 'GetControllerName') ? $this->GetControllerName() : '';
				if ($controller) {
					$this->registerAction([$controller, $this->getActions()]);
				} else {
					if ($controllers = Buffer::fetch('controller')) {
						if ($controller = end($controllers)) {
							$this->registerAction([$controller, $this->getActions()]);
						}
					}
				}
			}
		}

		private function setupRouteName(string|null &$name = null): void
		{
			$name = '';
			$routeNames = Buffer::fetch('names') ?? [];
			$routeName = method_exists($this, 'getRouteName') ? $this->getRouteName() : '';

			if ($routeNames) {
				$name .= implode('.', $routeNames);
			}

			if ($routeName) {
				$name .= ( $routeNames ? '.' : '' ) . $routeName;
			}
		}

		private function capture(Closure|string $closure, int $code = 200, string $type = 'text/html'): void
		{
			ob_start();
			is_string($closure) ? print($closure) : $closure();

			Route::register(ob_get_clean(), $code, $type);
			$this->toggleStatus(true);
		}

		/**
		 * @throws ReflectionException
		 */
		public function __destruct()
		{
			$this->setupDomain();
			$this->setupRouteName($routeName);
			$this->setupRouteAction();
			$this->setupRouteMiddleware();
			$this->setupRouteParamsExpression();
			$this->registerRoutes($prefixes = $this->getActivePrefix(), $routeName);

			if (!$this->getRouteStatus() && $this->validateURI($this->getURI(), $prefixes, $params)) {

				if (!$this->validateParamsExpressions($this->getExpressions(), $params)) {
					$this->capture(json_encode(['message' => 'Bad Request']), 400, 'application/json');
					return;
				}

				if (!$this->validateDomain($this->getRequestDomain()))
					return;

				if (!$this->validateMethodRequest(Pal::baseClassName(get_called_class())))
					return;

				if (!$this->validateMiddleware($this->fetchMiddlewares())) {
					$this->capture(json_encode(['message' => 'Unauthorized']), 401, 'application/json');
					return;
				}

				$this->capture( function () {
					echo $this->performAction($this->getActions(), $params ?? []);
				});
			}
		}
	}