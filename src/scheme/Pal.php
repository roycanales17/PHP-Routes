<?php

	namespace App\Routes\Scheme;

	use ReflectionException;
	use ReflectionMethod;

	class Pal
	{
		private static array $routes = [];
		private static array $routeNames = [];

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
			if (isset(self::$routes[$type]))
				return self::$routes[$type];

			$path = getcwd() . "/src/$type/builder";
			foreach (glob($path . '/*.php') as $file)
				self::$routes[$type][] = strtolower(pathinfo($file, PATHINFO_FILENAME));

			return self::$routes[$type] ?? [];
		}

		public static function createInstance(string $className, ...$params):? object
		{
			if (class_exists($className))
				return new $className(...$params);

			return null;
		}

		public static function registerRouteName(string $name, string $uri): void
		{
			self::$routeNames[$name] = $uri;
		}

		public static function fetchRoutesName(string $name = ''): array|string
		{
			if ($name) {
				return self::$routeNames[$name] ?? '';
			}

			return self::$routeNames;
		}

		public static function baseClassName(string $className): string
		{
			return basename(str_replace('\\', '/', $className));
		}

		public static function requestMethod(string $method = ''): string|bool
		{
			$request = strtoupper($_SERVER['REQUEST_METHOD']);
			if ($method) {
				return $request === strtoupper($method);
			}

			return $request;
		}
	}