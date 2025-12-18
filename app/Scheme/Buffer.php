<?php

	namespace App\Routes\Scheme;

	/**
	 * Class Buffer
	 *
	 * A lightweight in-memory buffer used during route configuration
	 * and bootstrapping.
	 *
	 * This class provides two isolated storage mechanisms:
	 *
	 * 1. `$buffered`
	 *    - Used for accumulating multiple entries per configuration key.
	 *    - Acts like a stack/queue where values are appended.
	 *
	 * 2. `$storage`
	 *    - Used for storing a single value per configuration key.
	 *    - Acts like a simple key-value store.
	 *
	 * All data is stored statically and lives for the duration of the request.
	 */
	final class Buffer
	{
		/**
		 * Buffered collections grouped by configuration key.
		 *
		 * Example:
		 * [
		 *   'routes' => [ ... ],
		 *   'middleware' => [ ... ]
		 * ]
		 */
		private static array $buffered = [];

		/**
		 * Single-value storage grouped by configuration key.
		 *
		 * Example:
		 * [
		 *   'domain' => 'example.com',
		 *   'prefix' => '/api'
		 * ]
		 */
		private static array $storage = [];

		/**
		 * Register a value into the buffer for the given configuration key.
		 *
		 * Multiple calls with the same `$config` will append values
		 * to the existing buffer.
		 *
		 * @param string $config Configuration key.
		 * @param mixed  $data   Data to buffer.
		 */
		public static function register(string $config, mixed $data): void
		{
			if (!(self::$buffered[$config] ?? [])) {
				self::$buffered[$config] = [];
			}

			self::$buffered[$config][] = $data;
		}

		/**
		 * Fetch buffered data for a configuration key.
		 *
		 * @param string $config Configuration key.
		 * @return mixed Returns buffered data or null if not found.
		 */
		public static function fetch(string $config): mixed
		{
			return self::$buffered[$config] ?? null;
		}

		/**
		 * Replace buffered data with a configuration key.
		 *
		 * This will overwrite any previously buffered values.
		 *
		 * @param string $config Configuration key.
		 * @param mixed  $data   New buffered data.
		 */
		public static function replace(string $config, mixed $data): void
		{
			self::$buffered[$config] = $data;
		}

		/**
		 * Retrieve all buffered data.
		 *
		 * @return array<string, mixed>
		 */
		public static function all(): array
		{
			return self::$buffered;
		}

		/**
		 * Store a single value for a configuration key.
		 *
		 * Unlike `register()`, this does not accumulate values.
		 *
		 * @param string $config Configuration key.
		 * @param mixed  $data   Data to store.
		 */
		public static function set(string $config, mixed $data): void
		{
			self::$storage[$config] = $data;
		}

		/**
		 * Retrieve a stored value by configuration key.
		 *
		 * @param string $config Configuration key.
		 * @return mixed Returns stored data or null if not found.
		 */
		public static function get(string $config): mixed
		{
			return self::$storage[$config] ?? null;
		}
	}
