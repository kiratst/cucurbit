<?php

namespace Cucurbit\Framework\Support;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;

class ClassAutoLoader
{

	/** @var bool $registered */
	private $registered = false;

	/** @var array $loaded_classes */
	private static $loaded_classes = [];

	/** @var Filesystem $files */
	private $files;

	/** @var string $base_path */
	private $base_path;

	/** @var array $include_dir */
	private $include_directory = ['modules'];

	/**
	 * ClassAutoLoader constructor.
	 * @param Application $app
	 */
	public function __construct(Application $app)
	{
		$this->base_path = $app->basePath();
	}

	/**
	 * register autoload
	 */
	public function register()
	{
		if ($this->registered) {
			return;
		}

		$this->registered = spl_autoload_register([$this, 'autoload']);
	}

	/**
	 * file autoload
	 * @param string $class
	 */
	public function autoload($class)
	{
		if (isset(self::$loaded_classes[$class])) {
			$file_path = self::$loaded_classes[$class];
			$this->require($file_path);
			return;
		}

		$path = $this->class2Path($class);

		foreach ($this->include_directory as $directory) {
			$relative_path = $directory . DIRECTORY_SEPARATOR . $path;
			if ($this->isRealFilePath($relative_path)) {
				$this->require($relative_path);
			}
		}
	}

	/**
	 * class name convert to file path
	 * @param $class
	 * @return string
	 */
	private function class2Path($class): string
	{
		if ($class[0] === '\\') {
			$class = substr($class, 1);
		}

		$part      = explode('\\', $class);
		$file      = array_pop($part);
		$module    = strtolower(array_shift($part));
		$namespace = implode('\\', $part);

		// namespace 2 path
		$path = str_replace(['_', '\\'], DIRECTORY_SEPARATOR, $namespace);

		// uppercase 2 lowercase
		$directories = explode(DIRECTORY_SEPARATOR, $path);

		$path = array_reduce($directories, function ($carry, $directory) {
			if ($carry) {
				$carry .= DIRECTORY_SEPARATOR;
			}

			return $carry . snake_case($directory);
		});

		if ($part) {
			$path .= DIRECTORY_SEPARATOR;
		}

		$file_path = $module . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . $path . $file . '.php';

		return $file_path;
	}

	private function isRealFilePath($path)
	{
		return is_file(realpath($this->base_path . DIRECTORY_SEPARATOR . $path));
	}

	/**
	 * @param $file_path
	 */
	private function require($file_path)
	{
		require_once $this->base_path . DIRECTORY_SEPARATOR . $file_path;
	}
}