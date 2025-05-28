<?php

	namespace App\Routes\Scheme;

	use InvalidArgumentException;
	use ReflectionException;
	use ReflectionMethod;

	class Pal
	{
		private static array $routes = [];
		private static string $prefix = '';
		private static string $domain = '';
		private static array $methodCache = [];

		public static function registerGlobalPrefix(string $prefix): void
		{
			self::$prefix = trim(str_replace('.', '/', $prefix), '/');
		}

		public static function registerGlobalDomain(string $domain): void
		{
			// Remove protocol and www
			$domain = preg_replace('#^https?://#', '', $domain);
			$domain = preg_replace('#^www\.#', '', $domain);
			$domain = explode('/', $domain)[0];

			if (!filter_var('http://' . $domain, FILTER_VALIDATE_URL) || !preg_match('/^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}$/i', $domain)) {
				throw new InvalidArgumentException("Invalid domain: {$domain}");
			}

			self::$domain = $domain;
		}

		public static function performPrivateMethod(object $instance, string $methodName, ...$params): ?object
		{
			$className = get_class($instance);
			$cacheKey = $className . '::' . $methodName;

			if (!isset(self::$methodCache[$cacheKey])) {
				if (!method_exists($instance, $methodName)) {
					return null;
				}
				$reflection = new ReflectionMethod($instance, $methodName);
				$reflection->setAccessible(true);
				self::$methodCache[$cacheKey] = $reflection;
			}

			return self::$methodCache[$cacheKey]->invoke($instance, ...$params);
		}

		public static function checkIfMethodIsStatic($className, $methodName): bool
		{
			static $cache = [];

			$cacheKey = $className . '::' . $methodName;

			if (!isset($cache[$cacheKey])) {
				try {
					$reflectionMethod = new ReflectionMethod($className, $methodName);
					$cache[$cacheKey] = $reflectionMethod->isStatic();
				} catch (ReflectionException $e) {
					$cache[$cacheKey] = false;
				}
			}

			return $cache[$cacheKey];
		}


		public static function getGlobalPrefix(): string
		{
			return self::$prefix;
		}

		public static function getGlobalDomain(): string
		{
			return self::$domain;
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