<?php
/*
|--------------------------------------------------------------------------
| Demo
|--------------------------------------------------------------------------
|
*/
\Route::group([
	'namespace'  => 'Backend',
], function (Illuminate\Routing\Router $router) {
	$router->get('/', 'DemoController@index');
});