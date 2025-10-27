<?php

	namespace Configurations\Builder;

	use Configurations\Blueprints\Controller;
	use Configurations\Blueprints\Domain;
	use Configurations\Blueprints\Group;
	use Configurations\Blueprints\Middleware;
	use Configurations\Blueprints\Name;
	use Configurations\Blueprints\Prefix as BasePrefix;
	use Configurations\Blueprints\Where;
	use Configurations\Config;

	class Prefix extends Config
	{
		private string $prefixProperty;

		use BasePrefix {
			RegisterPrefix as private prefix;
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
		use Controller {
			RegisterController as public controller;
		}
		use Middleware {
			RegisterMiddleware as public middleware;
		}

		function __construct(string $prefix)
		{
			$this->prefixProperty = $prefix;
		}

		protected function register(): void
		{
			$this->RegisterPrefix($this->prefixProperty);
		}
	}