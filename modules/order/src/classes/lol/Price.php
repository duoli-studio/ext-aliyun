<?php namespace Order\Application\Lol;


class Price
{


	protected $destination = '';

	public static $prices = [
		'copper-5:copper-4'     => 0,
		'copper-4:copper-3'     => 0,
		'copper-3:copper-2'     => 0,
		'copper-2:copper-1'     => 0,
		'copper-1:silver-5'     => 0,
		'silver-5:silver-4'     => 0,
		'silver-4:silver-3'     => 0,
		'silver-3:silver-2'     => 0,
		'silver-2:silver-1'     => 0,
		'silver-1:gold-5'       => 0,
		'gold-5:gold-4'         => 0,
		'gold-4:gold-3'         => 0,
		'gold-3:gold-2'         => 0,
		'gold-2:gold-1'         => 0,
		'gold-1:platinum-5'     => 0,
		'platinum-5:platinum-4' => 0,
		'platinum-4:platinum-3' => 0,
		'platinum-3:platinum-2' => 0,
		'platinum-2:platinum-1' => 0,
		'platinum-1:diamond-5'  => 0,
		'diamond-5:diamond-4'   => 0,
		'diamond-4:diamond-3'   => 0,
		'diamond-3:diamond-2'   => 0,
		'diamond-2:diamond-1'   => 0,
		'diamond-1:master'      => 0,
		'location:copper'       => 0,
		'location:silver'       => 0,
		'location:gold'         => 0,
		'location:platinum'     => 0,
		'location:diamond'      => 0,
		'location:master'       => 0,

	];

	public static $define      = [
		'copper'   => '黄铜',
		'silver'   => '白银',
		'gold'     => '黄金',
		'platinum' => '铂金',
		'diamond'  => '钻石',
		'master'   => '大师',
		'location' => '定位赛',
	];
	public static $define_back = [
		'新号' => 'copper',
		'青铜' => 'copper',
		'黄铜' => 'copper',
		'白银' => 'silver',
		'黄金' => 'gold',
		'铂金' => 'platinum',
		'白金' => 'platinum',
		'钻石' => 'diamond',
		'大师' => 'master',
	];
	public static $qualifying  = [
		'单双排'  => 'single_row',
		'单双'   => 'single_row',
		'组排'   => 'group',
		'灵活排位' => 'group',
		'灵活组排' => 'group',
		'定位'   => 'location',
	];

	public static $type = [
		'至尊'  => 'best',
		'优质'  => 'good',
		'晋级赛' => 'level',
	];


	public static $area = [
		'normal'        => '普通区',
		'normal-good'   => '普通优质',
		'normal-best'   => '普通至尊',
		'normal-level'  => '普通晋级赛',
		'telecom'       => '[电信1]区',
		'telecom-good'  => '[电信1]优质',
		'telecom-best'  => '[电信1]至尊',
		'telecom-level' => '[电信1]晋级赛',
		'normal2'       => '[网通1/2 电信10]区',
		'normal2-good'  => '[网通1/2 电信10]优质',
		'normal2-best'  => '[网通1/2 电信10]至尊',
		'normal2-level' => '[网通1/2 电信10]晋级赛',
	];

	protected static $num = [
		1, 2, 3, 4, 5,
	];

	/**
	 * 格式化所有的段位
	 * @return array
	 */
	public static function multiFormat()
	{
		$format = self::fullKey();
		$keys   = [];
		foreach ($format as $key) {
			$keys[$key] = self::fullDesc($key);
		}
		return $keys;
	}

	/**
	 * 格式化一级段位
	 * @return array
	 */
	public static function format()
	{
		$format = [];
		foreach (self::$prices as $key => $price) {
			$format[$key] = self::fullDesc($key, $price);
		}
		return $format;
	}

