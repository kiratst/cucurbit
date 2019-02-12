<?php
/*
|--------------------------------------------------------------------------
| Demo
|--------------------------------------------------------------------------
|
*/
\Route::group([
	'namespace'  => 'Api',
], function (Illuminate\Routing\Router $router) {
	$router->get('/', 'DemoController@index');
});