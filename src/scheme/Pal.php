<?php

	namespace App\Routes\Scheme;

	use ReflectionMethod;
	use SimpleXMLElement;

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

		public static function display(mixed $content = '', int $code = 200, bool $exit = false, $type = 'html'|'json'|'text'|'xml', bool $return = false )
		{
			if ($return)
				ob_start();

			http_response_code($code);
			switch ($type) {
				case 'json':
					header('Content-Type: application/json');
					echo(json_encode($content));
					break;

				case 'text':
					header('Content-Type: text/plain');
					echo($content);
					break;

				case 'xml':
					header('Content-Type: application/xml');
					$xml = new SimpleXMLElement('<root/>');
					array_walk_recursive($content, function($value, $key) use ($xml) {
						$xml->addChild($key, $value);
					});
					echo($xml->asXML());
					break;

				default:
					header('Content-Type: text/html');
					echo !is_string($content) ? json_encode($content) : $content;
					break;
			}

			if ($return)
				return ob_get_clean();

			if ($exit)
				exit;

			return null;
		}
	}