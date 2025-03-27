<?php

	namespace App\Routes\Scheme;

	use ReflectionException;
	use ReflectionMethod;

	class Pal
	{
		private static array $routes = [];

		public static function performPrivateMethod(object $instance, string $methodName, ...$params):? object
		{
			if (method_exists($instance, $methodName)) {
				$reflection = new ReflectionMethod($instance, $methodName);
				$reflection->setAccessible(true);
				return $reflection->invoke($instance, $params);
			}

			return null;
		}

		public static function checkIfMethodIsStatic($className, $methodName): bool {
			try {
				$reflectionMethod = new ReflectionMethod($className, $methodName);
				return $reflectionMethod->isStatic();
			} catch (ReflectionException $e) {
				return false;
			}
		}

		public static function getRoutes(string $type): array
		{
			if (isset(self::$routes[$type])) {
				return self::$routes[$type];
			}

			$baseDir = str_contains(__DIR__, '/vendor/')
				? dirname(__DIR__)
				: getcwd() . "/src";

			$path = $baseDir . "/$type/builder";

			if (is_dir($path)) {
				foreach (scandir($path) as $file) {
					if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
						self::$routes[$type][] = strtolower(pathinfo($file, PATHINFO_FILENAME));
					}
				}
			}

			return self::$routes[$type] ?? [];
		}

		public static function createInstance(string $className, ...$params):? object
		{
			if (class_exists($className))
				return new $className(...$params);

			return null;
		}

		public static function baseClassName(string $className): string
		{
			return basename(str_replace('\\', '/', $className));
		}
	}