<?php

	namespace App\Routes\Scheme;

	trait Properties
	{
		private array $params;
		private string $method;
		private array $protocol = [
			'requests',
			'configurations'
		];

		private static string $root = '';
		private static mixed $content = '';
		private static array $routes = [];
		private static int $responseCode = 200;
		private static bool $resolved = false;

		protected function setParams(array $params): void
		{
			$this->params = $params;
		}

		protected function setMethod(string $method): void
		{
			$this->method = $method;
		}

		protected function setRoutes(array $routes): void
		{
			self::$routes = $routes;
		}

		protected function setRoot(string $root): void
		{
			if (file_exists($root)) {
				self::$root = $root;
			}
		}

		protected function setContent(string $content): void
		{
			self::$content = $content;
		}

		protected function setHttpCode(int $code): void
		{
			self::$responseCode = $code;
		}

		protected function getRoutes(): array
		{
			return self::$routes;
		}

		protected function getMethod(): string
		{
			return $this->method;
		}

		protected function getProtocols(): array
		{
			return $this->protocol;
		}

		protected function getRoot(): string
		{
			return self::$root;
		}

		protected function getContent(): string
		{
			return self::$content;
		}

		protected function getResponseCode(): int
		{
			return self::$responseCode;
		}

		protected function buildPath(string $path): string
		{
			return $this->getRoot() . "/". rtrim($path, '.php') . '.php';
		}

		protected function isResolved(): bool
		{
			return self::$resolved;
		}

		protected static function setStaticResolved(bool $opt): void
		{
			self::$resolved = $opt;
		}

		protected static function setStaticHttpCode(int $code): void
		{
			self::$responseCode = $code;
		}

		protected static function setStaticContent(string $content): void
		{
			self::$content = $content;
		}

		protected static function setStaticRoot(string $root): void
		{
			self::$root = $root;
		}
	}