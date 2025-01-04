<?php

	namespace App\Routes\Configurations\Builder;

	use App\Routes\Configurations\Blueprints\Middleware as BaseMiddleware;
	use App\Routes\Configurations\Blueprints\Controller;
	use App\Routes\Configurations\Blueprints\Group;
	use App\Routes\Configurations\Config;
	use App\Routes\Scheme\Buffer;

	class Middleware extends Config
	{
		use Group;
		use Controller {
			BaseController as public controller;
		}
		use BaseMiddleware {
			BaseMiddleware as private middleware;
		}

		private string|array $middlewareProperty;

		function __construct(string|array $middleware)
		{
			$this->middlewareProperty = $middleware;
		}

		protected function perform(): void
		{
			if (is_string($this->middlewareProperty)) {
				$controller = $this?->getControllerName();

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
			$this->BaseMiddleware($this->middlewareProperty);
		}
	}