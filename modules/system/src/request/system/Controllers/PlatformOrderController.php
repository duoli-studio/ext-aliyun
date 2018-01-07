<?php namespace System\Request\Backend\Controllers;

use App\Lemon\Dailian\Action\ActionAliOss;
use App\Lemon\Dailian\Action\ActionPlatformOrder;
use App\Lemon\Dailian\Application\App\AppWeb;
use App\Lemon\Dailian\Application\Lol\Price;
use App\Lemon\Dailian\Application\Platform\Factory\PlatformFactory;
use App\Lemon\Dailian\Application\Platform\Mama;
use App\Lemon\Dailian\Application\Platform\Mao;
use App\Lemon\Dailian\Application\Platform\Tong;
use App\Lemon\Dailian\Application\Platform\Yi;
use App\Lemon\Dailian\Application\Platform\Yq;
use App\Lemon\Dailian\Application\Platform\Baozi;
use App\Lemon\Repositories\Exceptions\PolicyException;
use App\Lemon\Repositories\Sour\LmEnv;
use App\Lemon\Repositories\Sour\LmFile;
use App\Lemon\Repositories\Sour\LmStr;
use App\Lemon\Repositories\Sour\LmTime;
use App\Lemon\Repositories\System\SysSearch;
use App\Models\BaseConfig;
use App\Models\GameName;
use App\Models\GameServer;
use App\Models\GameSource;
use App\Models\GameType;
use App\Models\PamAccount;
use App\Models\PamRoleAccount;
use App\Models\PlatformAccount;
use App\Models\PlatformBind;
use App\Models\PlatformExport;
use App\Models\PlatformLog;
use App\Models\PlatformMoney;
use App\Models\PlatformOrder;
use App\Models\PlatformStatus;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * 平台订单
 * Class PlatformOrderController
 * @package App\Http\Controllers\Desktop
 */
