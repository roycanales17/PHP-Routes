# ROUTES - HTTP Handler

Easily manage HTTP requests in your PHP application using the `roy404/routes` bundle.

## Installation

Install the package using Composer:

```bash
composer require roy404/routes
```

---

## Route Feature Overview

The Route feature allows you to define and manage HTTP requests in a structured way. You can handle `GET`, `POST`, `PUT`, `PATCH`, `DELETE` requests, `group` routes, assign `middleware`, define `controllers`, and more.

---

## Available Methods

### HTTP Methods

- `Route::get(string $uri, string|array|Closure $action)` – Define a GET route.  
- `Route::post(string $uri, string|array|Closure $action)` – Define a POST route.  
- `Route::put(string $uri, string|array|Closure $action)` – Define a PUT route.  
- `Route::patch(string $uri, string|array|Closure $action)` – Define a PATCH route.  
- `Route::delete(string $uri, string|array|Closure $action)` – Define a DELETE route.  

### Route Configuration

- `Route::group(array $attributes, Closure $action)` – Group routes with shared configuration or middleware.  
- `Route::controller(string $className)` – Assign a controller for specific routes.  
- `Route::middleware(string|array $action)` – Assign middleware to routes.  
- `Route::prefix(string $prefix)` – Add a URI prefix for a group of routes.  
- `Route::name(string $name)` – Assign a name to a route.  
- `Route::domain(string|array $domain)` – Restrict a route to a specific domain.

---

## Examples

### 1. Group Routes

**Definition** <br>
`Route::group()` allows you to group multiple routes under shared configuration such as middleware, prefixes, domains, or naming conventions.

This is useful for organizing routes and avoiding repetitive configuration.

```php
Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', function () {
        echo 'Welcome to the Dashboard';
    });

    Route::get('/profile', function () {
        echo 'Your Profile';
    });
});
````

**How it works**
- All routes inside the group inherit the group’s attributes. 
- Common use cases include authentication, admin panels, and API versioning.

### 2. Controller

**Definition** <br>
`Route::controller()` assigns a controller class to a group of routes.
All routes within the group will automatically use the specified controller.

```php
Route::controller(HomeController::class)->group(function () {
    Route::get('/home', 'index');
});
````

**How it works**
- The string 'index' refers to the `index()` method on HomeController. 
- This keeps route files clean and moves logic into controllers.

### 3. Middleware

**Definition** <br>
`Route::middleware()` allows you to attach one or more middleware to a route or route group.
Middleware is executed before the route action and is commonly used for authentication, authorization, logging, or request validation.

```php
Route::middleware([Auth::class, 'isAuthenticated'])->group(function () {
    Route::get('/profile', function () {
        echo 'Your profile';
    })->middleware([Auth::class, 'isAuthenticated']);
});
```

**Handling Unauthorized Requests** <br>
If a middleware check fails, you may define an `unauthorized()` handler.
This callback is executed when access is denied, allowing you to customize the response (e.g., redirect, return JSON, or show an error page).

If failed, you can use `unauthorized` method to handle it.
```php
Route::middleware([Auth::class, 'isAuthenticated'])
    ->group(function () {
        Route::get('/profile', function () {
            echo 'Your profile';
        });
    })
    ->unauthorized(function () {
        // Handle unauthorized access here
        // Example: redirect to login page or return a 401 response
    });
```

**Key Notes**
- The unauthorized() callback is triggered when middleware validation fails.
- This is useful for centralized access control handling.
- Works seamlessly with route groups and individual routes.

**Route-Level Middleware** <br>
Middleware can also be applied directly to a single route instead of an entire group.
This is useful when only specific routes require protection or special processing.

```php
Route::get('/profile', function () {
    echo 'Your profile';
})->middleware([Auth::class, 'isAuthenticated']);
```

**How it works**
- The middleware is executed before the route’s action.
- If the middleware validation passes, the route action runs.
- If it fails, the request is blocked and handled as unauthorized.

### 4. Prefix

**Definition** <br>
`Route::prefix()` adds a URI prefix to all routes inside the group.
This is commonly used for admin panels, APIs, or versioned routes.

```php
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        echo 'Admin Dashboard';
    });
});
```

**Resulting URI**
```bash
/admin/dashboard
```

**How it works**
- The prefix is automatically prepended to every route in the group. 
- Can be combined with middleware and domain rules.


### 5. Name
**Definition**
`Route::name()` assigns a name to a route or a group of routes.
Named routes allow you to reference URLs without hardcoding paths.

