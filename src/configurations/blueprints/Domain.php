<?php

	namespace App\Routes\Configurations\Blueprints;

	use App\Routes\Scheme\Buffer;
	use App\Routes\Scheme\Pal;

	trait Domain
	{
		private array $domain = [];

		protected function RegisterDomain(string|array $domain): void
		{
			$this->domain[] = strtolower($domain);
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
			$domains = $this->getDomain();
			if ($domains && in_array(strtolower(Pal::baseClassName(get_called_class())), Pal::getRoutes('configurations'))) {
				foreach ($domains as $domain) {
					Buffer::register('domain', $domain);
				}
			}
		}

		protected function getDomain(): array
		{
			return $this->domain;
		}
	}