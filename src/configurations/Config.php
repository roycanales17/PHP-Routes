<?php

	namespace App\Routes\Configurations;

	use App\Routes\Scheme\Pal;

	abstract class Config
	{
		private function PerformConfigurations(string $action): void
		{
			$traits = class_uses(static::class);
			foreach ($traits as $provider) {
				$className = Pal::baseClassName($provider);
				$method = "$action$className";

				if (method_exists($this, $method)) {
					$this->$method();
				}
			}
		}

		function __destruct()
		{
			$this->register();
			$this->PerformConfigurations('Setup');
			array_map('call_user_func', $this?->getGroups() ?? []);
			$this->PerformConfigurations('Destroy');
		}
	}