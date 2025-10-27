<?php

	namespace Configurations\Blueprints;

	use Exception;
	use Scheme\Buffer;
	use Scheme\Pal;

	trait Controller
	{
		private string $controller = '';

		protected function RegisterController(string $className): self
		{
			if (!class_exists($className))
				throw new Exception("Controller class $className does not exist.");

			$this->controller = $className;
			return $this;
		}

		protected function DestroyController(): void
		{
			if ($this->GetControllerName()) {
				$controllers = Buffer::fetch('controller');
				if ($controllers) {
					array_pop($controllers);
					Buffer::replace('controller', $controllers);
				}
			}
		}

		protected function SetupController(): void
		{
			$className = $this->GetControllerName();
			if ($className && in_array(strtolower(Pal::baseClassName(get_called_class())), Pal::getRoutes('configurations'))) {
				Buffer::register('controller', $className);
			}
		}

		protected function GetControllerName(): string
		{
			return $this->controller;
		}
	}