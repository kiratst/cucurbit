<?php

namespace Cucurbit\Framework\Consoles;

use Cucurbit\Framework\Consoles\Commands\OptimizeCommand;
use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->registerGenerator();

		$this->optimize();
	}

	/**
	 * register generator commands
	 */
	public function registerGenerator()
	{
		$generators = [
			'command.make.cucurbit.module'     => Generators\MakeModuleCommand::class,
			'command.make.cucurbit.model'      => Generators\MakeModelCommand::class,
			'command.make.cucurbit.middleware' => Generators\MakeMiddlewareCommand::class,
		];

		foreach ($generators as $key => $class) {
			$this->app->singleton($class);

			$this->commands($class);
		}
	}

	public function optimize()
	{
		$this->app->singleton('command.cucurbit.optimize', function ($app) {
			return new OptimizeCommand();
		});
		$this->commands('command.cucurbit.optimize');
	}

}