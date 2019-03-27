<?php

namespace Cucurbit\Framework;

use Cucurbit\Framework\Consoles\CommandServiceProvider;
use Cucurbit\Framework\Contracts\RepositoryInterface;
use Cucurbit\Framework\Repository\FileRepository;
use Cucurbit\Framework\Service\Service;
use Cucurbit\Framework\Support\AutoLoadServiceProvider;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
	public function register()
	{
		$this->registerService();
		$this->registerCommand();
	}

	public function boot()
	{
		$this->publishes([
			__DIR__ . '/../config/cucurbit.php' => config_path('cucurbit.php'),
		], 'cucurbit-framework');

		$this->app['cucurbit']->register();
	}

	public function registerService()
	{
		$this->app->bind(RepositoryInterface::class, FileRepository::class);

		$this->app->singleton('cucurbit', function ($app) {
			$repository = $app->make(RepositoryInterface::class);
			return new Service($app, $repository);
		});

		$this->app->register(AutoLoadServiceProvider::class);
	}

	public function registerCommand()
	{
		$this->app->register(CommandServiceProvider::class);
	}
}