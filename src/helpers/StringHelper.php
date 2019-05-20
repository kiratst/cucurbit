<?php

namespace Cucurbit\Framework\Helper;

use Exception;

/**
 * 字符串助手函数
 */
class StringHelper
{

	const STRING_TAGS_P = 'p'; //p标签

	const STRING_TAGS_BR = 'br'; //br标签

	/* 地球赤道半径
	 * ---------------------------------------- */
	const EARTH_RADIUS = 6378.135;

	protected static $snakeCache = [];

	protected static $camelCache = [];

	protected static $studlyCache = [];

	/**
	 * 生成随机字符串
	 *
	 * @param int $length 长度
	 *
	 * @return string
	 * @throws Exception on failure
	 */
	public static function generateRandomString(int $length = 32): string
	{
		if (!\is_int($length) || $length <= 8) {
			$length = 32;
		}
		$key = static::generateRandomKey($length);

		return substr(strtr(base64_encode($key), '+/', '-_'), 0, $length);
	}

	/**
	 * 随机生成指定长度key值
	 *
	 * @param int $length 长度
	 *
	 * @return string
	 * @throws Exception on failure
	 */
	public static function generateRandomKey(int $length = 32): string
	{
		if (\function_exists('random_bytes')) {
			return random_bytes($length);
		}

		if (\function_exists('openssl_random_pseudo_bytes')) {
			$bytes = openssl_random_pseudo_bytes($length);
			if ($bytes !== false || (int) $length === mb_strlen($bytes, '8bit')) {
				return $bytes;
			}

			throw new \RuntimeException('Cannot generate random string');
		}

		//重复打乱截取
		$pool = '1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';

		return substr(str_shuffle(str_repeat($pool, 6)), 0, $length);
	}

	/**
	 * 字符串截取,可截取中文
	 *
	 * @param string $string  待截取字符传
	 * @param int    $length  截取长度
	 * @param int    $start   截取位置
	 * @param string $charset 编码
	 * @param bool   $suffix  是否有后缀
	 *
	 * @return string
	 */
	public static function substrCn($string, $length = 20, $start = 0, $charset = 'utf-8', $suffix = true): string
	{
		//去除html标签
		$string = static::clearTags($string);

		if ($length > 0 && mb_strlen($string, $charset) <= $length) {
			return $string;
		}

		$slice = $length > 0 ? mb_substr($string, $start, $length, $charset)
			: $string;

		if ($suffix) {
			return $slice . '...';
		}

		return $slice;
	}

	/**
	 * 去除字符串html格式
	 *
	 * @param string $string 字符串
	 *
	 * @return string
	 */
	public static function clearTags(string $string = null): string
	{
		//去除html标签
		$string = strip_tags($string);

		//去除首位空格
		$string = trim($string);

		//去掉两个空格以上的
		$string = preg_replace('/\s(?=\s)/', '', $string);

		//非空格替换成一个空格
		$string = preg_replace('/[\n\r\t]/', ' ', $string);

		return $string;
	}

	/**
	 * text转html
	 *
	 * @param string $content 内容
	 * @param string $type    p使用p标签包裹,br\r\n换成<br/>
	 *
	 * @return string
	 */
	public static function text2Html(string $content, string $type = self::STRING_TAGS_P): string
	{
		$string = '';
		switch ($type) {
			case self::STRING_TAGS_P:
				//使用p标签包裹
				$string = '<p>' . str_replace(PHP_EOL, '</p><p>', $content)
					. '</p>';
				break;
			case self::STRING_TAGS_BR:
				$string = str_replace(PHP_EOL, '<br/>', $content);
				break;
		}

		return $string;
	}

	/**
	 * 给定经纬度,计算两点之间的距离
	 *
	 * @param float $lat1   地点1纬度
	 * @param float $lon1   地点1经度
	 * @param float $lat2   地点2纬度
	 * @param float $lon2   地点2经度
	 * @param float $radius 地球半径(默认为赤道半径)
	 *
	 * @return float|int
	 */
	public static function distance($lat1, $lon1, $lat2, $lon2, $radius = self::EARTH_RADIUS)
	{
		$rad = M_PI / 180;

		$lat1 = (float) $lat1 * $rad;
		$lon1 = (float) $lon1 * $rad;
		$lat2 = (float) $lat2 * $rad;
		$lon2 = (float) $lon2 * $rad;

		$theta = $lon2 - $lon1;

		$dist = acos(
			sin($lat1) * sin($lat2) +
			cos($lat1) * cos($lat2) *
			cos($theta)
		);

		if ($dist < 0) {
			$dist = M_PI;
		}

		return $dist * $radius;
	}

	/**
	 * 计算某个经纬度的周围某段距离的正方形的四个点
	 *
	 * @param float $lng      经度
	 * @param float $lat      纬度
	 * @param float $distance 该点所在圆的半径，该圆与此正方形内切，默认值为0.5千米
	 *
	 * @return array 正方形的四个点的经纬度坐标
	 */
	public static function squarePoint($lng, $lat, $distance = 0.5): array
	{
		$dlng = 2 * asin(sin($distance / (2 * self::EARTH_RADIUS)) / cos(deg2rad($lat)));
		$dlng = rad2deg($dlng);

		$dlat = $distance / self::EARTH_RADIUS;
		$dlat = rad2deg($dlat);

		return [
			'left-top'     => ['lat' => $lat + $dlat, 'lng' => $lng - $dlng],
			'right-top'    => ['lat' => $lat + $dlat, 'lng' => $lng + $dlng],
			'left-bottom'  => ['lat' => $lat - $dlat, 'lng' => $lng - $dlng],
			'right-bottom' => ['lat' => $lat - $dlat, 'lng' => $lng + $dlng],
		];
	}

	/**
	 * 字符串转小写
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	public static function lower($value): string
	{
		return mb_strtolower($value, 'UTF-8');
	}

	/**
	 * 驼峰转下划线
	 *
	 * @param string $value
	 * @param string $delimiter
	 *
	 * @return string
	 */
	public static function snake($value, $delimiter = '_'): string
	{
		$key = $value;

		if (isset(static::$snakeCache[$key][$delimiter])) {
			return static::$snakeCache[$key][$delimiter];
		}

		if (!ctype_lower($value)) {
			$value = preg_replace('/\s+/u', '', $value);

			$value = static::lower(preg_replace('/(.)(?=[A-Z])/u',
				'$1' . $delimiter, $value));
		}

		return static::$snakeCache[$key][$delimiter] = $value;
	}

	/**
	 * 下划线转驼峰(首字母大写)
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	public static function studly($value): string
	{
		$key = $value;

		if (isset(static::$studlyCache[$key])) {
			return static::$studlyCache[$key];
		}

		$value = ucwords(str_replace(['-', '_'], ' ', $value));

		return static::$studlyCache[$key] = str_replace(' ', '', $value);
	}

	/**
	 * 下划线转驼峰(首字母小写)
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	public static function camel($value): string
	{
		if (isset(static::$camelCache[$value])) {
			return static::$camelCache[$value];
		}

		return static::$camelCache[$value] = lcfirst(static::studly($value));
	}

	/**
	 * 转为首字母大写的标题格式
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	public static function title($value): string
	{
		return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
	}

}