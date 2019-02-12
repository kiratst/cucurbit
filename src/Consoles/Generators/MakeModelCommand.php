<?php namespace Cucurbit\Framework\Consoles\Generators;

use Cucurbit\Framework\Service\Service;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeModelCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'cucurbit:model 
				{module : the name of module}
				{model : the name of model} ';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'create a model';

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

		if (!$this->service->exists($module)) {
			$this->error('Module ' . $module . ' does not exists!');
			return false;
		}

		if (!$this->files->exists(module_path($module))) {
			$this->error('Module ' . $module . ' does not exists!');
			return false;
		}

		$model      = $this->argument('model');
		$model_file = module_path($module, 'Models/' . $model) . '.php';

		if ($this->files->exists($model_file)) {
			$this->error('Model ' . $model . ' already exists!');
			return false;
		}

		$this->replace_container['namespace'] = 'Modules\\' . $module;
		$this->replace_container['ModelName'] = $model;

		/* stubs resource
		 * ---------------------------------------- */
		$resource_path = __DIR__ . '/stubs/ModelStub.stub';
		try {
			$content = $this->replaceContent($this->files->get($resource_path));

			$this->files->put($model_file, $content);

		} catch (\Throwable $e) {
			$this->error($e->getMessage());
		}

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
			'StubNamespace',
			'ModelName',
		];
		$replace = [
			$this->replace_container['namespace'],
			$this->replace_container['ModelName'],
		];

		return str_replace($search, $replace, $content);
	}
}
