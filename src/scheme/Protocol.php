<?php

	namespace App\Routes\Scheme;

	use Closure;

	trait Protocol
	{
		private string $uri = '';
		private array $middlewares = [];
		private string|array|Closure $actions = [];
		private static bool $status = false;

		protected function registerURI(string $uri): void
		{
			$this->uri = $uri;
		}

		protected function registerAction(string|array|Closure $action): void
		{
			$this->actions = $action;
		}

		protected function registerMiddlewares(array $middleware): void
		{
			$this->middlewares = $middleware;
		}

		protected function toggleStatus(bool $found): void
		{
			self::$status = $found;
		}

		protected function getRouteStatus(): bool
		{
			return self::$status;
		}

		protected function getURI(): string
		{
			return $this->uri;
		}

		protected function getActions(): string|array|Closure
		{
			return $this->actions;
		}

		protected function fetchMiddlewares(): array
		{
			return $this->middlewares;
		}

		private function getActivePrefix(): array
		{
			$globalPrefix = Buffer::fetch('prefix') ?? [];
			$prefix = method_exists($this, 'getPrefix') ? $this->getPrefix() : [];

			return array_merge($globalPrefix, $prefix);
		}

		protected function registerRoutes($prefixes, $routeName): void
		{
			$action = $this->getActions();
			if (is_object($action)) {
				$action = 'Closure';
			}

			$prefix = '';
			if ($prefixes) {
				$prefix .= '/'. trim(implode('/', $prefixes), '/');
			}

			Buffer::register('routes', [
				'uri' => $prefix . '/' . ltrim($this->getURI(), '/'),
				'actions' => $action,
				'middlewares' => $this->fetchMiddlewares(),
				'name' => $routeName
			]);
		}
	}