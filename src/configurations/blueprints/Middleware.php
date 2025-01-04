<?php


	namespace App\Routes\Configurations\Blueprints;

	use App\Routes\Scheme\Buffer;
	use App\Routes\Scheme\Pal;

	trait Middleware
	{
		private array $middleware = [];

		protected function BaseMiddleware(string|array $middleware): self
		{
			if ($middleware)
				$this->middleware[] = $middleware;

			return $this;
		}

		protected function arrayPopMiddleware(): void
		{
			$middlewares = Buffer::fetch(strtolower(Pal::baseClassName(get_called_class())));
			array_pop($middlewares);

			Buffer::replace('middleware', $middlewares);
		}

		protected function getMiddlewares(): array
		{
			return $this->middleware;
		}
	}