class PlatformOrderController extends InitController
{
	use AuthorizesRequests;

	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->middleware('lm_desktop.auth');
	}

	/**
	 * Display a listing of the resource.
	 * @param Request $request
	 * @return \Response
	 */
	public function getIndex(Request $request)
	{
		$days = [
			'2'  => '两天内',
			'7'  => '一周内',
			'10' => '十天内',
			'-'  => '超时',
		];

		$pay_status = [
			'Y' => '是',
			'N' => '否',
		];

		$fields = [
			'game_account'        => '游戏账号',
			'order_id'            => '订单ID',
			'order_title'         => '订单标题',
			'game_actor'          => '游戏角色',
			'order_qq'            => '订单QQ',
			'order_mobile'        => '订单手机号',
			'order_get_in_number' => '接单单号',
			'order_get_in_mobile' => '接单手机号',
		];

		/** @type PlatformOrder $Db */
		$Db = PlatformOrder::whereRaw('1=1');
		// ->where('is_re_publish', 0);   // 重发的单子不予显示
		$kw    = trim($request->input('kw'));
		$field = trim($request->input('field'));
		if ($field && isset($fields[$field]) && $kw) {
			$Db->where($field, 'like', '%' . $kw . '%');
		}

		// urgency @ 紧急订单
		$is_urgency = $request->input('is_urgency');
		if ($is_urgency) {
			$Db->where('is_urgency', 1);
		}
		// renew @ 续单
		$is_renew = $request->input('is_renew');
		if ($is_renew) {
			$Db->where('is_renew', 1);
		}

		$order_status = $request->input('order_status');
		if ($order_status) {
			// 撤销中
			if ($order_status == PlatformOrder::ORDER_STATUS_CANCEL) {
				$Db->where('cancel_status', PlatformOrder::CANCEL_STATUS_ING);
				$Db->where('order_status', PlatformOrder::ORDER_STATUS_CANCEL);
			} else if ($order_status == PlatformOrder::ORDER_STATUS_QUASH) {
				//撤销完成
				$Db->where('order_status', PlatformOrder::ORDER_STATUS_CANCEL);
				$Db->where('cancel_status', PlatformOrder::CANCEL_STATUS_DONE);
			} else {
				$Db->where('order_status', $order_status);
			}
		}
		else {
			$Db->where('order_status', '!=', PlatformOrder::ORDER_STATUS_DELETE);
		}

		$game_id = (int) $request->input('game_id');
		if ($game_id) {
			$Db->where('game_id', $game_id);
		}

		$server_id = $request->input('server_id');
		if ($server_id) {
			$Db->where('server_id', $server_id);
		}

		$source_id = $request->input('source_id');
		if ($source_id) {
			$Db->where('source_id', $source_id);
		}

		$type_id = intval($request->input('type_id'));
		if ($type_id) {
			$Db->where('type_id', $type_id);
		}


		// 撤单类型
		$cancel_custom = $request->input('cancel_custom');
		if ($cancel_custom) {
			$Db->where('order_status', PlatformOrder::ORDER_STATUS_CANCEL);

			// 协商
			if (
				$cancel_custom == PlatformOrder::CANCEL_CUSTOM_DEAL_ING ||
				$cancel_custom == PlatformOrder::CANCEL_CUSTOM_DEAL_OVER
			) {
				$Db->whereIn('cancel_type', [
					PlatformOrder::CANCEL_TYPE_DEAL,
					PlatformOrder::CANCEL_TYPE_SD_DEAL,
					PlatformOrder::CANCEL_TYPE_PUB_DEAL,
				]);
				if ($cancel_custom == PlatformOrder::CANCEL_CUSTOM_DEAL_ING) {
					$Db->where('cancel_status', PlatformOrder::CANCEL_STATUS_ING);
				}
				if ($cancel_custom == PlatformOrder::CANCEL_CUSTOM_DEAL_OVER) {
					$Db->where('cancel_status', PlatformOrder::CANCEL_STATUS_DONE);
				}
			}

			// 客服
			if (
				$cancel_custom == PlatformOrder::CANCEL_CUSTOM_KF_DEAL_IN ||
				$cancel_custom == PlatformOrder::CANCEL_CUSTOM_KF_DEAL_OVER ||
				$cancel_custom == PlatformOrder::CANCEL_CUSTOM_KF_DEAL_FORCE
			) {
				$Db->where('cancel_type', PlatformOrder::CANCEL_TYPE_KF);
				if ($cancel_custom == PlatformOrder::CANCEL_CUSTOM_KF_DEAL_IN) {
					$Db->whereIn('kf_status', [
							PlatformOrder::KF_STATUS_ING,
							PlatformOrder::KF_STATUS_WAIT,
						]
					);
				}
				if ($cancel_custom == PlatformOrder::CANCEL_CUSTOM_KF_DEAL_FORCE) {
					$Db->where('kf_status', PlatformOrder::KF_STATUS_DONE);
				}
			}
		}
		// 客服状态
		$kf_status = $request->input('kf_status');
		if ($kf_status) {
			$Db->where('kf_status', $kf_status);
		}

		$publish_account_name = $request->input('publish_account_name');
		if ($publish_account_name) {
			$Db->where('publish_account_name', 'like', '%' . $publish_account_name . '%');
		}
		$order_tag_note = $request->input('order_tag_note');
		if ($order_tag_note) {
			$Db->where('order_tag_note', 'like', '%' . $order_tag_note . '%');
		}
		$actor_tag_note = $request->input('actor_tag_note');
		if ($actor_tag_note) {
			$Db->where('actor_tag_note', 'like', '%' . $actor_tag_note . '%');
		}

		$period = $request->input('period');
		if ($period) {
			if (is_numeric($period)) {
				$time = intval($period) * 24 * 3600 + LmEnv::time();
				$end  = Carbon::createFromTimestamp($time)->toDateTimeString();
				$Db->where('ended_at', '<', $end);
				$Db->where('ended_at', '>', Carbon::now());
			} else {
				$Db->where('ended_at', '>', Carbon::now());
			}
		}

		// 录单时间
		$input_start_date = $request->input('input_start_date');
		if ($input_start_date) {
			$Db->where('created_at', '>=', LmTime::dayStart($input_start_date));
		}
		$input_end_date = $request->input('input_end_date');
		if ($input_end_date) {
			$Db->where('created_at', '<', LmTime::dayEnd($input_end_date));
		}
		if ($input_start_date || $input_end_date) {
			$Db->whereNotNull('created_at');
		}

		// 发单时间
		$publish_start_date = $request->input('publish_start_date');
		if ($publish_start_date) {
			$Db->where('published_at', '>=', LmTime::dayStart($publish_start_date));
		}
		$publish_end_date = $request->input('publish_end_date');
		if ($publish_end_date) {
			$Db->where('published_at', '<', LmTime::dayEnd($publish_end_date));
		}
		if ($publish_start_date || $publish_end_date) {
			$Db->whereNotNull('published_at');
		}

		/* 合作方全文检索 [(Mark Zhao) 16-7-3]
		$publish_account = $request->input('publish_account');
		if ($publish_account) {
			$order->whereRaw(\DB::raw('match (publish_account) against (? IN BOOLEAN MODE)'), [',_' . $publish_account . '_,']);
		}
		----------- */

		// 员工单
		$is_employee = $request->input('is_employee');
		if ($is_employee) {
			//员工订单
			$employee_id = $request->input('employee_id');
			if ($employee_id) {
				$Db->where('employee', $employee_id);
			}
			$Db->where('employee_publish', 1);
			$account_ids   = PamRoleAccount::where('role_id', site('pt_employee_id'))->lists('account_id');
			$employee_list = PamAccount::whereIn('account_id', $account_ids)->lists('account_name', 'account_id');
			\View::share([
				'employee_list' => $employee_list,
			]);
		}

		// 发单
		$is_publish = $request->input('is_publish');
		if ($is_publish) {
			$Db->where('employee_publish', 0);
		}

		// 合作方检索
		$accept_platform_account_id = $request->input('accept_platform_account_id');
		if ($accept_platform_account_id) {
			$Db->where('accept_platform_account_id', $accept_platform_account_id);
		}

		$is_question = $request->input('is_question');
		if ($is_question) {
			$Db->where('is_question', 1);
			$question_type = $request->input('question_type');
			if ($question_type) {
				$Db->where('question_type', $question_type);
			}
			$question_handle_account_id = $request->input('question_handle_account_id');
			if ($question_handle_account_id) {
				$Db->where('question_handle_account_id', $question_handle_account_id);
			}
			$question_status = $request->input('question_status');
			if ($question_status) {
				$Db->where('question_status', $question_status);
			}
		}

		// 发单未接手
		$is_input_timeout = $request->input('is_input_timeout');
		if ($is_input_timeout) {
			$input_timeouts = LmStr::parseKey(site('pt_input_timeout'));
			\View::share([
				'input_timeouts' => $input_timeouts,
			]);
			// 已发布
			$Db->where('order_status', PlatformOrder::ORDER_STATUS_PUBLISH);
			$input_timeout = $request->input('input_timeout');

			// 是否在范围之内
			$in = true;
			if (strpos($input_timeout, '+') !== false) {
				$in = false;
			}
			$input_timeout = (int) $input_timeout;
			if ($input_timeout) {
				$itCarbon = Carbon::now()->subHour($input_timeout);
				if ($in) {
					$Db->where('created_at', '>', $itCarbon);
				} else {
					$Db->where('created_at', '<', $itCarbon);
				}

			}
		}

		// 录单未接手
		$is_ing_timeout = $request->input('is_ing_timeout');
		if ($is_ing_timeout) {
			$ing_timeouts = LmStr::parseKey(site('pt_ing_timeout'));
			\View::share([
				'ing_timeouts' => $ing_timeouts,
			]);
			// 已发布
			$Db->where('order_status', PlatformOrder::ORDER_STATUS_ING);   //代练进行中

			$ing_timeout = $request->input('ing_timeout');
			// 是否在范围之内
			$in = true;
			if (strpos($ing_timeout, '+') !== false) {
				$in = false;
			}
			$ing_timeout = (int) $ing_timeout;
			if ($ing_timeout) {
				$itCarbon = Carbon::now()->subHour($ing_timeout);
				// 应该完成的时间点
				if ($in) {
					$Db->whereRaw(\DB::raw('addtime(assigned_at, order_hours*60*60*60) > "' . $itCarbon . '"'));
				} else {
					$Db->whereRaw(\DB::raw('addtime(assigned_at, order_hours*60*60*60) < "' . $itCarbon . '"'));
				}
			}
		}
		$is_tgp_question = $request->input('is_tgp_question');
		if ($is_tgp_question) {
			$Db->where('is_tgp_question', 1);
		}

		$export = $request->input('export');
		// 导出不进行额外查询
		if (!$export) {
			$Db->with('platformStatus');
			$Db->with('platformAccept');
			$Db->with('platformAccount');
			$Db->with('ePam');
		}
		$orderKey = SysSearch::key('order_id', [
			'created_at', 'published_at', 'order_left_hours', 'question_at', 'tgp',
		]);
		if ($orderKey == 'tgp') {
			$Db->orderByRaw('(tgp_win/tgp_num) ' . SysSearch::order());
		} else {
			$Db->orderBy($orderKey, SysSearch::order());
		}

		$items = $Db->paginate($this->pagesize);
		$items->appends($request->input());
		$handleAccount = PamAccount::where('account_type', PamAccount::ACCOUNT_TYPE_DESKTOP)->lists('account_name', 'account_id');

		if ($export) {
			$exports = [
				'order_id'                   => '订单id',
				'order_get_in_number'        => '订单编号',
				'order_title'                => '订单标题',
				'created_at'                 => '录单时间',
				'published_at'               => '发布时间',
				'order_status'               => [
					'订单状态',
					'App\Models\PlatformOrder::kvOrderStatus',
				],
				'accept_platform_account_id' => [
					'合作方',
					'App\Models\PlatformAccount::getNote',
				],
				'game_account'               => '游戏账号',
				'game_pwd'                   => '游戏密码',
				'game_actor'                 => '角色名',
				'order_get_in_price'         => '接单价格',
				'order_price'                => '发单价格',
				'server_big_title'           => '服务器大区',
				'game_area'                  => '服务器',
				'order_get_in_wangwang'      => '旺旺号',
				'order_get_in_mobile'        => '联系电话',
				'ended_at'                   => '完成时间',
			];

			if (!acl($this->pam->account_id, 'export_password')) {
				unset($exports['game_pwd']);
			}
			// create director
			$dir      = 'excel/' . Carbon::now()->format('Ym');
			$filename = $this->pam->account_name . '-' . Carbon::now()->format('m-d_H-i') . '-' . str_random(6);
			$disk     = \Storage::disk('storage');
			$disk->makeDirectory($dir);
			$path = $dir . '/' . $filename;
			PlatformExport::create([
				'account_id'   => $this->pam->account_id,
				'storage_path' => $path . '.xls',
				'storage_type' => 'xls',
			]);
			LmFile::exportPaginate($items, $exports, $path, true);
		}

		$orderStatusNums = PlatformOrder::selectRaw(\DB::raw('count(order_id) as co, order_status'))->groupBy('order_status')->lists('co', 'order_status');
		$orderIngNums    = PlatformOrder::selectRaw(\DB::raw('count(order_id) as co, employee_publish'))
			->where('order_status', PlatformOrder::ORDER_STATUS_ING)
			->groupBy('employee_publish')->lists('co', 'employee_publish');
		$orderCancelNum  = PlatformOrder::where('cancel_status', PlatformOrder::CANCEL_STATUS_ING)->where('order_status', PlatformOrder::ORDER_STATUS_CANCEL)->count('order_id');
		$orderQuashlNum  = PlatformOrder::where('cancel_status', PlatformOrder::CANCEL_STATUS_DONE)->where('order_status', PlatformOrder::ORDER_STATUS_CANCEL)->count('order_id');
		return view('desktop.platform_order.index',  [
			'items'             => $items,
			'days'              => $days,
			'pay_status'        => $pay_status,
			'fields'            => $fields,
			'handle_account'    => $handleAccount,
			'order_status_nums' => $orderStatusNums,
			'order_ing_nums'    => $orderIngNums,
			'order_cancel_num'  => $orderCancelNum,
			'order_quash_num'   => $orderQuashlNum,
		]);
	}

	public function getCreate()
	{
		$defaultOrderContent = site('pt_default_order_content');
		return view('desktop.platform_order.item', [
			'default_order_content' => $defaultOrderContent,
		]);
	}

	public function postCreate(Request $request)
	{
		$input = $request->except('_token', '_method');
		$file = $request->file('enclosure');
		if ($file) {
			$Image = new ActionAliOss();
			if ($Image->saveFile($file)) {
				$input['enclosure'] = $Image->getUrl();
			}
		}

		$validator = \Validator::make($input, [
			'order_price'     => 'numeric|min:0|max:800',
			'order_content'   => 'between:5,600',
			'order_add_price' => 'numeric',
		]);

		if ($validator->fails()) {
			return AppWeb::resp(AppWeb::ERROR, $validator->errors());
		}

		// 来源
		$input['source_id']    = isset($input['source_id']) ? intval($input['source_id']) : 0;
		$input['source_title'] = GameSource::kvSourceTitle($input['source_id']);

		// 游戏区服处理
		$game_id = (int) $input['game_id'];
		if (!$game_id) {
			return AppWeb::resp(AppWeb::ERROR, '请选择游戏', null, $input);
		}
		if ($input['server_id']) {
			if (GameServer::isTop($input['server_id'])) {
				return AppWeb::resp(AppWeb::ERROR, '请选择服务器, 不要选择游戏大区', null, $input);
			}
		} else {
			return AppWeb::resp(AppWeb::ERROR, '请选择服务器', null, $input);
		}

		$input['game_area']        = GameServer::getLinkageName($input['server_id']);
		$gameArea                  = explode('/', $input['game_area']);
		$input['server_big_title'] = isset($gameArea[0]) ? $gameArea[0] : '';
		$input['server_title']     = isset($gameArea[1]) ? $gameArea[1] : '';

		// 游戏类型
		$input['type_id'] = isset($input['type_id']) ? intval($input['type_id']) : 0;
		if ($input['type_id']) {
			$input['type_title'] = GameType::kvTypeTitle($game_id, $input['type_id']);
		}

		// 额外信息
		if (isset($input['order_tags']) && $input['order_tags']) {
			$input['order_tags'] = LmStr::matchEncode($input['order_tags']);
		}

		$Order = new ActionPlatformOrder();
		if ($Order->create($input, $this->desktop)) {
			return AppWeb::resp(AppWeb::SUCCESS, '订单创建成功', 'location|' . route('dsk_platform_order.create'));
		} else {
			return AppWeb::resp(AppWeb::ERROR, $Order->getError(), null, $input);
		}
	}


	public function getEdit($id)
	{
		$item             = PlatformOrder::find($id);
		$item->order_tags = LmStr::matchDecode($item->order_tags);
		return view('desktop.platform_order.item', [
			'item'    => $item,
			'games'   => GameName::kvLinear(),
			'sources' => GameSource::kvSourceTitle(),
		]);
	}


	public function postEdit(Request $request, $id)
	{
		$input = $request->except(['_token']);
		$file  = $request->file('enclosure');
		if ($file) {
			$Image = new ActionAliOss();
			if ($Image->saveFile($file)) {
				$input['enclosure'] = $Image->getUrl();
			}
		}

		if ($input['server_id']) {
			if (GameServer::isTop($input['server_id'])) {
				return AppWeb::resp(AppWeb::ERROR, '请选择服务器, 不要选择游戏大区', null, $input);
			}
		} else {
			return AppWeb::resp(AppWeb::ERROR, '请选择服务器', null, $input);
		}

		$input['game_area'] = GameServer::getLinkageName($input['server_id']);

		if (isset($input['order_tags']) && $input['order_tags']) {
			$input['order_tags'] = LmStr::matchEncode($input['order_tags']);
		}
		$Platform = new ActionPlatformOrder($id);
		$Platform->edit($input, $this->desktop);
		return AppWeb::resp(AppWeb::SUCCESS, '订单编辑成功', 'location|' . route('dsk_platform_order.index'));
	}


	/**
	 * 撤单后重新发布
	 * @param $id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postOrderRePublish($id)
	{
		// 重新创建订单
		$Platform = new ActionPlatformOrder($id);
		$Platform->orderRePublish($this->pam);
		return AppWeb::resp(AppWeb::SUCCESS, '已经重新创建订单', 'reload|1');
	}

	public function postRePublish($id)
	{
		// 重新创建订单
		$Platform = new ActionPlatformOrder($id);
		$Platform->rePublish($this->pam);
		return AppWeb::resp(AppWeb::SUCCESS, '已经重新创建订单', 'reload|1');
	}

	/**
	 * 面板编辑
	 * @param Request $request
	 * @param         $id
	 * @return mixed
	 */
	public function postUpdate(Request $request, $id)
	{
		$input    = $request->except(['_token']);
		$Platform = new ActionPlatformOrder($id);
		if ($Platform->edit($input, $this->desktop)) {
			return AppWeb::resp(AppWeb::SUCCESS, '订单编辑成功', 'reload|1');
		} else {
			return AppWeb::resp(AppWeb::ERROR, $Platform->getError());
		}

	}

	/**
	 * 退款
	 * @param         $id
	 * @return mixed
	 */
	public function postRefund($id)
	{
		$order = PlatformOrder::find($id);
		if (!$order) {
			return AppWeb::resp(AppWeb::ERROR, '此订单不存在');
		}
		$items = PlatformStatus::where('order_id', $order->order_id)->get();

		//如果有发送的订单 进行删单操作
		if (is_array($items->toArray())) {
			foreach ($items as $item) {
				if ($item['mama_is_publish'] == 1 && $item['mama_is_delete'] != 1) {
					$yi = new Mama($item['pt_account_id'], $order, $this->pam);
					$yi->delete();
				}
				if ($item['mao_is_publish'] && $item['mao_is_delete'] != 1) {
					$yi = new Mao($item['pt_account_id'], $order, $this->pam);
					$yi->delete();
				}
				if ($item['yi_is_publish'] && $item['yi_is_delete'] != 1) {
					$yi = new Yi($item['pt_account_id'], $order, $this->pam);
					$yi->delete();
				}
				if ($item['baozi_is_publish'] && $item['baozi_is_delete'] != 1) {
					$baozi = new Baozi($item['pt_account_id'], $order, $this->pam);
					$baozi->delete();
				}
				if ($item['tong_is_publish'] && $item['tong_is_delete'] != 1) {
					$yi = new Tong($item['pt_account_id'], $order, $this->pam);
					$yi->delete();
				}
				if ($item['yq_is_publish'] && $item['yq_is_delete'] != 1) {
					$yq = new Yq($item['pt_account_id'], $order, $this->pam);
					$yq->delete();
				}

			}
		}
		$this->policy('refund', $order);
		$Platform = new ActionPlatformOrder($id);

		$Platform->refund($this->desktop);
		return AppWeb::resp(AppWeb::SUCCESS, '订单已经操作退款', 'reload|1');
	}


	/**
	 * 选择发布的用户
	 * @param $order_id
	 * @return mixed
	 */
	public function getBatchPublish($order_id)
	{
		/** @type PlatformOrder $order */
		$order = PlatformOrder::with('platformStatus')->find($order_id);
		// - 已经接单
		$assign['order'] = $order;
		if ($order->accept_id) {
			$assign['accept']         = true;
			$assign['accept_account'] = PlatformAccount::find($order->accept_platform_account_id);
		} else {
			$assign['accept'] = false;
		}
		// 显示当前的接单账号
		/** @type Collection $accountIds */
		$accountIds = PlatformBind::where('account_id', $this->pam->account_id)->lists('platform_account_id');

		$hasAccount       = false;
		$platformAccounts = new Collection();
		if (!$accountIds->isEmpty()) {
			$hasAccount = true;
			/** @type Collection $platformAccounts */
			$platformAccounts = PlatformAccount::whereIn('id', $accountIds)->get();
		}

		/* 未发布成功的提示  ----------- */
		$publishReason = $order->publish_reason ? unserialize($order->publish_reason) : [];
		$accountIds->each(function($account_id) use (& $publishReason) {
			if (!isset($publishReason[$account_id . '_error'])) {
				$publishReason[$account_id . '_error'] = '';
			}
			if (!isset($publishReason[$account_id . '_success'])) {
				$publishReason[$account_id . '_success'] = '';
			}
		});
		$order->publish_reason = serialize($publishReason);
		$order->save();
		$assign['un_accept_has_account']       = $hasAccount;
		$assign['un_accept_platform_accounts'] = $platformAccounts;
		$assign['un_accept_publish_reason']    = $publishReason;

		/* 获取已经发单  ----------- */
		/** @type Collection $colPlatformStatus */
		$colPlatformStatus = $order->platformStatus;
		$arrPlatformStatus = [];
		if (!$colPlatformStatus->isEmpty()) {
			$colPlatformStatus->each(function($item) use (& $arrPlatformStatus) {
				$arrPlatformStatus[$item->pt_account_id] = $item;
			});
		}
		$assign['platform_status'] = $arrPlatformStatus;

		return view('desktop.platform_order.publish', $assign);
	}

	/**
	 * 将某个订单分配到指定的用户
	 * @param $order_id
	 * @param $account_id
	 * @return bool|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postAssignPublish($order_id, $account_id)
	{
		$platformAccount = PlatformAccount::getCacheItem($account_id);
		$platformOrder   = PlatformOrder::find($order_id);
		switch ($platformAccount->platform) {
			case PlatformAccount::PLATFORM_YI:
				$yi = new Yi($platformAccount->id, $platformOrder, $this->pam);
				if ($yi->publish()) {
					$success = '发布到易代练平台成功';
					PlatformOrder::logPublishReason($account_id, 'success', $success, $platformOrder);
					return AppWeb::resp(AppWeb::SUCCESS, $success, 'reload|1');
				} else {
					$error = $yi->getError();
					PlatformOrder::logPublishReason($account_id, 'error', $error, $platformOrder);
					return AppWeb::resp(AppWeb::ERROR, $error, 'reload|1');
				}
				break;
			case PlatformAccount::PLATFORM_BAOZI:
				$baozi = new Baozi($platformAccount->id, $platformOrder, $this->pam);
				if ($baozi->publish()) {
					$success = '发布到电竞包子平台成功';
					PlatformOrder::logPublishReason($account_id, 'success', $success, $platformOrder);
					return AppWeb::resp(AppWeb::SUCCESS, $success, 'reload|1');
				} else {
					$error = $baozi->getError();
					PlatformOrder::logPublishReason($account_id, 'error', $error, $platformOrder);
					return AppWeb::resp(AppWeb::ERROR, $error, 'reload|1');
				}
				break;
			case PlatformAccount::PLATFORM_MAO:
				$mao = new Mao($platformAccount->id, $platformOrder, $this->pam);
				if ($mao->publish()) {
					$success = '发布到代练猫平台成功';
					PlatformOrder::logPublishReason($account_id, 'success', $success, $platformOrder);
					return AppWeb::resp(AppWeb::SUCCESS, $success, 'reload|1');
				} else {
					$error = $mao->getError();
					PlatformOrder::logPublishReason($account_id, 'error', $error, $platformOrder);
					return AppWeb::resp(AppWeb::ERROR, $mao->getError(), 'reload|1');
				}
				break;
			case PlatformAccount::PLATFORM_MAMA:
				$mama = new Mama($platformAccount->id, $platformOrder, $this->pam);
				if ($mama->publish()) {
					$success = '发布到代练妈妈平台成功';
					PlatformOrder::logPublishReason($account_id, 'success', $success, $platformOrder);
					return AppWeb::resp(AppWeb::SUCCESS, $success, 'reload|1');
				} else {
					$error = $mama->getError();
					PlatformOrder::logPublishReason($account_id, 'error', $error, $platformOrder);
					return AppWeb::resp(AppWeb::ERROR, $mama->getError(), 'reload|1');
				}
				break;
			case PlatformAccount::PLATFORM_TONG:
				$tong = new Tong($platformAccount->id, $platformOrder, $this->pam);
				if ($tong->publish()) {
					$success = '发布到代练通平台成功';
					PlatformOrder::logPublishReason($account_id, 'success', $success, $platformOrder);
					return AppWeb::resp(AppWeb::SUCCESS, $success, 'reload|1');
				} else {
					$error = $tong->getError();
					PlatformOrder::logPublishReason($account_id, 'error', $error, $platformOrder);
					return AppWeb::resp(AppWeb::ERROR, $error, 'reload|1');
				}
				break;
			case PlatformAccount::PLATFORM_YQ:
				$yq = new Yq($platformAccount->id, $platformOrder, $this->pam);
				if ($yq->publish()) {
					$success = '发布到17代练平台成功';
					PlatformOrder::logPublishReason($account_id, 'success', $success, $platformOrder);
					return AppWeb::resp(AppWeb::SUCCESS, $success, 'reload|1');
				}
				else {
					$error = $yq->getError();
					PlatformOrder::logPublishReason($account_id, 'error', $error, $platformOrder);
					return AppWeb::resp(AppWeb::ERROR, $error, 'reload|1');
				}
				break;
		}
		return false;
	}

	/**
	 * 批量发布
	 * @param Request $request
	 * @param         $order_id
	 * @return mixed
	 */
	public function postBatchPublish(Request $request, $order_id)
	{
		$account_id = $request->input('account_id');
		if (!$account_id) {
			return AppWeb::resp(AppWeb::ERROR, '请选择发布平台');
		} else {
			foreach ($account_id as $id) {
				$this->postAssignPublish($order_id, $id);
			}
			return AppWeb::resp(AppWeb::SUCCESS, '已经发送请求, 请查看发布状态', 'reload|1');
		}
	}

	/**
	 * 批量发布
	 * @param         $order_id
	 * @return mixed
	 */
	public function postBatchRePublish($order_id)
	{
		$Order = new ActionPlatformOrder($order_id);
		if (!$Order->batchRePublish()) {
			return AppWeb::resp(AppWeb::ERROR, $Order->getError());
		} else {
			return AppWeb::resp(AppWeb::SUCCESS, '重发成功', 'reload|1');
		}
	}

	/**
	 * 将订单发布给某个指定的员工
	 * @param $order_id
	 * @param $account_id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postAssignEmployee($order_id, $account_id)
	{
		$platformOrder = PlatformOrder::find($order_id);
		PlatformStatus::firstOrCreate([
			'order_id'      => $order_id,
			'platform'      => 'employee',
			'pt_account_id' => $account_id,
		]);
		$platformOrder->employee_publish = 1;
		$platformOrder->order_status     = PlatformOrder::ORDER_STATUS_PUBLISH;
		$platformOrder->published_at     = Carbon::now();
		$platformOrder->save();
		$success = '成功发布给员工';
		PlatformOrder::logPublishReason($account_id, 'success', $success, $platformOrder);
		return AppWeb::resp(AppWeb::SUCCESS, $success, 'reload|1');
	}

	/**
	 * 提交问题
	 * @param $order_id
	 * @return mixed
	 */
	public function getQuestion($order_id)
	{
		$order         = PlatformOrder::find($order_id);
		$handleAccount = PamAccount::where('account_type', PamAccount::ACCOUNT_TYPE_DESKTOP)->lists('account_name', 'account_id');
		return view('desktop.platform_order.question', [
			'order'          => $order,
			'handle_account' => $handleAccount,
		]);
	}

	/**
	 * 提交问题
	 * @param Request $request
	 * @param         $order_id
	 * @return mixed
	 */
	public function postQuestion(Request $request, $order_id)
	{
		$PlatformOrder        = new ActionPlatformOrder($order_id);
		$question_type        = $request->input('question_type');
		$handle_account_id    = $request->input('question_handle_account_id');
		$question_description = $request->input('question_description');
		$question_thumb       = $request->input('question_thumb');

		if ($PlatformOrder->question($question_type, $handle_account_id, $question_description, $question_thumb, $this->desktop)) {
			return AppWeb::resp(AppWeb::SUCCESS, '问题已经提交', 'reload_opener|workspace');
		} else {
			return AppWeb::resp(AppWeb::ERROR, $PlatformOrder->getError());
		}
	}

	public function getHandleQuestion($order_id)
	{
		$order = PlatformOrder::with('questionHandleAccount')
			->with('questionAccount')
			->find($order_id);
		return view('desktop.platform_order.handle_question', [
			'order' => $order,
		]);
	}

	/**
	 * 关闭问题
	 * @param Request $request
	 * @param         $order_id
	 * @return mixed
	 */
	public function postHandleQuestion(Request $request, $order_id)
	{
		$PlatformOrder   = new ActionPlatformOrder($order_id);
		$message         = $request->input('message');
		$question_status = $request->input('question_status');
		if ($PlatformOrder->handleQuestion($question_status, $message, $this->desktop)) {
			return AppWeb::resp(AppWeb::SUCCESS, '问题已经记录/处理', 'reload_opener|workspace');
		} else {
			return AppWeb::resp(AppWeb::ERROR, $PlatformOrder->getError());
		}
	}


	/**
	 * 导入订单到指定的字段
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postImport(Request $request)
	{
		$content    = $request->input('content');
		$arrContent = explode("\n", $content);
		$serverName = $this->parseImport($arrContent, '服务器');
		$gameSource = $this->parseImport($arrContent, '订单来源');
		$serverId   = 0;
		$gameId     = 0;
		$sourceId   = 0;

		if ($serverName) {
			$gameServer = GameServer::where('server_title', $serverName)->first();
			if ($gameServer) {
				$serverId = $gameServer->server_id;
				$gameId   = $gameServer->game_id;
			}
		}
		if ($gameSource) {
			$sourceId = GameSource::where('source_name', $gameSource)->value('source_id');
		}
		$return = [
			'server_id'             => $serverId,
			'game_id'               => $gameId,
			'game_area'             => $serverName,
			'game_actor'            => $this->parseImport($arrContent, '角色名'),
			'source_id'             => $sourceId,
			'order_note'            => $content,
			'order_title'           => $this->parseImport($arrContent, '代练内容'),
			'order_content'         => $this->parseImport($arrContent, '代练内容') . ' ' . $serverName,
			'order_get_in_mobile'   => $this->parseImport($arrContent, '联系电话'),
			'order_get_in_number'   => $this->parseImport($arrContent, '订单编号'),
			'order_get_in_price'    => $this->parseImport($arrContent, '拍单价格'),
			'order_get_in_wangwang' => $this->parseImport($arrContent, '旺旺昵称'),
			'order_qq'              => $this->parseImport($arrContent, '游戏账号'),
			'game_account'          => $this->parseImport($arrContent, '游戏账号'),
			'game_pwd'              => $this->parseImport($arrContent, '游戏密码'),
			'game_source'           => $gameSource,
		];
		try {
			//调取数据
			$game  = BaseConfig::getCache('game');
			$items = unserialize($game['price']);
			//取出代练内容
			$str = $this->parseImport($arrContent, '代练内容');
			//取出单双排信息
			preg_match_all('/【(.*?)】/', ($str), $result);
			if (!isset($result[1])) {
				return AppWeb::resp(AppWeb::SUCCESS, '解析成功!', $return);
			}
			foreach ($result[1] as $item) {
				if (isset(Price::$qualifying[$item])) {
					//排位信息
					$pai = Price::$qualifying[$item];
				}
				if (isset(Price::$type[$item])) {
					//优质或至尊
					$type = Price::$type[$item];
				}
			}

			if ($serverName == '艾欧尼亚') {
				$dan1 = 'telecom';
			} elseif ($serverName == '比尔吉沃特' || $serverName == '德玛西亚' || $serverName == '黑色玫瑰') {
				$dan1 = 'normal2';
			} else {
				$dan1 = 'normal';
			}

			if (isset($pai) && $pai == 'single_row') {
				$items = $items['single_double'];
			} elseif (isset($pai) && $pai == 'group') {
				$items = $items['flexible'];
			} else {
				return AppWeb::resp(AppWeb::SUCCESS, '解析成功!', $return);
			}

			preg_match_all("/定位赛/", $str, $location);
			// 如果是定位赛的话
			if (isset($location[0][0])) {
				//取出定位赛场数
				preg_match_all("/([0-9]*)场/", $str, $c_int);
				if (!isset($c_int[1][0])) {
					preg_match_all("/([0-9]*)把/", $str, $c_int);
				}
				if (!isset($c_int[1][0])) {
					return AppWeb::resp(AppWeb::SUCCESS, '解析成功!', $return);
				}

				//取出段位
				$dan = [];
				foreach (Price::$define_back as $k => $v) {
					preg_match_all('/' . $k . '/', $str, $du_an);
					if (isset($du_an['0']['0'])) {
						$dan = Price::$define_back[$du_an['0']['0']];
					}
				}
				if (!isset($dan)) {
					return AppWeb::resp(AppWeb::SUCCESS, '解析成功!', $return);
				}

				$c_int = $c_int[1][0];
				if (isset($type)) {
					$price = $items[$dan1 . '-' . $type];
				} else {
					$price = $items[$dan1]['location' . ':' . $dan];
				}
				//算出定位赛价格
				$result_price          = round($price / 10 * $c_int);
				$return['order_price'] = $result_price;
				return AppWeb::resp(AppWeb::SUCCESS, '解析成功!', $return);
			} else {
				// 取出段位信息，
				preg_match_all("/[^】]*[^x80-xff]{0,2}[0-9]-[^x80-xff]+[0-9]/", $str, $dan);
				if (!isset($dan[0][0])) {
					preg_match_all("/[^】^0-9]*[^x80-xff]{0,2}[0-9]到[^x80-xff]+[0-9]/", $str, $dan);
				}
				if (!isset($dan[0][0])) {
					return AppWeb::resp(AppWeb::SUCCESS, '解析成功!', $return);
				} else {
					$arr = explode('-', $dan[0][0]);
					if (!isset($arr[1])) {
						$arr = explode('到', $dan[0][0]);
					}
					//开始段位
					$start     = mb_substr($arr[0], 0, 2, 'utf-8');
					$start_int = mb_substr($arr[0], 2);

					//结束段位
					$end     = mb_substr($arr[1], 0, 2, 'utf-8');
					$end_int = mb_substr($arr[1], 2);

					$dan = Price::$define_back[$start] . '-' . $start_int . ':' . Price::$define_back[$end] . '-' . $end_int;
					//取出胜点
					preg_match_all('/([0-9]*)胜点/', $str, $wins_points);

					if (count($wins_points[1]) < 1) {
						preg_match_all('/([0-9]*)点/', $str, $wins_points);
					}

					//段位总价格
					$price_count = Price::dismantle($dan);
					if (isset($type)) {
						$price = $items[$dan1 . '-' . $type];
					} else {
						$price = $items[$dan1];
					}
					$arr = [];
					foreach ($price_count as $k => $v) {
						$arr[] = $price[$v];
					}
					$price_num = 0;
					foreach ($arr as $k => $v) {
						$price_num += $v;
					}

					$wins_points = isset($wins_points[1][0]) ? $wins_points[1][0] : 0;
					//计算折扣率
					if ($wins_points >= 0 && $wins_points <= 20) {
						$percent = site('0_20');
					} elseif ($wins_points > 20 && $wins_points <= 30) {
						$percent = site('21_30');
					} elseif ($wins_points > 30 && $wins_points <= 50) {
						$percent = site('31_50');
					} elseif ($wins_points > 50 && $wins_points <= 75) {
						$percent = site('51_75');
					} elseif ($wins_points > 75 && $wins_points <= 99) {
						$percent = site('76_99');
					} else {
						$percent = site('100');
					}
					$price_percent = end($arr) * $percent / 100;
					if ($percent == site('100')) {
						//计算晋级赛价格
						$jin_ji        = $items[$dan1 . '-' . 'level'];
						$price_percent = end($arr) - $jin_ji[end($price_count)];
					}

					//	计算价格 [AAa价格*胜点折扣+(AA(a-1)至BBb价格)]*段位折扣
					//计算第一个价格
					if (count($arr) > 5) {
						$result_price          = round(($price_num - $price_percent) * ((100 - site('five_dan')) / 100));
						$return['order_price'] = $result_price;
					} else {
						$result_price          = round(($price_num - $price_percent));
						$return['order_price'] = $result_price;
					}
				}
				return AppWeb::resp(AppWeb::SUCCESS, '解析成功!', $return);
			}
		} catch (\Exception $e) {
			\Log::debug($this->parseImport($arrContent, '代练内容'));
			return AppWeb::resp(AppWeb::SUCCESS, '解析成功!', $return);
		}
	}


	/**
	 * 更新游戏进度
	 * @param Request $request
	 * @param         $order_id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postProgress(Request $request, $order_id)
	{
		$content       = $request->input('message');
		$PlatformOrder = new ActionPlatformOrder($order_id);
		if ($PlatformOrder->talk($content, $this->desktop->account_id)) {
			// 更新订单主进度
			return AppWeb::resp(AppWeb::SUCCESS, '更新游戏进度成功!', 'reload|1');
		} else {
			return AppWeb::resp(AppWeb::ERROR, $PlatformOrder->getError());
		}
	}


	/**
	 * 显示游戏进度
	 * @param $id
	 * @return \Illuminate\View\View
	 */
	public function getGetIn($id)
	{
		$order     = PlatformOrder::find($id);
		$orderTags = LmStr::matchDecode($order->order_tags);
		// $order->order_note = '';
		$note = preg_replace("/\n游戏密码(.*)\n/", "\n游戏密码 : ******\n", $order->order_note);
		$note = preg_replace("/\n联系电话(.*)\n/", "\n联系电话 : ******\n", $note);

		$order->order_note = $note;
		return view('desktop.platform_order.get_in', [
			'order'      => $order,
			'order_tags' => $orderTags,
		]);
	}

	public function getMoney($id)
	{
		$order = PlatformOrder::find($id);
		$items = PlatformMoney::where('order_id', $id)
			->orderBy('created_at', 'desc')
			->paginate($this->pagesize);
		return view('desktop.platform_order.money', [
			'items' => $items,
			'order' => $order,
		]);
	}


	/**
	 * 内部信息
	 * @param Request $request
	 * @param         $id
	 * @return mixed
	 */
	public function postGetIn(Request $request, $id)
	{
		$input = $request->except(['_token']);
		if (isset($input['order_tags']) && $input['order_tags']) {
			$input['order_tags'] = LmStr::matchEncode($input['order_tags']);
		}
		PlatformOrder::where('order_id', $id)->update($input);
		return AppWeb::resp(AppWeb::SUCCESS, '更新内部信息!');
	}

	/**
	 * 显示游戏进度
	 * @param $id
	 * @return \Illuminate\View\View
	 */
	public function getDetail($id)
	{
		/** @type PlatformOrder $order */
		$order    = PlatformOrder::with('platformAccept')->findOrFail($id);
		$progress = PlatformLog::where('order_id', $id)
			->orderBy('created_at', 'Desc')
			->with('pam')
			->paginate(7);
		$canEdit  = \Gate::check('edit', $order);
		$data     = [
			'order_id'      => $id,
			'order'         => $order,
			'progress'      => $progress,
			'can_edit'      => $canEdit,
			'edit_disabled' => $canEdit ? '' : 'disabled',
		];
		if ($order->platformAccept) {
			$data['status'] = $order->platformAccept;

			if ($order->accept_platform == PlatformAccount::PLATFORM_TONG) {

				/** @type Tong $tong */
				$tong                 = PlatformFactory::create($order->platformAccept, $order);
				$data['snapshot_url'] = $tong->getSnapshotUrl();
			}
			//else if ($order->accept_platform == PlatformAccount::PLATFORM_MAO) {
			// 	$mao                  = PlatformFactory::create($order->platformAccept, $order);
			// 	//$data['snapshot_url'] = $mao->getSnapshotUrl();
			// }
		}
		if (!$order->rel_order_id) {
			$order->rel_order_id = $order->order_id;
			$order->save();
		}
		$data['rel_ids'] = explode(',', $order->rel_order_id);
		return view('desktop.platform_order.detail', $data);
	}

	/**
	 * 删除订单
	 * @param $order_id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function postDestroy($order_id)
	{
		$Order = new ActionPlatformOrder($order_id);
		// $this->policy('destroy', $Order->getOrgetBatchPublishder());
		if ($Order->destroy($this->desktop)) {
			return AppWeb::resp(AppWeb::SUCCESS, '订单已删除!', 'reload|1');
		} else {
			return AppWeb::resp(AppWeb::ERROR, $Order->getError());
		}
	}


	public function postReload($order_id)
	{
		// 同步当前订单的信息
		pt_sync_order($order_id, $log);
		return AppWeb::resp(AppWeb::SUCCESS, '手动同步所有订单信息成功!', 'reload|1');
	}


	public function postColor($id, $color)
	{
		$Order = new ActionPlatformOrder($id);
		$Order->setColor($color);
		if ($color != 'cancel') {
			return AppWeb::resp(AppWeb::SUCCESS, '已设置订单颜色');
		} else {
			return AppWeb::resp(AppWeb::SUCCESS, '已取消订单颜色');
		}
	}

	/**
	 * 备注
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postNote(Request $request)
	{
		$type      = $request->input('type');
		$id        = $request->input('id');
		$value     = $request->input('value');
		$data      = [
			'type'  => $type,
			'id'    => $id,
			'value' => $value,
		];
		$validator = \Validator::make($data, [
			'id'    => 'required',
			'type'  => 'in:order,actor',
			'value' => 'required',
		]);
		if ($validator->fails()) {
			return AppWeb::resp(AppWeb::ERROR, $validator->errors());
		}
		PlatformOrder::where('order_id', $id)->update([
			$type . '_tag_note' => $value,
		]);
		return AppWeb::resp(AppWeb::SUCCESS, '已备注', 'reload_opener|workspace');
	}

	public function getShowField($id, $field)
	{
		$Order = new ActionPlatformOrder($id);
		if (!in_array($field, ['order_get_in_mobile', 'game_pwd',])) {
			return AppWeb::resp(AppWeb::ERROR, '非法访问');
		}
		$msg = '';
		if ($field == 'order_get_in_mobile') {
			$msg = '查看了号主联系方式';
		}
		if ($field == 'game_pwd') {
			$msg = '查看了游戏密码';
		}
		$Order->talk($msg, $this->pam->account_id);
		echo $Order->getOrder()->$field;
	}


	public function postChangePwd($id)
	{
		$Order = new ActionPlatformOrder($id);
		$pwd   = \Input::get('pwd');
		if ($Order->changePwd($pwd, $this->pam->account_id)) {
			return AppWeb::resp(AppWeb::SUCCESS, '密码修改成功', 'reload|1');
		} else {
			return AppWeb::resp(AppWeb::ERROR, $Order->getError());
		}
	}

	public function postEnableUrgency($id)
	{
		$Order = new ActionPlatformOrder($id);
		if ($Order->urgency('enable')) {
			return AppWeb::resp(AppWeb::SUCCESS, '设定紧急订单', 'reload|1');
		} else {
			return AppWeb::resp(AppWeb::ERROR, $Order->getError());
		}
	}

	public function postEnableRenew($id)
	{

		$Order = new ActionPlatformOrder($id);
		if ($Order->renew('enable')) {
			return AppWeb::resp(AppWeb::SUCCESS, '设定续单', 'reload|1');
		} else {
			return AppWeb::resp(AppWeb::ERROR, $Order->getError());
		}
	}

	public function postDisableRenew($id)
	{
		$Order = new ActionPlatformOrder($id);
		if ($Order->renew('disable')) {
			return AppWeb::resp(AppWeb::SUCCESS, '取消续单', 'reload|1');
		} else {
			return AppWeb::resp(AppWeb::ERROR, $Order->getError());
		}
	}

	public function postDisableUrgency($id)
	{
		$Order = new ActionPlatformOrder($id);
		if ($Order->urgency('disable')) {
			return AppWeb::resp(AppWeb::SUCCESS, '取消紧急订单', 'reload|1');
		} else {
			return AppWeb::resp(AppWeb::ERROR, $Order->getError());
		}
	}

	/**
	 * Display a listing of the resource.
	 * @param Request $request
	 * @param null    $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\Symfony\Component\HttpFoundation\BinaryFileResponse
	 */
	public function getExportIndex(Request $request, $id = null)
	{
		if ($id) {
			// download
			$path = storage_path(PlatformExport::find($id)->storage_path);
			return \Response::download($path);
		}
		$Db           = PlatformExport::with('pam')->orderBy('created_at', 'desc');
		$account_name = $request->input('account_name');
		if ($account_name) {
			$account_id = PamAccount::where('account_name', 'like', '%' . $account_name . '%')->lists('account_id');
			if ($account_id) {
				$Db->whereIn('account_id', $account_id);
			}
		}
		$items = $Db->paginate($this->pagesize);
		$items->appends(\Input::all());
		return view('desktop.platform_order.export_index', [
			'items' => $items,
		]);
	}

	/**
	 * 订单导入
	 * @param $data
	 * @param $key
	 * @return mixed
	 */
	private function parseImport($data, $key)
	{
		foreach ($data as $k => $v) {
			if (strpos($v, $key) !== false) {
				return trim(str_replace([$key, ';', ":", '：', ' ', '|', '　'], '', $v));
			}
		}
		return null;
	}

	private function policy($do, $obj)
	{
		try {
			$this->authorize($do, $obj);
		} catch (\Exception $e) {
			throw new PolicyException('订单状态已改变, 请刷新后重试');
		}
	}
}