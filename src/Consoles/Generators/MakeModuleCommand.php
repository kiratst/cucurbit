<?php namespace Cucurbit\Framework\Consoles\Generators;

use Cucurbit\Framework\Service\Service;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeModuleCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'cucurbit:module {module : the name of module}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'create a module';

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
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		// module name
		$module = $this->argument('module');
		$module = ucfirst(strtolower($module));

		// create base module directory : Modules
		if (!$this->files->isDirectory(module_path())) {
			$this->files->makeDirectory(module_path());
		}

		if ($this->service->exists($module)) {
			$this->error('Module ' . $module . ' already exists!');
			return false;
		}

		// create module directory
		$module_directory = module_path($module);
		if (!$this->files->isDirectory($module_directory)) {
			$this->files->makeDirectory($module_directory);
		}

		$this->replace_container['namespace'] = 'Modules\\' . $module;
		$this->replace_container['Module']    = lcfirst($module);

		/* stubs resource
		 * ---------------------------------------- */
		$resource_path  = __DIR__ . '/../../../resources/directories/';
		$resource_files = $this->files->allFiles($resource_path, true);
		foreach ($resource_files as $resource_file) {
			$content = $this->replaceContent($resource_file->getContents());

			$subPath  = $resource_file->getRelativePathname();
			$filePath = $module_directory . '/Application/' . $subPath;
			$dir      = \dirname($filePath);

			if (!$this->files->isDirectory($dir)) {
				$this->files->makeDirectory($dir, 0755, true);
			}
			$this->files->put($filePath, $content);
		}

		/* clear module optimize
		 * ---------------------------------------- */
		$this->service->optimize();

		return true;
	}

	/**
	 * content replace
	 *
	 * @param string $content file content
	 *
	 * @return mixed
	 */
	public function replaceContent($content)
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
}
