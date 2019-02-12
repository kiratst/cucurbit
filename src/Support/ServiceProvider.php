<?php namespace Cucurbit\Framework\Support;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

abstract class ServiceProvider extends BaseServiceProvider
{
	/**
	 * @var array listeners
	 */
	protected $listeners = [];

	/**
	 * @var array policy
	 */
	protected $policies = [];

	/**
	 * @throws \Exception
	 */
	public function boot()
	{
		if ($module = $this->getModule(\func_get_args())) {
			if ($this->listeners) {
				$this->bootListener();
			}

			if ($this->policies) {
				$this->bootPolicy();
			}
		}
	}

	/**
	 * @param $args
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function getModule($args)
	{
		$name = (isset($args[0]) and \is_string($args[0])) ? ucfirst($args[0]) : null;
		if ($name) {
			if (!app('cucurbit')->exists($name)) {
				throw new \Exception("Module {$name} doesn't exists");
			}
			return $name;
		}
		return '';
	}

	/**
	 * boot listener
	 */
	protected function bootListener()
	{
		foreach ($this->listeners as $event => $listeners) {
			foreach ($listeners as $listener) {
				\Event::listen($event, $listener);
			}
		}
	}

	/**
	 * boot policy
	 */
	protected function bootPolicy()
	{
		foreach ($this->policies as $key => $value) {
			\Gate::policy($key, $value);
		}
	}

	/**
	 * @return string
	 */
	protected function consoleLogPath(): string
	{
		$day = Carbon::now()->toDateString();

		return storage_path('logs/console-' . $day . '.log');
	}
}