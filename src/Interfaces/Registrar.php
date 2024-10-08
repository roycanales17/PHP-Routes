<?php
	
	namespace App\Routing\Interfaces;
	
	interface Registrar
	{
		/**
		 * Add URI parameters constraint.
		 *
		 * This method allows you to specify constraints for the given URI parameters,
		 * which will be validated against the provided constraint when processing requests.
		 *
		 * @param array|string $params A single parameter or an array of parameters to apply the constraint.
		 * @param string $constraint (optional) A constraint that defines the acceptable format or value for the parameters.
		 * @return self Returns the current instance for method chaining.
		 */
		public function where( array|string $params, string $constraint = '' ): self;
		
		/**
		 * Add route middleware or guard.
		 *
		 * This method allows you to attach one or more middleware actions that will be executed
		 * before the main action is called. Middleware can be used for tasks such as authentication,
		 * logging, or modifying request/response data.
		 *
		 * @param array|string $actions A single action or an array of actions representing the middleware to apply.
		 * @return self Returns the current instance for method chaining.
		 */
		public function middleware( array|string $actions ): self;

        /**
         * Set a prefix for the route group.
         *
         * This method defines a prefix that will be applied to all routes within the group.
         * It is useful for organizing routes under a common path, such as adding a prefix
         * like 'admin' for routes that belong to an administrative section of the application.
         *
         * @param string $prefix The prefix to be added to the routes.
         * @return self Returns the current instance for method chaining.
         */
        public function prefix( string $prefix ): self;

        /**
         * Assign a name (alias) to the route.
         *
         * This method sets a unique name for the route, which can be referenced elsewhere
         * in the application. Route names are useful for generating URLs and for redirection,
         * allowing you to manage routes by their name rather than their URI.
         *
         * @param string $name The name (alias) to be assigned to the route.
         * @return self Returns the current instance for method chaining.
         */
        public function name( string $name ): self;
    }
