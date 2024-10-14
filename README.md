# ROUTES LIBRARY

Install the bundle using Composer:


# DOCUMENTATION

This PHP library provides an intuitive, object-oriented way to define and manage HTTP routes in your application. It supports various HTTP methods, route grouping, middleware, and custom route handling. **All routes can only be called statically**.

## Usage

1. **Define Routes:** Use the HTTP methods to define routes statically through the `Route` class.

   ```php
   use App\Routing\Route;
   use App\Routing\Controllers\UserController;

   // Define a GET route
   Route::get('/users', [UserController::class, 'index']);

   // Define a POST route
   Route::post('/users', [UserController::class, 'store']);

   // Define a PUT route
   Route::put('/users/{id}', [UserController::class, 'update']);

   // Define a DELETE route with array options
   Route::delete('/users/{id}', [UserController::class, 'destroy']);

   // Define a PATCH route
   Route::patch('/users/{id}', [UserController::class, 'patch']);

2. **Use Controllers:** Routes can point to methods within controllers for handling requests.

    ```php
   namespace App\Routing\Controllers;

    class UserController
    {
       public function index()
      {
         // Return a list of users
      }

      public function store()
      {
        // Create a new user
      }

      public function update($id)
      {
        // Update a user by ID
      }

      public function destroy($id)
      {
        // Delete a user by ID
      }

      public function patch($id)
      {
        // Partially update a user by ID
      }
    }   
   ```

3. **Implement Middleware:** Middleware can be applied to routes for additional request handling.

    ```PHP
   Route::get('/users', [UserController::class, 'index'])->middleware('auth');
    ```
   
4. **Route Prefixes:** Use route prefixes to group related routes under a common path.

    ```PHP
   Route::prefix('/admin')->group(function() {
       Route::get('/users', [AdminUserController::class, 'index']);
       Route::post('/users', [AdminUserController::class, 'store']);
   });
    ```
   
5. **Route Groups** Group routes to apply shared attributes such as middleware and prefixes.

    ```PHP
    Route::group(['middleware' => 'auth'], function() {
        Route::get('/users', [Api\UserController::class, 'index']);
        Route::post('/users', [Api\UserController::class, 'store']);
    });
    ```

## Available Methods

**Http Methods**
- `static get(string $uri, array $action)`: Defines a GET route.
- `static post(string $uri, array $action)`: Defines a POST route.
- `static put(string $uri, array $action)`: Defines a PUT route.
- `static patch(string $uri, array $action)`: Defines a PATCH route.
- `static delete(string $uri, array $action)`: Defines a DELETE route.

**Route Configuration**
- `static group(array $routes, \Closure $action)`: Registers a group of routes with shared configurations and middleware, enhancing route organization and reusability.
- `static controller(string $className)`: Registers a controller.
- `static middleware(string|array $action)`: Registers middleware for the route.
- `static prefix(string $prefix)`: Adds a prefix to the route URI.


## Getting Started

To set up routing in your application, ensure that your web server is configured to use URL rewriting. This allows your application to route requests properly. Below are the configurations for both Apache or Nginx servers.

### Apache

If you are using an Apache web server, add the following code to your `.htaccess` file located in your application's root directory:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On

    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d

    # Handle all other URLs
    RewriteRule ^(.*)$ web.php/$1 [L] # Recommended: index.php
</IfModule>
```

### Nginx
For Nginx, you will need to configure the server block in your Nginx configuration file (usually located in /etc/nginx/sites-available/default or a similar path). Add the following rules to handle URL rewriting:

```nginx
location / {
    try_files $uri $uri/ /web.php?$query_string; # Recommended: index.php
}
```