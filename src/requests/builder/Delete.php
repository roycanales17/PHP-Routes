<?php

	namespace App\Routes\Requests\Builder;
	use App\Routes\Configurations\Blueprints\Controller;
	use App\Routes\Configurations\Blueprints\Middleware;
	use App\Routes\Configurations\Blueprints\Name;
	use App\Routes\Configurations\Blueprints\Prefix;
	use App\Routes\Requests\Http;

	class Delete extends Http
	{
		use Middleware {
			baseMiddleware as public middleware;
		}
		use Controller {
			baseController as public controller;
		}
		use Prefix {
			BasePrefix as public prefix;
		}
		use Name {
			BaseName as public name;
		}
	}