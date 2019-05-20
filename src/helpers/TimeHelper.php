<?php

namespace Cucurbit\Framework\Helpers;

use Carbon\Carbon;

/**
 * 时间助手函数
 */
class TimeHelper
{

	/* 差值格式化类型
	 * ---------------------------------------- */
	const DIFF_FORMAT_TYPE_DAYS   = 'days';
	const DIFF_FORMAT_TYPE_MONTHS = 'months';
	const DIFF_FORMAT_TYPE_YEARS  = 'years';

	/* 排列顺序
	 * ---------------------------------------- */
	const DATE_SORT_ASC  = 'asc';
	const DATE_SORT_DESC = 'desc';

	/* 步频
	 * ---------------------------------------- */
	const DIFF_STEP = 1;

	/* 秒数
	 * ---------------------------------------- */
	const SECONDS_OF_DAY = 86400;

	/**
	 * 一天的开始
	 * @param string|null $date 日期
	 * @return string
	 */
	public static function dayStart(string $date = null): string
	{
		if (!$date) {
			$date = date('Y-m-d H:i:s');
		}

		return Carbon::parse($date)->startOfDay();
	}

	/**
	 * 一天的结束
	 * @param string|null $date 日期
	 * @return string
	 */
	public static function dayEnd(string $date = null): string
	{
		if (!$date) {
			$date = date('Y-m-d H:i:s');
		}

		return Carbon::parse($date)->endOfDay();
	}

	/**
	 * 按格式获取两个时间段的所有日期
	 * @param string $start_date 开始日期
	 * @param string $end_date   结束日期
	 * @param string $diff_type  返回格式 days:天; months:月; years: 年
	 * @param string $sort       排列顺序
	 * @return array
	 */
	public static function dateDiff($start_date, $end_date, $diff_type = self::DIFF_FORMAT_TYPE_DAYS, $sort = self::DATE_SORT_ASC): array
	{
		$date   = [];
		$format = [
			self::DIFF_FORMAT_TYPE_DAYS   => 'Y-m-d',
			self::DIFF_FORMAT_TYPE_MONTHS => 'Y-m',
			self::DIFF_FORMAT_TYPE_YEARS  => 'Y',
		];

		if (!isset($format[$diff_type])) {
			$diff_type = self::DIFF_FORMAT_TYPE_DAYS;
		}

		$method = ($sort === self::DATE_SORT_ASC ? 'add' : 'sub') . ucfirst($diff_type);

		$start = is_numeric($start_date) ? Carbon::create($start_date)->firstOfYear() : Carbon::parse($start_date);
		$end   = is_numeric($end_date) ? Carbon::create($end_date)->lastOfYear() : Carbon::parse($end_date);

		while ($start <= $end) {
			if ($sort === self::DATE_SORT_ASC) {
				$date[] = $start->format($format[$diff_type]);
				$start->$method(self::DIFF_STEP);
			}
			if ($sort === self::DATE_SORT_DESC) {
				$date[] = $end->format($format[$diff_type]);
				$end->$method(self::DIFF_STEP);
			}
		}
		return $date;
	}

	/**
	 * 返回今日开始和结束的时间戳
	 *
	 * @return array
	 */
	public static function today(): array
	{
		return [
			mktime(0, 0, 0, date('m'), date('d'), date('Y')),
			mktime(23, 59, 59, date('m'), date('d'), date('Y'))
		];
	}

	/**
	 * 返回昨日开始和结束的时间戳
	 *
	 * @return array
	 */
	public static function yesterday(): array
	{
		$yesterday = date('d') - 1;
		return [
			mktime(0, 0, 0, date('m'), $yesterday, date('Y')),
			mktime(23, 59, 59, date('m'), $yesterday, date('Y'))
		];
	}

	/**
	 * 返回本周开始和结束的时间戳
	 *
	 * @return array
	 */
	public static function week(): array
	{
		$timestamp = time();
		return [
			strtotime(date('Y-m-d', strtotime('+0 week Monday', $timestamp))),
			strtotime(date('Y-m-d', strtotime('+0 week Sunday', $timestamp))) + self::SECONDS_OF_DAY - 1
		];
	}

	/**
	 * 返回上周开始和结束的时间戳
	 *
	 * @return array
	 */
	public static function lastWeek(): array
	{
		$timestamp = time();
		return [
			strtotime(date('Y-m-d', strtotime('last week Monday', $timestamp))),
			strtotime(date('Y-m-d', strtotime('last week Sunday', $timestamp))) + self::SECONDS_OF_DAY - 1
		];
	}

	/**
	 * 返回本月开始和结束的时间戳
	 * @return array
	 */
	public static function month(): array
	{
		return [
			mktime(0, 0, 0, date('m'), 1, date('Y')),
			mktime(23, 59, 59, date('m'), date('t'), date('Y'))
		];
	}

	/**
	 * 返回上个月开始和结束的时间戳
	 *
	 * @return array
	 */
	public static function lastMonth(): array
	{
		$begin = mktime(0, 0, 0, date('m') - 1, 1, date('Y'));
		$end   = mktime(23, 59, 59, date('m') - 1, date('t', $begin), date('Y'));

		return [$begin, $end];
	}

	/**
	 * 返回今年开始和结束的时间戳
	 *
	 * @return array
	 */
	public static function year(): array
	{
		return [
			mktime(0, 0, 0, 1, 1, date('Y')),
			mktime(23, 59, 59, 12, 31, date('Y'))
		];
	}

	/**
	 * 返回去年开始和结束的时间戳
	 *
	 * @return array
	 */
	public static function lastYear(): array
	{
		$year = date('Y') - 1;
		return [
			mktime(0, 0, 0, 1, 1, $year),
			mktime(23, 59, 59, 12, 31, $year)
		];
	}

	public static function dayOf()
	{

	}

	/**
	 * 获取几天前零点到现在/昨日结束的时间戳
	 *
	 * @param int  $day 天数
	 * @param bool $now 返回现在或者昨天结束时间戳
	 * @return array
	 */
	public static function dayToNow($day = 1, $now = true): array
	{
		$end = time();
		if (!$now) {
			list($foo, $end) = self::yesterday();
		}

		return [
			mktime(0, 0, 0, date('m'), date('d') - $day, date('Y')),
			$end
		];
	}

	/**
	 * 返回几天前的时间戳
	 *
	 * @param int $day
	 * @return int
	 */
	public static function daysAgo($day = 1): int
	{
		$nowTime = time();
		return $nowTime - self::daysToSecond($day);
	}

	/**
	 * 返回几天后的时间戳
	 *
	 * @param int $day
	 * @return int
	 */
	public static function daysAfter($day = 1): int
	{
		$nowTime = time();
		return $nowTime + self::daysToSecond($day);
	}

	/**
	 * 天数转换成秒数
	 *
	 * @param int $day
	 * @return int
	 */
	public static function daysToSecond($day = 1): int
	{
		return $day * self::SECONDS_OF_DAY;
	}

	/**
	 * 周数转换成秒数
	 *
	 * @param int $week
	 * @return int
	 */
	public static function weekToSecond($week = 1): int
	{
		return self::daysToSecond() * 7 * $week;
	}
}