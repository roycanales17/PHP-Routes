<?php

	namespace App\Routes\Configurations\Builder;

	use App\Routes\Configurations\Config;
	use App\Routes\Configurations\Blueprints\Group;
	use App\Routes\Configurations\Blueprints\Middleware;
	use App\Routes\Configurations\Blueprints\Controller as BaseController;

	class Controller extends Config
	{
		private string $classNameProperty;

		use Group;
		use BaseController {
			RegisterController as private controller;
		}
		use Middleware {
			RegisterMiddleware as public middleware;
		}

		function __construct(string $className)
		{
			$this->classNameProperty = $className;
		}

		protected function register(): void
		{
			$this->RegisterController($this->classNameProperty);
		}
	}