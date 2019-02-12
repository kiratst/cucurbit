<?php
/*
|--------------------------------------------------------------------------
| Demo
|--------------------------------------------------------------------------
|
*/
\Route::group([
	'namespace'  => 'Front',
], function (Illuminate\Routing\Router $router) {
	$router->get('/', 'DemoController@index');
});