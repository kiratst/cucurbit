<?php

namespace Cucurbit\Framework\Support;

use Illuminate\Support\ServiceProvider;

class AutoLoadServiceProvider extends ServiceProvider
{
	public function register()
	{
		$autoloader = new ClassAutoLoader($this->app);

		$this->app->instance(ClassAutoLoader::class, $autoloader);

		$autoloader->register();
	}

}