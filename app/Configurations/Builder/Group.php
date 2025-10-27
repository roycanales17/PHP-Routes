<?php

	namespace Configurations\Builder;

	use Closure;
	use Configurations\Blueprints\Controller;
	use Configurations\Blueprints\Domain;
	use Configurations\Blueprints\Group as BaseGroup;
	use Configurations\Blueprints\Middleware;
	use Configurations\Blueprints\Name;
	use Configurations\Blueprints\Prefix;
	use Configurations\Blueprints\Where;
	use Configurations\Config;
	use Scheme\Pal;

	class Group extends Config
	{
		use BaseGroup {
			RegisterGroup as private group;
		}
		use Name;
		use Controller;
		use Middleware;
		use Prefix;
		use Domain;
		use Where;

		private array $groupAttributesProperty;
		private static array $traits = [];

		function __construct(array $attributes, Closure $action)
		{
			$this->groupAttributesProperty = $attributes;
			$this->group($action);
		}

		private function getConfigurations(): array
		{
			if (self::$traits)
				return self::$traits;

			return self::$traits = class_uses(static::class);
		}

		protected function register(): void
		{
			$configurationsNames = [];
			foreach ($this->getConfigurations() as $class) {
				$configurationsNames[] = strtolower(Pal::baseClassName($class));
			}

			foreach ($this->groupAttributesProperty as $config => $action) {
				if (in_array(strtolower($config), $configurationsNames)) {
					$method = "Register$config";
					if (method_exists($this, $method)) {
						$this->$method($action);
					}
				}
			}
		}
	}