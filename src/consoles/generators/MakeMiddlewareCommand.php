<?php

namespace Cucurbit\Framework\Consoles\Generators;

use Cucurbit\Framework\Service\Service;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeMiddlewareCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'cucurbit:middleware 
				{module : the name of module}
				{middleware : the name of middleware} ';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'create a middleware';

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
		$module = strtolower($module);

		if (!$this->service->exists($module)) {
			$this->error('Module ' . $module . ' does not exists!');
			return false;
		}

		if (!$this->files->exists(module_path($module))) {
			$this->error('Module ' . $module . ' does not exists!');
			return false;
		}

		$middleware      = $this->argument('middleware');
		$middleware_file = module_path($module, 'application/http/middleware/' . $middleware) . '.php';

		if ($this->files->exists($middleware_file)) {
			$this->error('Model ' . $middleware . ' already exists!');
			return false;
		}

		$this->replace_container['namespace']      = 'Modules\\' . ucfirst($module);
		$this->replace_container['MiddlewareName'] = $middleware;

		/* stubs resource
		 * ---------------------------------------- */
		$resource_path = __DIR__ . '/stubs/MiddlewareStub.stub';
		try {
			$content = $this->replaceContent($this->files->get($resource_path));

			$this->files->put($middleware_file, $content);

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
			'MiddlewareName',
		];
		$replace = [
			$this->replace_container['namespace'],
			$this->replace_container['MiddlewareName'],
		];

		return str_replace($search, $replace, $content);
	}
}
