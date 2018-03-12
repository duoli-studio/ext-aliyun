<?php namespace System\Request\Develop;

/**
 * 环境检测工具
 */
class EnvController extends InitController
{


	public function phpinfo()
	{
		return view('system::develop.env.phpinfo');
	}

	public function check()
	{

		$env = [
			'weixin'       => [
				'title'  => '微信',
				'result' => class_exists('\Poppy\Extension\Wxpay\Lib\WxPayAppApiPay'),
			],
			'alipay'       => [
				'title'  => '支付宝',
				'result' => class_exists('\Finance\Classes\Extension\AliPay'),
			],
			'yunxin'       => [
				'title'  => '网易云信',
				'result' => class_exists('\Poppy\Extension\NetEase\Im\Yunxin'),
			],
			'node'       => [
				'title'  => 'Node',
				'result' =>  command_exist('node'),
			],
			'apidoc'       => [
				'title'  => 'Node-apidoc',
				'result' =>  command_exist('apidoc'),
			],
			'php-gd'       => [
				'title'  => 'PHP-Gd',
				'result' =>  extension_loaded('gd'),
			],
			'php-json'       => [
				'title'  => 'PHP-JSON',
				'result' =>  extension_loaded('json'),
			],

			'php-iconv'       => [
				'title'  => 'PHP-iconv',
				'result' =>  extension_loaded('iconv'),
			],

			'php-mysqlnd'       => [
				'title'  => 'PHP-mysqlnd',
				'result' =>  extension_loaded('mysqlnd'),
			],
			'php-mbstring'       => [
				'title'  => 'PHP-mbstring',
				'result' =>  extension_loaded('mbstring'),
			],
			'php-bcmath'       => [
				'title'  => 'PHP-bcmath',
				'result' =>  extension_loaded('bcmath'),
			],
		];
		return view('system::develop.env.check', [
			'env' => $env,
		]);
	}

	/**
	 * 检查数据库设计
	 * @url http://blog.csdn.net/zhezhebie/article/details/78589812
	 */
	public function db()
	{
		$tables = array_map('reset', \DB::select('show tables'));

		$suggestString   = function($col) {
			if (strpos($col['Type'], 'char') !== false) {
				if ($col['Null'] === 'YES') {
					return '(Char-null)';
				}
				if (!is_null($col['Default']) && $col['Default'] !== '') {
					if (!is_string($col['Default'])) {
						return '(Char-default)';
					}
				}
			}
			return '';
		};
		$suggestInt      = function($col) {
			if (strpos($col['Type'], 'int') !== false) {
				switch ($col['Key']) {
					case "PRI":
						// 主键不能为Null (Allow Null 不可选)
						// Default 不可填入值
						// 所以无任何输出
						break;
					default:
						if (!is_numeric($col['Default'])) {
							return '(Int-default)';
						}
						if ($col['Null'] === 'YES') {
							return '(Int-Null)';
						}
						break;
				}
			}
			return '';
		};
		$suggestDecimal  = function($col) {
			if (strpos($col['Type'], 'decimal') !== false) {
				if ($col['Default'] !== "0.00") {
					return '(Decimal-default)';
				}
				if ($col['Null'] === 'YES') {
					return '(Decimal-Null)';
				}
			}
			return '';
		};
		$suggestDatetime = function($col) {
			if (strpos($col['Type'], 'datetime') !== false) {
				if (!is_null($col['Default'])) {
					return '(Datetime-default)';
				}
				if ($col['Null'] === 'NO') {
					return '(Datetime-null)';
				}
			}
			return '';
		};
		$suggestFloat    = function($col) {
			if (strpos($col['Type'], 'float') !== false) {
				return '(Float-set)';
			}
			return '';
		};

		$formatTables = [];
		foreach ($tables as $table) {
			$columns       = \DB::select('show full columns from ' . $table);
			$formatColumns = [];
			/*
			 * column 字段
			 * Field      : account_no
		     * Type       : varchar(100)
		     * Collation  : utf8_general_ci
		     * Null       : NO
		     * Key        : ""
		     * Default    : ""
		     * Extra      : ""
		     * Privileges : select,insert,update,references
		     * Comment    : 账号
			 * ---------------------------------------- */

			foreach ($columns as $column) {
				$column            = (array) $column;
				$column['suggest'] =
					$suggestString($column) .
					$suggestInt($column) .
					$suggestDecimal($column) .
					$suggestDatetime($column);
				$suggestFloat($column);
				$formatColumns[$column['Field']] = $column;
			}
			$formatTables[$table] = $formatColumns;
		}
		return view('system::develop.env.db', [
			'items' => $formatTables,
		]);
	}

}
