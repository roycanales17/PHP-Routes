<?php


	namespace App\Routes\Configurations\Blueprints;

	use App\Routes\Scheme\Buffer;
	use App\Routes\Scheme\Pal;

	trait Middleware
	{
		private array $middleware = [];

		protected function RegisterMiddleware(string|array $middleware): self
		{
			if ($middleware)
				$this->middleware[] = $middleware;

			return $this;
		}

		protected function DestroyMiddleware(): void
		{
			$middlewares = $this->GetMiddlewares();
			for ($i = 0; $i < count($middlewares); $i++) {
				$middlewares_r = Buffer::fetch(strtolower(Pal::baseClassName(get_called_class())));
				array_pop($middlewares_r);
				Buffer::replace('middleware', $middlewares_r);
			}
		}

		protected function SetupMiddleware(): void
		{
			$middlewares = $this->GetMiddlewares();
			foreach ($middlewares as $middleware) {
				if (is_string($middleware)) {
					$controllers = Buffer::fetch('controller');

					if ($controllers) {
						$controller = end($controllers);
						$middleware = [$controller, $middleware];
					}
				}

				if ($middleware && in_array(strtolower(Pal::baseClassName(get_called_class())), Pal::getRoutes('configurations')))
					Buffer::register('middleware', $middleware);
			}
		}

		protected function GetMiddlewares(): array
		{
			return $this->middleware;
		}
	}