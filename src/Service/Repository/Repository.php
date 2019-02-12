<?php namespace Cucurbit\Framework\Service\Repository;

use Cucurbit\Framework\Service\Repository\Interfaces\Repository as BaseRepository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;

abstract class Repository implements BaseRepository
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
				return ucfirst(basename($item));
			});
			return $baseNames;
		} catch (\Throwable $e) {
			return collect();
		}
	}

}