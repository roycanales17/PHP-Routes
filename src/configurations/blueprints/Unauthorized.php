<?php

	namespace App\Routes\Configurations\Blueprints;

	use App\Routes\Scheme\Buffer;
	use App\Routes\Scheme\Pal;
	use Closure;

	trait Unauthorized
	{
		private ?Closure $callback = null;

		protected function RegisterUnauthorized(?Closure $callback): self
		{
			if ($callback) {
				$this->callback = $callback;
			}

			return $this;
		}

		protected function DestroyUnauthorized(): void
		{
			if ($this->getUnauthorized()) {
				$unauthorized = Buffer::fetch('unauthorized');
				if ($unauthorized) {
					Buffer::replace('unauthorized', $unauthorized);
				}
			}
		}

		protected function SetupUnauthorized(): void
		{
			$domains = $this->getUnauthorized();
			if ($domains && in_array(strtolower(Pal::baseClassName(get_called_class())), Pal::getRoutes('configurations'))) {
				foreach ($domains as $domain) {
					Buffer::register('unauthorized', $domain);
				}
			}
		}

		protected function getUnauthorized(): ?Closure
		{
			return $this->callback;
		}
	}