<?php namespace Cucurbit\Framework\Helpers;

class CookieHelper
{
	/**
	 * 判断Cookie是否存在
	 * @param $name
	 * @return bool
	 */
	public static function has($name): bool
	{
		return isset($_COOKIE[$name]);
	}

	/**
	 * 获取某个Cookie值
	 * @param $name
	 * @return string
	 */
	public static function get($name): string
	{
		return $_COOKIE[$name] ?? '';
	}

	/**
	 * 设置cookie
	 * @param string $name
	 * @param mixed  $value
	 * @param int    $expire
	 * @param string $path
	 * @param string $domain
	 * @return bool
	 */
	public static function set($name, $value, $expire = 0, $path = '', $domain = ''): bool
	{
		if (empty($path)) {
			$path = '/';
		}
		if (empty($domain)) {
			$domain = $_SERVER['SERVER_NAME'];
		}

		$expire = !empty($expire) ? time() + $expire : 0;

		return setcookie($name, $value, $expire, $path, $domain);
	}

	/**
	 * 删除某个Cookie值
	 * @param string $name key
	 */
	public static function remove($name)
	{
		self::set($name, '', time() - 3600);
		unset($_COOKIE[$name]);
	}

	/**
	 * 清空所有Cookie值
	 */
	public static function clear()
	{
		unset($_COOKIE);
	}
}