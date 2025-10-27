<?php

	namespace Configurations\Builder;

	use Configurations\Blueprints\Controller as BaseController;
	use Configurations\Blueprints\Domain;
	use Configurations\Blueprints\Group;
	use Configurations\Blueprints\Middleware;
	use Configurations\Blueprints\Name;
	use Configurations\Blueprints\Prefix;
	use Configurations\Blueprints\Where;
	use Configurations\Config;

	class Controller extends Config
	{
		private string $classNameProperty;

		use BaseController {
			RegisterController as private controller;
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