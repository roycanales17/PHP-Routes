<?php

	namespace App\Routes\Configurations\Blueprints;

	trait Name
	{
		private string $name = '';

		protected function BaseName(string $routeName): self
		{
			$this->name = $routeName;
			return $this;
		}

		protected function getRouteName(): string
		{
			return $this->name;
		}
	}