	/**
	 * 段位描述
	 * @param     $key
	 * @param int $price
	 * @return array
	 */
	public static function fullDesc($key, $price = 0)
	{
		// $key = 'location:copper';
		list($start, $end) = explode(':', $key);
		if ($start == 'location') {
			return [
				'key'        => $key,
				'start'      => $start,
				'end'        => $end,
				'start_desc' => self::$define[$start],
				'end_desc'   => self::$define[$end],
			];
		}
		else {
			list($start_key, $start_num) = explode('-', $start);
			if (strpos($end, '-') === false) {
				// 大师级别
				$end_key = $end;
				$end_num = ($end_key == 'master') ? 0 : 1;
			}
			else {
				list($end_key, $end_num) = explode('-', $end);
			}

			return [
				'key'        => $key,
				'start'      => $start,
				'end'        => $end,
				'group'      => isset($start_key) ? $start_key : '',
				'start_desc' => self::$define[$start_key] . $start_num,
				'end_desc'   => self::$define[$end_key] . ($end_num ?: ''),
				'full_desc'  => (self::$define[$start_key] == self::$define[$end_key])
					? self::$define[$start_key] . $start_num . '-' . $end_num
					: self::$define[$start_key] . $start_num . '-' . self::$define[$end_key] . ($end_num ?: ''),
				'price'      => $price,
			];
		}
	}

	/**
	 * 获取所有的可能性值
	 * @return array
	 */
	public static function fullKey()
	{
		$keys = [];
		foreach (self::$define as $start_key => $start_desc) {
			foreach (self::$define as $end_key => $end_desc) {
				foreach (self::$num as $start_num) {
					foreach (self::$num as $end_num) {
						// 起始段位不可能是大师
						if ($start_key == 'master') {
							continue;
						}

						// 最终不是大师
						if ($end_key != 'master') {
							// 相等移除
							if (self::compare($start_key . '-' . $start_num, $end_key . '-' . $end_num) < 0) {
								$keys[] = $start_key . '-' . $start_num . ':' . $end_key . '-' . $end_num;
							}
						}
						else {
							$keys[] = $start_key . '-' . $start_num . ':' . $end_key;
						}
					}
				}

			}
		}
		return $keys;
	}


	/**
	 * 段位拆解
	 * @param $key
	 * @return array
	 */
	public static function dismantle($key)
	{
		list($start, $end) = explode(':', $key);
		$keys   = self::multiFormat();
		$return = [];
		foreach ($keys as $key => $desc) {
			if (self::compare($desc['start'], $start) >= 0 && self::compare($desc['end'], $end) <= 0) {
				if (isset(self::$prices[$key])) {
					$return[] = $key;
				}
			}
		}
		return $return;
	}


	/**
	 * 对比两个段位
	 * 第一个段位大于第二个, 返回1 ; 小于返回 -1; 等于 返回 0;
	 * @param $first
	 * @param $second
	 * @return int
	 */
	public static function compare($first, $second)
	{
		if (strpos($first, '-') === false) {
			// 大师级别
			$first_key = $first;
			$first_num = ($first_key == 'master') ? 0 : 1;
		}
		else {
			list($first_key, $first_num) = explode('-', $first);
		}
		if (strpos($second, '-') === false) {
			// 大师级别
			$second_key = $second;
			$second_num = ($second_key == 'master') ? 0 : 1;
		}
		else {
			list($second_key, $second_num) = explode('-', $second);
		}

		// 对比 key
		$first_index  = array_search($first_key, array_keys(self::$define));
		$second_index = array_search($second_key, array_keys(self::$define));

		// 黄金 黄金
		if ($first_index == $second_index) {
			if ($first_num > $second_num) {
				// 黄金 5  > 黄金 3
				return -1;
			}
			elseif ($first_num == $second_num) {
				// 黄金 5 = 黄金 5
				return 0;
			}
			else {
				// 黄金 5 < 黄金 3
				return 1;
			}
		}
		else if ($first_index > $second_index) {
			// 大师 黄金
			return 1;
		}
		else {
			// 白银 黄金
			return -1;
		}
	}
}


