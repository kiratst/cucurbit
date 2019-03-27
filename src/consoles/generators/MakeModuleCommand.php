<?php

namespace Cucurbit\Framework\Consoles\Generators;

class MakeModuleCommand extends BaseMakeCommand
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
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		// module name
		$module = $this->argument('module');
		$module = ucfirst(strtolower($module));

		if (!$this->checkModule($module)) {
			return false;
		}

		// create module directory
		$module_directory = module_path($module);
		$this->makeDir($module_directory);

		$this->replace_container['namespace'] = $this->getNamespace($module);
		$this->replace_container['Module']    = lcfirst($module);

		/* stubs resource
		 * ---------------------------------------- */
		$resource_path  = __DIR__ . '/../../../resources/directories/';
		$resource_files = $this->files->allFiles($resource_path, true);
		foreach ($resource_files as $resource_file) {
			$content = $this->replaceContent($resource_file->getContents());

			$subPath  = $resource_file->getRelativePathname();
			$filePath = $module_directory . '/' . $subPath;
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

}
