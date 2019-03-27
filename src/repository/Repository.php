<?php

namespace Cucurbit\Framework\Repository;

use Cucurbit\Framework\Contracts\RepositoryInterface;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;

abstract class Repository implements RepositoryInterface
{

	public $path;

	/** @var Filesystem */
	public $files;

	public function __construct(Filesystem $files)
	{
		$this->files = $files;
	}

	/**
	 * get the path of cache file
	 *
	 * @return string
	 */
	protected function getCachePath(): string
	{
		return storage_path('app/modules.json');
	}

	/**
	 * @return mixed
	 */
	public function getPath()
	{
		return $this->path ?: module_path();
	}

	/**
	 * @param mixed $path
	 */
	public function setPath($path)
	{
		$this->path = $path;
	}

	/**
	 * get all modules base name
	 *
	 * @return Collection
	 */
	protected function getAllBaseNames()
	{
		$path = $this->getPath();

		try {
			$collection = collect($this->files->directories($path));
			$baseNames  = $collection->map(function ($item, $key) {
				return basename($item);
			});
			return $baseNames;
		} catch (\Throwable $e) {
			return collect();
		}
	}

	/**
	 * @param $module
	 * @return array|mixed|string
	 * @throws \Exception
	 */
	public function getManifest($module)
	{
		$manifest_path = $this->getManifestPath($module);
		try {
			$content = $this->files->get($manifest_path);
			$content = json_decode($content, true) ?? [];
		} catch (FileNotFoundException $e) {
			throw new \Exception('file not found: ' . $manifest_path);
		}

		return $content;
	}

	protected function getManifestPath($module): string
	{
		return module_path($module) . '/manifest.json';
	}
}