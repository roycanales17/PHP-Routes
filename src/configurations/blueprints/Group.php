<?php

	namespace App\Routes\Configurations\Blueprints;

	use Closure;

	trait Group
	{
		private array $groups = [];

		public function group(Closure $callback): self
		{
			$this->groups[] = $callback;
			return $this;
		}

		protected function getGroups(): array
		{
			return $this->groups;
		}
	}