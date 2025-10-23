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
			if ($this->GetUnauthorized()) {
				$unauthorized = Buffer::fetch('unauthorized');
				if ($unauthorized) {
					Buffer::replace('unauthorized', $unauthorized);
				}
			}
		}

		protected function SetupUnauthorized(): void
		{
			$unauthorized = $this->GetUnauthorized();
			if ($unauthorized && in_array(strtolower(Pal::baseClassName(get_called_class())), Pal::getRoutes('configurations'))) {
				Buffer::register('unauthorized', $unauthorized);
			}
		}

		protected function GetUnauthorized(): ?Closure
		{
			return $this->callback;
		}
	}