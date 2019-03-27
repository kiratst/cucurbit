<?php

namespace Cucurbit\Framework\Contracts;

use Illuminate\Support\Collection;

interface RepositoryInterface
{
	/**
	 * Get all modules.
	 *
	 * @return Collection
	 */
	public function all(): Collection;

	/**
	 * Get all module names.
	 *
	 * @return Collection
	 */
	public function names(): Collection;

	/**
	 * Determines if the given module exists.
	 *
	 * @param string $key
	 *
	 * @return bool
	 */
	public function exists($key): bool;

	/**
	 * Returns the given module property.
	 *
	 * @param string     $property
	 * @param mixed|null $default
	 *
	 * @return mixed|null
	 */
	public function get($property, $default = null);

	/**
	 * Set the given module property value.
	 *
	 * @param string $property
	 * @param mixed  $value
	 *
	 * @return bool
	 */
	public function set($property, $value): bool;

	public function getManifest($module);
}