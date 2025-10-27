<?php

	namespace Requests\Builder;

	use Configurations\Blueprints\Controller;
	use Configurations\Blueprints\Domain;
	use Configurations\Blueprints\Middleware;
	use Configurations\Blueprints\Name;
	use Configurations\Blueprints\Prefix;
	use Configurations\Blueprints\Where;
	use Requests\Http;

	class Delete extends Http
	{
		use Where {
			RegisterWhere as public where;
		}
		use Middleware {
			RegisterMiddleware as public middleware;
		}
		use Controller {
			RegisterController as public controller;
		}
		use Prefix {
			RegisterPrefix as public prefix;
		}
		use Name {
			RegisterName as public name;
		}
		use Domain {
			RegisterDomain as public domain;
		}
	}