```php
Route::name('user')->group(function () {
    Route::get('home', function () {
        echo 'Your home';
    })->name('home');

    Route::get('profile', function () {
        echo 'Your profile';
    })->name('profile');
});
```

**Generated Route Names**
```bash
user.home
user.profile
```

**How it works**
- Group names act as a prefix. 
- Route names are useful for URL generation and refactoring.

### 6. Domain

**Definition** <br>
`Route::domain()` restricts a group of routes to a specific domain or subdomain.
This is useful for multi-tenant, admin, or API-based architectures.

```php
Route::domain('admin.example.com')->group(function () {
    Route::get('/home', function () {
        echo 'Your home';
    });
});
```

**How it works** 
- Routes inside the group will only respond to the specified domain. 
- Can be combined with prefixes and middleware. 
- Ideal for separating admin and public interfaces.

---

## Getting Started
This section walks you through setting up the routing system and defining your first routes.

### 1. Configure Routes
The `Route::configure()` method initializes the routing system.
It tells the router where your project lives, which route files to load, and optionally applies global configuration such as prefixes, domains, and middleware.

```php
<?php
require 'vendor/autoload.php';

use App\Routes\Route;

Route::configure(
    __DIR__,               // Project root directory
    ['routes/web.php'],    // Route definition files
)->routes(function (array $routes) {
    /**
     * This callback receives all registered routes.
     * Useful for debugging, inspecting route metadata,
     * or generating links from named routes.
     */
    echo '<pre>';
    print_r($routes);
    echo '</pre>';
})->captured(function (mixed $content, int $code, string $type) {
    /**
     * This callback handles the final response output.
     * You can customize headers, status codes,
     * or response formatting here.
     */
    http_response_code($code);
    header('Content-Type: ' . $type);
    echo $content;
});
```

`Route::configure()` Parameters:
```php
Route::configure(
    string $root,
    array  $routes,
    string $prefix = '',
    string $domain = '',
    array  $middleware = []
)
```

| Parameter     | Description                                            |
| ------------- | ------------------------------------------------------ |
| `$root`       | Base directory of your application (usually `__DIR__`) |
| `$routes`     | Array of route files to load (e.g. `routes/web.php`)   |
| `$prefix`     | Optional global URI prefix applied to all routes       |
| `$domain`     | Optional domain restriction for all routes             |
| `$middleware` | Global middleware applied to every route               |


**Example: Global Prefix & Middleware**
```php
Route::configure(
    __DIR__,
    ['routes/web.php'],
    prefix: 'api',
    middleware: ['auth']
);
```

### 2. Define Routes
Routes are defined inside the route files you passed to `Route::configure()`.

Example: `routes/web.php`
```php
<?php

use App\Routes\Route;

Route::get('/', function () {
    echo 'Hello World!';
});
```

**Using Controllers**
```php
Route::controller(HomeController::class)->group(function () {
    Route::get('/home', 'index');
    Route::get('/about', 'about');
});
```

**Using Route Names**
```php
Route::get('/dashboard', function () {
    echo 'Dashboard';
})->name('dashboard');
```

You can later reference it as:
```php
$link = Route::link('dashboard');
```

### 3. How the Request Lifecycle Works
1.  Web server redirects all requests to index.php
2.  Route::configure() loads all route definitions
3.  The router matches the current request URI and HTTP method 
4. Middleware is executed (if any)
5. The route action is executed 
6. The response is passed to the captured() callback


### 4. Minimal Setup Example
This is the simplest way to get your application up and running using the routing system.

**Note**: Make sure your web server is properly configured (Apache `.htaccess` or Nginx) to route all requests through a single entry file.

`index.php`
```php
<?php
require 'vendor/autoload.php';

use App\Routes\Route;

// Configure the router and handle the response output
Route::configure(__DIR__, ['routes/web.php'])
    ->captured(fn ($content) => print $content);
```

`routes/web.php`
```php
<?php

use App\Routes\Route;

// Define a basic route
Route::get('/', fn () => 'Hello World');
```

### Running the Project
You can use PHP’s built-in development server to run the application locally:
```bash
 php -S localhost:8000
```
Then open your browser and visit:
```php
http://localhost:8000
```
You should see:
```php
Hello World
```

**Notes**
- This setup is ideal for quick testing and local development.
- For production environments, make sure to configure Apache or Nginx with URL rewriting (see Server Configuration section).
- All requests are routed through index.php, allowing the router to properly match and dispatch routes.

---

## Server Configuration

### Apache (.htaccess)

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On

    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d

    RewriteRule ^(.*)$ index.php/$1 [L] 
</IfModule>
```

### Nginx

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```