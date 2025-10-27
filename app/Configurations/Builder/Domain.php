<?php

	namespace Configurations\Builder;

	use Configurations\Blueprints\Controller;
	use Configurations\Blueprints\Domain as BaseDomain;
	use Configurations\Blueprints\Group;
	use Configurations\Blueprints\Middleware;
	use Configurations\Blueprints\Name;
	use Configurations\Blueprints\Prefix;
	use Configurations\Blueprints\Where;
	use Configurations\Config;

	class Domain extends Config
	{
		private string|array $domainProperty;

		use BaseDomain {
			RegisterDomain as private domain;
		}
		use Where {
			RegisterWhere as public where;
		}
		use Controller {
			RegisterController as public controller;
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

		function __construct(string|array $domain)
		{
			$this->domainProperty = $domain;
		}

		protected function register(): void
		{
			$this->RegisterDomain($this->domainProperty);
		}
	}