<?php

	namespace App\Routes\Configurations\Blueprints;

	use App\Routes\Scheme\Buffer;
	use App\Routes\Scheme\Pal;

	trait Controller
	{
		private string $controller = '';

		protected function baseController(string $className): self
		{
			if (class_exists($className))
				$this->controller = $className;

			return $this;
		}

		protected function arrayPopController(): void
		{
			$controllers = Buffer::fetch(strtolower(Pal::baseClassName(get_called_class())));
			array_pop($controllers);

			Buffer::replace('controller', $controllers);
		}

		protected function getControllerName(): string
		{
			return $this->controller;
		}
	}