<?php

if (!function_exists('module_path')) {
	/**
	 * return the module path
	 *
	 * @param null|string $module module
	 * @param null|string $file   file path
	 * @return string
	 */
	function module_path($module = null, $file = null)
	{
		$base_path = base_path('Modules');

		if (!$module) {
			return $base_path;
		}

		$module = ucfirst($module);

		$module_path = $module ? $base_path . '/' . ltrim($module, '/') : '';
		if (!$file) {
			return $module_path;
		}

		return $module_path . '/Application/' . $file;
	}
}

if (!function_exists('module_class')) {
	/**
	 * return class's namespace
	 *
	 * @param string $module module_name
	 * @param string $class  class
	 * @return string
	 */
	function module_class($module, $class = '')
	{
		$name      = 'Modules\\' . $module;
		$namespace = studly_case($name);

		if (!$class) {
			return $namespace;
		}

		return "{$namespace}\\{$class}";
	}
}