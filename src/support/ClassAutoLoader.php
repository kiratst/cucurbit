<?php

namespace Cucurbit\Framework\Support;

use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
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

	/** @var string $cached_class_path */
	private $cached_class_path;

	/** @var array $include_dir */
	private $include_directory = ['modules'];

	/**
	 * ClassAutoLoader constructor.
	 * @param Application $app
	 */
	public function __construct(Application $app)
	{
		$this->base_path         = $app->basePath();
		$this->cached_class_path = $app->bootstrapPath() . '/cache/module_classes.php';
		$this->files             = new Filesystem();
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
	 * @throws Exception
	 */
	public function autoload($class)
	{
		$this->loadCache();

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

				self::$loaded_classes[$class] = $relative_path;
			}
		}

		$this->cache();
	}

	/**
	 * load cached classes
	 */
	private function loadCache()
	{
		if (!self::$loaded_classes) {
			try {
				self::$loaded_classes = $this->files->getRequire($this->cached_class_path);
			} catch (FileNotFoundException $e) {
				$this->files->put(
					$this->cached_class_path,
					'<?php return ' . var_export([], true) . ';'
				);

				self::$loaded_classes = [];
			}
		}
	}

	/**
	 * cache classes
	 * @throws Exception
	 */
	private function cache()
	{
		if (!self::$loaded_classes) {
			return;
		}

		if (!is_writable(\dirname($this->cached_class_path))) {
			throw new Exception('make sure `' . $this->cached_class_path . '` exists and writable ?');
		}

		$this->files->put(
			$this->cached_class_path,
			'<?php return ' . var_export(self::$loaded_classes, true) . ';'
		);
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

	private function isRealFilePath($path): bool
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