<?php namespace Cucurbit\Framework;

use Cucurbit\Framework\Consoles\CommandServiceProvider;
use Cucurbit\Framework\Service\Repository\FileRepository;
use Cucurbit\Framework\Service\Repository\Interfaces\Repository;
use Cucurbit\Framework\Service\Service;
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
		$this->app['cucurbit']->register();
	}

	public function registerService()
	{
		$this->app->bind(Repository::class, FileRepository::class);

		$this->app->singleton('cucurbit', function ($app) {
			$repository = $app->make(Repository::class);
			return new Service($app, $repository);
		});
	}

	public function registerCommand()
	{
		$this->app->register(CommandServiceProvider::class);
	}
}