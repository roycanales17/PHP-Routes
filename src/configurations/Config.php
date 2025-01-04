<?php

	namespace App\Routes\Configurations;

	use App\Routes\Scheme\Buffer;
	use App\Routes\Scheme\Pal;

	abstract class Config
	{
		protected function setupController(): void
		{
			$className = $this?->getControllerName();
			if (in_array(strtolower(Pal::baseClassName(static::class)), Pal::getRoutes('configurations')))
				Buffer::register('controller', $className);
		}

		protected function setupMiddleware(): void
		{
			$middlewares = $this?->getMiddlewares();
			foreach ($middlewares as $middleware) {
				if (is_string($middleware)) {
					$controllers = Buffer::fetch('controller');

					if ($controllers) {
						$controller = end($controllers);
						$middleware = [$controller, $middleware];
					}
				}

				if (in_array(strtolower(Pal::baseClassName(static::class)), Pal::getRoutes('configurations')))
					Buffer::register('middleware', $middleware);
			}
		}

		function __destruct()
		{
			$this->perform();
			$this->setupController();
			$this->setupMiddleware();

			foreach ($this?->getGroups() as $group)
				call_user_func($group);

			$controller = $this?->getControllerName();
			$middlewares = $this?->getMiddlewares();

			if ($controller)
				$this?->arrayPopController();

			for ($i = 0; $i < count($middlewares); $i++)
				$this?->arrayPopMiddleware();
		}
	}