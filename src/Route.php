<?php

	namespace App\Routes;

	use App\Routes\Scheme\Facade;
	use App\Routes\Requests\Builder\{Delete, Get, Patch, Post, Put};
	use App\Routes\Configurations\Builder\{Controller, Group, Middleware, Prefix, Name};

	/**
	 * Class Route
	 *
	 * This class extends the Facade and provides static methods for route registration and configuration.
	 * It allows defining routes for different HTTP methods (GET, POST, PUT, PATCH, DELETE) as well as
	 * additional configuration options like controllers, middleware, and prefixes.
	 *
	 * @method static Put put(string $uri, string|array|\Closure $action = []) Defines a PUT route.
	 * @method static Patch patch(string $uri, string|array|\Closure $action = []) Defines a PATCH route.
	 * @method static Delete delete(string $uri, string|array|\Closure $action = []) Defines a DELETE route.
	 * @method static Get get(string $uri, string|array|\Closure $action = []) Defines a GET route.
	 * @method static Post post(string $uri, string|array|\Closure $action = []) Defines a POST route.
	 * @method static Group group(array $routes, \Closure $action) Registers a group of routes with shared configurations and middleware, enhancing route organization and reusability.
	 * @method static Controller controller(string $className) Registers a controller.
	 * @method static Middleware middleware(string|array $action) Registers middleware for the route.
	 * @method static Prefix prefix(string $prefix) Adds a prefix to the route URI.
	 * @method static Name name(string $name)
	 */

	class Route extends Facade
	{
		public static function configure(string $root, array $routesPath): self {
			return new static(routes: $routesPath, root: $root);
		}
	}