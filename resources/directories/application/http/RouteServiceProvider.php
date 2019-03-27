<?php

namespace StubNamespace\Http;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;

class RouteServiceProvider extends ServiceProvider
{
	/**
	 * This namespace is applied to your controller routes.
	 *
	 * In addition, it is set as the URL generator's root namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'StubNamespace\Controllers';

	/**
	 * Define your route model bindings, pattern filters, etc.
	 *
	 * @return void
	 */
	public function boot()
	{
		//

		parent::boot();
	}

	/**
	 * Define the routes for the application.
	 *
	 * @return void
	 */
	public function map()
	{
		$this->mapApiRoutes();

		$this->mapWebRoutes();

		//
	}

	/**
	 * Define the "web" routes for the application.
	 *
	 * These routes all receive session state, CSRF protection, etc.
	 *
	 * @return void
	 */
	public function mapWebRoutes()
	{
		\Route::group([
			'namespace' => 'StubNamespace\Controllers',
			'prefix'    => 'Module',
		], function (Router $router) {
			require_once module_path('Module', 'application/http/routes/front.php');
		});
	}

	/**
	 * Define the "api" routes for the application.
	 *
	 * These routes are typically stateless.
	 *
	 * @return void
	 */
	public function mapApiRoutes()
	{
		\Route::group([
			'namespace' => 'StubNamespace\Controllers',
			'prefix'    => 'api/Module',
		], function (Router $router) {
			require_once module_path('Module', 'application/http/routes/api.php');
		});
	}
}
