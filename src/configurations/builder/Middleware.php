<?php

	namespace App\Routes\Configurations\Builder;

	use App\Routes\Configurations\Blueprints\Middleware as BaseMiddleware;
	use App\Routes\Configurations\Blueprints\Controller;
	use App\Routes\Configurations\Blueprints\Group;
	use App\Routes\Configurations\Blueprints\Prefix;
	use App\Routes\Configurations\Config;

	class Middleware extends Config
	{
		private string|array $middlewareProperty;

		use BaseMiddleware {
			RegisterMiddleware as private middleware;
		}
		use Group {
			RegisterGroup as public group;
		}
		use Prefix {
			RegisterPrefix as public prefix;
		}
		use Controller {
			RegisterController as public controller;
		}

		function __construct(string|array $middleware)
		{
			$this->middlewareProperty = $middleware;
		}

		protected function register(): void
		{
			$this->RegisterMiddleware($this->middlewareProperty);
		}
	}