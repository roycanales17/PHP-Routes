<?php

	use App\Routes\Route;

	/**
	 * Class auth
	 *
	 * This class is used for testing different authentication levels.
	 * It contains methods that simulate user roles and authentication checks.
	 */
	class auth
	{
		/**
		 * Checks if the user is authenticated.
		 *
		 * @return bool True if the user is authenticated, false otherwise.
		 */
		public function is_authenticated(): bool {
			return true;
		}

		/**
		 * Checks if the user is a guest.
		 *
		 * @return bool True if the user is a guest, false otherwise.
		 */
		public function is_guest(): bool {
			return true;
		}

		/**
		 * Checks if the user is an admin.
		 *
		 * @return bool True if the user is an admin, false otherwise.
		 */
		public function is_admin(): bool {
			return true;
		}
	}

	/**
	 * Class User
	 *
	 * This class contains methods that return different pages based on the user's role.
	 */
	class User {

		/**
		 * Returns the admin page content.
		 *
		 * @return string The content for the admin page.
		 */
		function admin_page(): string {
			return 'Hello Admin!';
		}

		/**
		 * Returns the guest page content.
		 *
		 * @return string The content for the guest page.
		 */
		function guest_page(): string {
			return 'Hello Guest!';
		}

		/**
		 * Returns the admin5 page content.
		 *
		 * @return string The content for the admin5 page.
		 */
		function admin5_page(): string {
			return 'Hello Admin 5!';
		}

		/**
		 * Returns the admin6 page content.
		 *
		 * @return string The content for the admin6 page.
		 */
		function admin6_page(): string {
			return 'Hello Admin 6!';
		}
	}

	Route::controller(User::class)->group(function () {
		Route::middleware([auth::class, 'is_authenticated'])->group(function () {

			Route::get('/profile', 'admin6_page');
		});
	});