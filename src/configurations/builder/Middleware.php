<?php

	namespace App\Routes\Configurations\Builder;

	use App\Routes\Configurations\Blueprints\Middleware as BaseMiddleware;
	use App\Routes\Configurations\Blueprints\Controller;
	use App\Routes\Configurations\Blueprints\Group;
	use App\Routes\Configurations\Blueprints\Prefix;
	use App\Routes\Configurations\Config;
	use App\Routes\Scheme\Buffer;

	class Middleware extends Config
	{
		private string|array $middlewareProperty;

		use Group;
		use BaseMiddleware {
			RegisterMiddleware as private middleware;
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
			if (is_string($this->middlewareProperty)) {
				$controller = $this?->GetControllerName();
				if ($controller) {
					$this->middlewareProperty = [$controller, $this->middlewareProperty];
				} else {
					$controller = Buffer::fetch('controller');
					if ($controller) {
						if ($controller = end($controller)) {
							$this->middlewareProperty = [$controller, $this->middlewareProperty];
						}
					}
				}
			}
			$this->RegisterMiddleware($this->middlewareProperty);
		}
	}