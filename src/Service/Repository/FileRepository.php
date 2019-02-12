<?php namespace Cucurbit\Framework\Service\Repository;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Collection;

class FileRepository extends Repository
{
	/**
	 * get all modules
	 *
	 * @return Collection
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	public function all(): Collection
	{
		return $this->getCache();
	}

	/**
	 * get all modules name
	 *
	 * @return Collection
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	public function names(): Collection
	{
		return $this->all();
	}


	public function set($property, $value): bool
	{
	}

	public function get($property, $default = null)
	{
	}

	/**
	 * @param string $key
	 * @return bool
	 * @throws FileNotFoundException
	 */
	public function exists($key): bool
	{
		return $this->names()->contains($key);
	}


	/**
	 * update module information cache
	 *
	 * @return bool
	 */
	public function optimize()
	{
		$cachePath = $this->getCachePath();
		//		$cache     = $this->getCache();
		$baseNames = $this->getAllBaseNames();

		// update module cache
		$content = json_encode($baseNames->toArray(), JSON_PRETTY_PRINT);
		$this->files->put($cachePath, $content);

		// update composer.json
		//		$this->updateComposer();
		//
		//		exec('composer dump-autoload');
		return true;
	}

	/**
	 * @return bool
	 * @throws FileNotFoundException
	 */
	public function updateComposer()
	{
		$names    = $this->getAllBaseNames();
		$base_path = base_path();

		$file    = $base_path . '/composer.json';
		try {
			$content = $this->files->get($file);
		} catch (FileNotFoundException $e) {
			throw new FileNotFoundException('file ' . $file . 'not found');
		}

		$data = json_decode($content, true);

		$autoload  = $data['autoload'];
		$load_name = collect($autoload['psr-4']);

		$load = collect();
		$load_name->each(function ($value, $key) use ($load) {
			list($name, $test) = explode('\\', $key);
			$load->push(strtolower($name));
		});

		if ($names->diff($load->unique()->toArray())->isEmpty()) {
			return true;
		}

		$modules = [];
		$names->each(function ($item) use (&$modules) {
			$key   = ucfirst($item);
			$value = "modules/{$key}/Application/";

			$modules["{$key}\\"] = $value;
		});

		$data['autoload']['psr-4'] = $modules;

		$content = json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
		$this->files->put($file, $content);
		return true;
	}

	/**
	 * Clear optimized module cache
	 * @return bool
	 */
	public function clear()
	{
		$cachePath = $this->getCachePath();
		if ($this->files->exists($cachePath)) {
			$this->files->delete($cachePath);
		}
		return true;
	}

	/**
	 * create instance of cache file
	 *
	 * @return Collection
	 */
	private function createCache()
	{
		$cachePath = $this->getCachePath();
		$content   = json_encode([], JSON_PRETTY_PRINT);

		$this->files->put($cachePath, $content);

		return collect(json_decode($content, true));
	}

	/**
	 * get cache content
	 *
	 * @return Collection
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	private function getCache(): Collection
	{
		$cachePath = $this->getCachePath();
		if (!$this->files->exists($cachePath)) {
			$this->createCache();
			$this->optimize();
		}

		return collect(json_decode($this->files->get($cachePath), true));
	}
}