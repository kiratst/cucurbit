<?php

namespace Cucurbit\Framework\Helpers;

/**
 * Class UtilHelper
 * @package Cucurbit\Framework\Helpers
 */
class UtilHelper
{
	/**
	 * @param string $string ip
	 * @return bool
	 */
	public static function isIp($string): bool
	{
		return (bool) filter_var($string, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6);
	}

	/**
	 * @param string $string
	 * @return false|int
	 */
	public static function isUrl($string)
	{
		return preg_match('/^http(s?):\/\/.*/', $string, $matches);
	}

	/**
	 * @param string $email
	 * @return bool
	 */
	public static function isEmail($email): bool
	{
		return \strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
	}

	/**
	 * @param string $mobile
	 * @return false|int
	 */
	public static function isMobile($mobile)
	{
		return preg_match("/^(\+86)?1(3|4|5|6|8|7|9)\d\d{8}$/", $mobile);
	}

	/**
	 * @param string $telephone
	 * @return false|int
	 */
	public function isTelephone($telephone)
	{
		return preg_match("/((\d{11})|^((\d{7,8})|(\d{4}|\d{3})-(\d{7,8})|(\d{4}|\d{3})-(\d{7,8})-(\d{4}|\d{3}|\d{2}|\d{1})|(\d{7,8})-(\d{4}|\d{3}|\d{2}|\d{1}))$)/", $telephone);

	}

	/**
	 * @param string $link
	 * @return string
	 */
	public static function fixLink($link): string
	{
		if (\strlen($link) < 10) {
			return '';
		}

		return strpos($link, '://') === false ? 'http://' . $link : $link;
	}
}