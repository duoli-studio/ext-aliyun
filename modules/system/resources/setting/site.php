<?php

use \App\Lemon\Repositories\System\SysKernel;

/**
 * 根据 type/input_type 生成输入框, 默认是input 输入框, 同时根据type来获取验证类型, 所有类型不是必选输入, 因为存在默认值
 * 因为是设置项. 通过统一的入口来进入.
 * 能否通过 config入口进入?
 * 所有的配置项目不允许返回 null
 * 'default' // 默认值
 * 'title'   // 左侧标题说明
 * 'tips'     // 描述
 */

return [
	/*
	|--------------------------------------------------------------------------
	| 私有的配置使用 '_' 开头
	| 'sample' => [
	|	 'title'           => '网站控制',
	|	 'first_col_class' => 'w240',
	| ],
	|--------------------------------------------------------------------------
	*/
	'_groups'                       => [

		'site'     => [
			'title' => '网站控制',
		],
		'platform' => [
			'title' => '代练王',
		],
		'desktop'  => [
			'title' => '管理后台',
		],
		'sms'      => [
			'title' => '短信',
		],
	],
	'site_name'                     => [
		'form_type'    => SysKernel::FORM_TYPE_TEXT,
		'title'        => '网站名称',
		'validator'    => 'required',
		'group'        => 'site',
		'form_options' => [
			'class' => 'form-control',
		],
	],
	'open_register'                 => [
		'form_type'                   => SysKernel::FORM_TYPE_SINGLE_SELECT,
		'title'                       => '开启用户注册',
		'single_select_default_value' => 1,
		'single_select_options'       => [
			1 => '是',
			0 => '否',
		],
		'group'                       => 'site',
		'form_options'                => [
			'inline' => true,
		],
	],
	'open_register_off_description' => [
		'form_type'    => SysKernel::FORM_TYPE_TEXTAREA,
		'title'        => '关闭注册提示',
		'group'        => 'site',
		'form_options' => [
			'cols'  => 30,
			'rows'  => 5,
			'class' => 'form-control',
		],
	],
	'is_open'                       => [
		'form_type'                   => SysKernel::FORM_TYPE_SINGLE_SELECT,
		'title'                       => '站点开启',
		'single_select_default_value' => 0,
		'single_select_options'       => [
			1 => '是',
			0 => '否',
		],
		'group'                       => 'site',
		'form_options'                => [
			'inline' => true,
		],
	],
	'close_reason'                  => [
		'form_type'    => SysKernel::FORM_TYPE_TEXTAREA,
		'title'        => '站点关闭原因',
		'group'        => 'site',
		'form_options' => [
			'cols'  => 30,
			'rows'  => 5,
			'class' => 'form-control',
		],
	],
	'copyright'                     => [
		'form_type'    => SysKernel::FORM_TYPE_TEXTAREA,
		'title'        => '版权信息',
		'group'        => 'site',
		'form_options' => [
			'cols'  => 30,
			'rows'  => 5,
			'class' => 'form-control',
		],
	],

	/*
	|--------------------------------------------------------------------------
	| 代练王平台订单
	|--------------------------------------------------------------------------
	|
	*/
	'pt_detail_sync_interval'       => [
		'form_type'    => SysKernel::FORM_TYPE_TEXT,
		'title'        => '详情同步间隔(分钟)',
		'validator'    => 'required|digits_between:1,100',
		'group'        => 'platform',
		'form_options' => [
			'class' => 'form-control',
		],
	],
	'pt_detail_sync_amount'         => [
		'form_type'    => SysKernel::FORM_TYPE_TEXT,
		'title'        => '详情同步数量(单位)',
		'validator'    => 'required|digits_between:1,3000',
		'group'        => 'platform',
		'form_options' => [
			'class' => 'form-control',
		],
	],
	'pt_re_publish_interval'        => [
		'form_type'    => SysKernel::FORM_TYPE_TEXT,
		'title'        => '重新发单间隔(分钟)',
		'validator'    => 'required|digits_between:1,100',
		'group'        => 'platform',
		'form_options' => [
			'class' => 'form-control',
		],
	],
	'pt_re_publish_amount'          => [
		'form_type'    => SysKernel::FORM_TYPE_TEXT,
		'title'        => '重新发单数量(单位)',
		'validator'    => 'required|digits_between:1,300',
		'group'        => 'platform',
		'form_options' => [
			'class' => 'form-control',
		],
	],
	'pt_default_order_content'      => [
		'form_type'    => SysKernel::FORM_TYPE_TEXTAREA,
		'title'        => '代练要求(MaMa/1dailian)',
		'group'        => 'platform',
		'tips'         => '默认填写在发单时候的代练内容/要求项目中',
		'form_options' => [
			'cols'  => 30,
			'rows'  => 5,
			'class' => 'form-control',
		],
	],
	'pt_tong_order_content'         => [
		'form_type'    => SysKernel::FORM_TYPE_TEXTAREA,
		'title'        => '代练通要求(Tong)',
		'group'        => 'platform',
		'tips'         => '代练通的代练要求',
		'form_options' => [
			'cols'  => 30,
			'rows'  => 5,
			'class' => 'form-control',
		],
	],
	'pt_default_order_url'          => [
		'form_type'    => SysKernel::FORM_TYPE_TEXT,
		'title'        => '易代练地址',
		'group'        => 'platform',
		'validator'    => 'required',
		'form_options' => [
			'class' => 'form-control',
		],
	],
	'pt_default_baozi_url'          => [
		'form_type'    => SysKernel::FORM_TYPE_TEXT,
		'title'        => '代练包子地址',
		'group'        => 'platform',
		'validator'    => 'required',
		'form_options' => [
			'class' => 'form-control',
		],
	],
	'pt_default_yq_url'          => [
		'form_type'    => SysKernel::FORM_TYPE_TEXT,
		'title'        => '17代练地址',
		'group'        => 'platform',
		'validator'    => 'required',
		'form_options' => [
			'class' => 'form-control',
		],
	],
	'pt_employee_id'                => [
		'form_type'    => SysKernel::FORM_TYPE_TEXT,
		'title'        => '员工ID设置',
		'validator'    => 'required|digits_between:1,3000',
		'group'        => 'platform',
		'form_options' => [
			'class' => 'form-control',
		],
	],

	'pt_input_timeout'      => [
		'form_type'    => SysKernel::FORM_TYPE_TEXTAREA,
		'title'        => '录单未接手',
		'group'        => 'platform',
		'tips'         => '使用 3|3小时; 这种格式, 每行一条',
		'form_options' => [
			'cols'  => 30,
			'rows'  => 5,
			'class' => 'form-control',
		],
	],
	'pt_ing_timeout'        => [
		'form_type'    => SysKernel::FORM_TYPE_TEXTAREA,
		'title'        => '订单超时',
		'group'        => 'platform',
		'tips'         => '使用 3|3小时; 这种格式, 每行一条',
		'form_options' => [
			'cols'  => 30,
			'rows'  => 5,
			'class' => 'form-control',
		],
	],
	'pt_overtime_add_money' => [
		'form_type'    => SysKernel::FORM_TYPE_TEXTAREA,
		'title'        => '订单超时加钱',
		'group'        => 'platform',
		'tips'         => '使用 3|3;  这种格式, 每行一条,代表超时小时数与增加的钱数',
		'form_options' => [
			'cols'  => 30,
			'rows'  => 5,
			'class' => 'form-control',
		],
	],

	/*
	|--------------------------------------------------------------------------
	| 后台启用配置
	|--------------------------------------------------------------------------
	|
	*/
	'dsk_enable_login_ip'   => [
		'form_type'                   => SysKernel::FORM_TYPE_SINGLE_SELECT,
		'title'                       => '后台启用IP控制',
		'single_select_default_value' => 'N',
		'single_select_options'       => [
			'N' => '否',
			'Y' => '是',
		],
		'group'                       => 'desktop',
		'form_options'                => [
			'inline' => true,
		],
	],

	/*
	|--------------------------------------------------------------------------
	| 后台启用配置
	|--------------------------------------------------------------------------
	|
	*/
	'sms_order_over'        => [
		'form_type'    => SysKernel::FORM_TYPE_TEXTAREA,
		'title'        => '订单完成(模板需要和Ihuyi相同, 否则发送不出去)',
		'group'        => 'sms',
		'form_options' => [
			'class' => 'form-control',
			'rows'  => 3,
		],
	],
	'sms_order_handle'      => [
		'form_type'    => SysKernel::FORM_TYPE_TEXTAREA,
		'title'        => '接单订单(模板需要和Ihuyi相同, 否则发送不出去)',
		'group'        => 'sms',
		'form_options' => [
			'class' => 'form-control',
			'rows'  => 3,
		],
	],
	/*
		|--------------------------------------------------------------------------
		| 价格计算
		|--------------------------------------------------------------------------
		|
		*/

	'0_20'     => [
		'form_type'    => SysKernel::FORM_TYPE_TEXT,
		'title'        => '0到20胜点(%)',
		'group'        => 'dan_discount',
		'form_options' => [
			'class' => 'form-control w96',
		],
	],
	'21_30'    => [
		'form_type'    => SysKernel::FORM_TYPE_TEXT,
		'title'        => '21到30胜点(%)',
		'validator'    => 'digits_between:1,3000',
		'group'        => 'dan_discount',
		'form_options' => [
			'class' => 'form-control w96',
		],
	],
	'31_50'    => [
		'form_type'    => SysKernel::FORM_TYPE_TEXT,
		'title'        => '31到50胜点(%)',
		'validator'    => 'digits_between:1,3000',
		'group'        => 'dan_discount',
		'form_options' => [
			'class' => 'form-control w96',
		],
	],
	'51_75'    => [
		'form_type'    => SysKernel::FORM_TYPE_TEXT,
		'title'        => '51到75胜点(%)',
		'validator'    => 'digits_between:1,3000',
		'group'        => 'dan_discount',
		'form_options' => [
			'class' => 'form-control w96',
		],
	],
	'76_99'    => [
		'form_type'    => SysKernel::FORM_TYPE_TEXT,
		'title'        => '76到99胜点(%)',
		'validator'    => 'digits_between:1,1000',
		'group'        => 'dan_discount',
		'form_options' => [
			'class' => 'form-control w96',
		],
	],
	'100'      => [
		'form_type'    => SysKernel::FORM_TYPE_TEXT,
		'title'        => '100胜点(%)',
		'validator'    => 'digits_between:1,1000',
		'group'        => 'dan_discount',
		'form_options' => [
			'class' => 'form-control w96',
		],
	],
	'five_dan' => [
		'form_type'    => SysKernel::FORM_TYPE_TEXT,
		'title'        => '超过5个段位自动折扣(%)',
		'validator'    => 'digits_between:1,1000',
		'group'        => 'dan_discount',
		'form_options' => [
			'class' => 'form-control w96',
		],
	],

];