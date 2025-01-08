<?php

	namespace App\Routes\Requests\Builder;

	use App\Routes\Configurations\Blueprints\Controller;
	use App\Routes\Configurations\Blueprints\Middleware;
	use App\Routes\Configurations\Blueprints\Name;
	use App\Routes\Configurations\Blueprints\Prefix;
	use App\Routes\Requests\Http;

	class Get extends Http
	{
		use Middleware {
			RegisterMiddleware as public middleware;
		}
		use Controller {
			RegisterController as public controller;
		}
		use Prefix {
			BasePrefix as public prefix;
		}
		use Name {
			BaseName as public name;
		}
	}