<?php

	namespace Configurations\Builder;

	use Configurations\Blueprints\Controller;
	use Configurations\Blueprints\Domain;
	use Configurations\Blueprints\Group;
	use Configurations\Blueprints\Middleware as BaseMiddleware;
	use Configurations\Blueprints\Name;
	use Configurations\Blueprints\Prefix;
	use Configurations\Blueprints\Unauthorized;
	use Configurations\Blueprints\Where;
	use Configurations\Config;

	class Middleware extends Config
	{
		private string|array $middlewareProperty;

		use BaseMiddleware {
			RegisterMiddleware as private middleware;
		}
		use Unauthorized {
			RegisterUnauthorized as public unauthorized;
		}
		use Where {
			RegisterWhere as public where;
		}
		use Domain {
			RegisterDomain as public domain;
		}
		use Name {
			RegisterName as public name;
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