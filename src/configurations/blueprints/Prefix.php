<?php


	namespace App\Routes\Configurations\Blueprints;

	trait Prefix
	{
		private array $prefix = [];

		protected function BasePrefix(string $prefix): self
		{
			if($prefix)
				$this->prefix[] = trim($prefix, '/');

			return $this;
		}

		protected function getPrefix(): array
		{
			return $this->prefix;
		}
	}