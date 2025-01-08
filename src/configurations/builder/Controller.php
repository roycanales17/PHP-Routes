<?php

	namespace App\Routes\Configurations\Builder;

	use App\Routes\Configurations\Config;
	use App\Routes\Configurations\Blueprints\Group;
	use App\Routes\Configurations\Blueprints\Middleware;
	use App\Routes\Configurations\Blueprints\Controller as BaseController;

	class Controller extends Config
	{
		use Group;
		use BaseController {
			baseController as private controller;
		}
		use Middleware {
			BaseMiddleware as public middleware;
		}

		private string $classNameProperty;

		function __construct(string $className)
		{
			$this->classNameProperty = $className;
		}

		protected function perform(): void
		{
			$this->baseController($this->classNameProperty);
		}
	}