<?php

namespace Cucurbit\Framework\Consoles\Generators;

use Cucurbit\Framework\Service\Service;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

/**
 * base make command
 */
class BaseMakeCommand extends Command
{
	/**
	 * @var Filesystem
	 */
	protected $files;

	/**
	 * @var Service
	 */
	protected $service;

	/**
	 * @var array
	 */
	protected $replace_container = [];

	/**
	 * Create a new command instance.
	 *
	 * @param Filesystem $files
	 * @param Service    $service
	 */
	public function __construct(Filesystem $files, Service $service)
	{
		parent::__construct();
		$this->files   = $files;
		$this->service = $service;
	}

	/**
	 * content replace
	 *
	 * @param string $content file content
	 *
	 * @return mixed
	 */
	protected function replaceContent($content)
	{
		$search  = [
			'Module',
			'StubNamespace',
		];
		$replace = [
			$this->replace_container['Module'],
			$this->replace_container['namespace'],
		];

		return str_replace($search, $replace, $content);
	}

	/**
	 * return the namespace
	 * @param string $module module name
	 * @return string
	 */
	protected function getNamespace($module): string
	{
		return ucfirst($module);
	}

	/**
	 * @param string $module module name
	 * @return bool
	 */
	protected function checkModule($module): bool
	{
		if ($this->service->exists($module)) {
			$this->error('Module ' . $module . ' already exists!');
			return false;
		}

		return true;
	}

	/**
	 * make dir
	 * @param string $dir dir
	 */
	protected function makeDir($dir)
	{
		// create base module directory : Modules
		if (!$this->files->isDirectory(module_path())) {
			$this->files->makeDirectory(module_path());
		}

		if (!$this->files->isDirectory($dir)) {
			$this->files->makeDirectory($dir);
		}
	}
}
