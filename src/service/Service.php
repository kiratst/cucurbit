<?php

namespace Cucurbit\Framework\Service;

use Cucurbit\Framework\Repository\FileRepository;
use Illuminate\Foundation\Application;

class Service
{
	/**
	 * @var Application
	 */
	public $app;

	/**
	 * @var FileRepository
	 */
	public $repository;

	/**
	 * Create a Modules instance.
	 *
	 * @param Application    $app
	 * @param FileRepository $repository
	 */
	public function __construct(Application $app, FileRepository $repository)
	{
		$this->app        = $app;
		$this->repository = $repository;
	}

	public function register()
	{
		// all modules
		$modules = $this->repository->all();
		$modules->each(function ($module, $module_name) {
			$this->registerServiceProvider($module_name);
			$this->autoloadFiles($module);
		});

	}

	/**
	 * register module's service provider
	 * @param $module
	 */
	public function registerServiceProvider($module)
	{
		if ($this->repository->files->isDirectory(module_path($module))) {
			$service_provider = module_class($module, 'ServiceProvider');
			if (class_exists($service_provider)) {
				$this->app->register($service_provider);
			}
		}
	}

	/**
	 * autoload module files
	 * @param string $module module_name
	 */
	private function autoloadFiles($module)
	{
		if (isset($module['autoload'])) {
			foreach ($module['autoload'] as $file) {
				$path = module_path($module['module'], $file);
				if (file_exists($path)) {
					include $path;
				}
			}
		}
	}

	public function __call($method, $arguments)
	{
		return \call_user_func_array([$this->repository, $method], $arguments);
	}

}