<?php

	namespace Configurations\Builder;

	use Configurations\Blueprints\Controller;
	use Configurations\Blueprints\Domain;
	use Configurations\Blueprints\Group;
	use Configurations\Blueprints\Middleware;
	use Configurations\Blueprints\Name;
	use Configurations\Blueprints\Prefix;
	use Configurations\Blueprints\Where as BaseWhere;
	use Configurations\Config;

	class Where extends Config
	{
		private string $whereKeyProperty;
		private string $whereValueProperty;

		use BaseWhere {
			RegisterWhere as private where;
		}
		use Name {
			RegisterName as public name;
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

		function __construct(string $key, string $expression)
		{
			$this->whereKeyProperty = $key;
			$this->whereValueProperty = $expression;
		}

		protected function register(): void
		{
			$this->RegisterWhere($this->whereKeyProperty, $this->whereValueProperty);
		}
	}