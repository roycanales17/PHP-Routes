<?php

	namespace Configurations\Blueprints;

	use Closure;
	use Scheme\Buffer;
	use Scheme\Pal;

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