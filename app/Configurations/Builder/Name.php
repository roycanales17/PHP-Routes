<?php

	namespace Configurations\Builder;

	use Configurations\Blueprints\Controller;
	use Configurations\Blueprints\Domain;
	use Configurations\Blueprints\Group;
	use Configurations\Blueprints\Middleware;
	use Configurations\Blueprints\Name as BaseName;
	use Configurations\Blueprints\Prefix;
	use Configurations\Blueprints\Where;
	use Configurations\Config;

	class Name extends Config
	{
		private string $nameProperty;

		use BaseName {
			RegisterName as private name;
		}
		use Where {
			RegisterWhere as public where;
		}
		use Domain {
			RegisterDomain as public domain;
		}
		use Middleware {
			RegisterMiddleware as public middleware;
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

		function __construct(string $name)
		{
			$this->nameProperty = $name;
		}

		protected function register(): void
		{
			$this->RegisterName($this->nameProperty);
		}
	}