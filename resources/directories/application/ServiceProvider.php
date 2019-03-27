<?php

namespace StubNamespace;

use Cucurbit\Framework\Support\ServiceProvider as CucurbitSupport;
use Illuminate\Console\Scheduling\Schedule;
use StubNamespace\Http\RouteServiceProvider;

class ServiceProvider extends CucurbitSupport
{
	/**
	 * @var string
	 */
	private $name = 'Module';

	/**
	 * @var array listeners
	 */
	protected $listeners = [];

	/**
	 * @var array policy
	 */
	protected $policies = [];


    /**
     * Bootstrap any application services.
     *
     * @return void
     * @throws \Exception
     */
    public function boot()
    {
        //
	    parent::boot($this->name);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
	    $this->app->register(RouteServiceProvider::class);

	    $this->commands([]);

		$this->registerSchedule();
    }

	/**
	 * register schedule
	 */
	private function registerSchedule()
	{
		$this->app['events']->listen('console.schedule', function (Schedule $schedule) {
			$schedule->command('command')
				->everyThirtyMinutes()->appendOutputTo($this->consoleLogPath());
		});
	}
}
