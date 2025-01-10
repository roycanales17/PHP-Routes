<?php

	namespace App\Routes\Configurations\Blueprints;

	use App\Routes\Scheme\Buffer;
	use App\Routes\Scheme\Pal;

	trait Domain
	{
		private string $domain = '';

		protected function RegisterDomain(string $domain): void
		{
			$this->domain = strtolower($domain);
		}

		protected function DestroyDomain(): void
		{
			if ($this->getDomain()) {
				$domain = Buffer::fetch('domain');
				if ($domain) {
					array_pop($domain);
					Buffer::replace('domain', $domain);
				}
			}
		}

		protected function SetupDomain(): void
		{
			$domain = strtolower($this->getDomain());
			if ($domain && in_array(strtolower(Pal::baseClassName(get_called_class())), Pal::getRoutes('configurations'))) {
				Buffer::register('domain', $domain);
			}
		}

		protected function getDomain(): string
		{
			return $this->domain;
		}
	}