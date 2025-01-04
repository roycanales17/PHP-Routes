<?php

	namespace App\Routes\Scheme;

	class Buffer
	{
		private static array $buffered = [];

		public static function register(string $config, $data): void
		{
			if (!(self::$buffered[$config] ?? [])) {
				self::$buffered[$config] = [];
			}

			self::$buffered[$config][] = $data;
		}

		public static function fetch(string $config): mixed
		{
			return self::$buffered[$config] ?? null;
		}

		public static function replace(string $config, $data): void
		{
			self::$buffered[$config] = $data;
		}
	}