<?php namespace System\Request\Backend\Controllers;

use App\Lemon\Dailian\Application\App\AppWeb;
use App\Models\PlatformAccount;
use App\Models\PlatformBind;
use Illuminate\Http\Request;

/**
 * 平台订单
 * Class PlatformOrderController
 * @package App\Http\Controllers\Desktop
 */
class PlatformAccountController extends InitController
{

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
		$Account = PlatformAccount::orderBy('created_at', 'desc');

		// keyword
		$note = $request->input('note');
		if ($note) {
			$kw = trim($note);
			$Account->where('note', 'like', '%' . $kw . '%');
		}
		// 发单人
		$contact = $request->input('contact');
		if ($contact) {
			$kw = trim($contact);
			$Account->where('contact', 'like', '%' . $kw . '%');
		}
		$platform = $request->input('platform');
		if ($platform) {
			$Account->where('platform', $platform);
		}

		$items = $Account->paginate($this->pagesize);
		$items->appends($request->input());
		return view('desktop.platform_account.index', [
			'items' => $items,
		]);
	}

	public function getCreate($type = null)
	{
		$type = $type ?: PlatformAccount::PLATFORM_YI;
		return view('desktop.platform_account.item', [
			'type' => $type,
		]);
	}

	public function postCreate(Request $request)
	{
		$type = $request->input('type');
		$data = [
			'platform' => $type,
			'mobile'   => $request->input('mobile'),
			'qq'       => $request->input('qq'),
			'contact'  => $request->input('contact'),
			'note'     => $request->input('note'),
		];
		switch ($type) {
			case 'yi':
			default:
				$data['yi_nickname']   = $request->input('yi_nickname');
				$data['yi_userid']     = $request->input('yi_userid');
				$data['yi_app_key']    = $request->input('yi_app_key');
				$data['yi_app_secret'] = $request->input('yi_app_secret');
				$data['yi_payword']    = $request->input('yi_payword');
				PlatformAccount::create($data);
				break;
			case 'baozi':
				$data['baozi_nickname']   = $request->input('baozi_nickname');
				$data['baozi_userid']     = $request->input('baozi_userid');
				$data['baozi_app_key']    = $request->input('baozi_app_key');
				$data['baozi_app_secret'] = $request->input('baozi_app_secret');
				$data['baozi_payword']    = $request->input('baozi_payword');
				PlatformAccount::create($data);
				break;
			case 'mao';
				$data['mao_account']  = $request->input('mao_account');
				$data['mao_password'] = $request->input('mao_password');
				$data['mao_payword']  = $request->input('mao_payword');
				PlatformAccount::create($data);
				break;
			case 'mama';
				$data['mama_account']  = $request->input('mama_account');
				$data['mama_password'] = $request->input('mama_password');
				$data['mama_payword']  = $request->input('mama_payword');
				PlatformAccount::create($data);
				break;
			case 'tong':
				$data['tong_nickname'] = $request->input('tong_nickname');
				$data['tong_userid']   = $request->input('tong_userid');
				$data['tong_account']  = $request->input('tong_account');
				$data['tong_password'] = $request->input('tong_password');
				$data['tong_payword']  = $request->input('tong_payword');
				PlatformAccount::create($data);
				break;
            case 'yq';
                $data['yq_phone']  = $request->input('yq_phone');
                $data['yq_account'] = $request->input('yq_account');
                $data['yq_auth_key']  = $request->input('yq_auth_key');
                $data['yq_payword']  = $request->input('yq_payword');
                PlatformAccount::create($data);
                break;
		}
		return AppWeb::resp(AppWeb::SUCCESS, '保存平台信息成功', 'location|' . route('dsk_platform_account.index'));
	}


	public function getEdit($id)
	{
		$item = PlatformAccount::find($id);
		return view('desktop.platform_account.item', [
			'item' => $item,
			'type' => $item->platform,
		]);
	}

	public function postEdit(Request $request, $id)
	{
		$input = $request->except(['_token', 'type']);
		PlatformAccount::where('id', $id)->update($input);
		return AppWeb::resp(AppWeb::SUCCESS, '账号编辑成功', 'location|' . route('dsk_platform_account.index'));
	}

	public function postDestroy($id)
	{
		if (PlatformBind::where('platform_account_id', $id)->exists()) {
			return AppWeb::resp(AppWeb::ERROR, '该账号已经被绑定, 不得删除');
		}
		PlatformAccount::destroy($id);
		return AppWeb::resp(AppWeb::SUCCESS, '删除账号成功', 'reload|1');
	}

	/**
	 * 绑定发单账号
	 * @return mixed
	 */
	public function getBind()
	{
		$yi        = PlatformAccount::kvLinear(PlatformAccount::PLATFORM_YI);
		$yq        = PlatformAccount::kvLinear(PlatformAccount::PLATFORM_YQ);
		$mao       = PlatformAccount::kvLinear(PlatformAccount::PLATFORM_MAO);
		$mama      = PlatformAccount::kvLinear(PlatformAccount::PLATFORM_MAMA);
		$tong      = PlatformAccount::kvLinear(PlatformAccount::PLATFORM_TONG);
		$baozi     = PlatformAccount::kvLinear(PlatformAccount::PLATFORM_BAOZI);
		$yiAccount = PlatformBind::where('account_id', $this->pam->account_id)
			->where('platform', PlatformAccount::PLATFORM_YI)
			->lists('platform_account_id');

		$yqAccount = PlatformBind::where('account_id', $this->pam->account_id)
			->where('platform', PlatformAccount::PLATFORM_YQ)
			->lists('platform_account_id');

		$maoAccount = PlatformBind::where('account_id', $this->pam->account_id)
			->where('platform', PlatformAccount::PLATFORM_MAO)
			->lists('platform_account_id');

		$mamaAccount = PlatformBind::where('account_id', $this->pam->account_id)
			->where('platform', PlatformAccount::PLATFORM_MAMA)
			->lists('platform_account_id');

		$tongAccount = PlatformBind::where('account_id', $this->pam->account_id)
			->where('platform', PlatformAccount::PLATFORM_TONG)
			->lists('platform_account_id');

		$baoziAccount = PlatformBind::where('account_id', $this->pam->account_id)
			->where('platform', PlatformAccount::PLATFORM_BAOZI)
			->lists('platform_account_id');
		return view('desktop.platform_account.bind', [
			'yi'           => $yi,
			'yq'           => $yq,
			'mao'          => $mao,
			'tong'         => $tong,
			'mama'         => $mama,
			'yi_account'   => array_flip(array_flatten($yiAccount)),
			'yq_account'   => array_flip(array_flatten($yqAccount)),
			'mao_account'  => array_flip(array_flatten($maoAccount)),
			'baozi'         => $baozi,
			'mama_account'  => array_flip(array_flatten($mamaAccount)),
			'tong_account'  => array_flip(array_flatten($tongAccount)),
			'baozi_account' => array_flip(array_flatten($baoziAccount)),
		]);
	}

	/**
	 * 保存发单账号
	 * @param Request $request
	 * @return mixed
	 * @throws \Exception
	 */
	public function postBind(Request $request)
	{
		$yi      = $request->input('yi');
		$yq      = $request->input('yq');
		$baozi      = $request->input('baozi');
		$mao     = $request->input('mao');
		$tong    = $request->input('tong');
		$mama    = $request->input('mama');
		$account = [];
		$base    = [
			'account_id'   => $this->pam->account_id,
			'account_type' => $this->pam->account_type,
		];
		if ($yi) {
			if (count(array_flip($yi)) != count($yi)) {
				$yi = array_flip(array_flip($yi));
			}
			foreach ($yi as $y) {
				if (!$y) {
					continue;
				}
				$account[] = array_merge($base, [
					'platform'            => PlatformAccount::PLATFORM_YI,
					'platform_account_id' => $y,
				]);
			}
		}
		if ($yq) {
			if (count(array_flip($yq)) != count($yq)) {
				$yq = array_flip(array_flip($yq));
			}
			foreach ($yq as $y) {
				if (!$y) {
					continue;
				}
				$account[] = array_merge($base, [
					'platform'            => PlatformAccount::PLATFORM_YQ,
					'platform_account_id' => $y,
				]);
			}
		}
		if ($mao) {
			if (count(array_flip($mao)) != count($mao)) {
				$mao = array_flip(array_flip($mao));
			}
			foreach ($mao as $m) {
				if (!$m) {
					continue;
				}
				$account[] = array_merge($base, [
					'platform'            => PlatformAccount::PLATFORM_MAO,
					'platform_account_id' => $m,
				]);
			}
		}
		if ($mama) {
			if (count(array_flip($mama)) != count($mama)) {
				$mama = array_flip(array_flip($mama));
			}
			foreach ($mama as $m) {
				if (!$m) {
					continue;
				}
				$account[] = array_merge($base, [
					'platform'            => PlatformAccount::PLATFORM_MAMA,
					'platform_account_id' => $m,
				]);
			}
		}
		if ($tong) {
			if (count(array_flip($tong)) != count($tong)) {
				$tong = array_flip(array_flip($tong));
			}
			foreach ($tong as $t) {
				if (!$t) {
					continue;
				}
				$account[] = array_merge($base, [
					'platform'            => PlatformAccount::PLATFORM_TONG,
					'platform_account_id' => $t,
				]);
			}
		}
		if ($baozi) {
			if (count(array_flip($baozi)) != count($baozi)) {
				$baozi = array_flip(array_flip($baozi));
			}
			foreach ($baozi as $t) {
				if (!$t) {
					continue;
				}
				$account[] = array_merge($base, [
					'platform'            => PlatformAccount::PLATFORM_BAOZI,
					'platform_account_id' => $t,
				]);
			}
		}
		PlatformBind::where('account_id', $this->pam->account_id)->delete();
		if ($account) {
			PlatformBind::insert($account);
		}
		return AppWeb::resp(AppWeb::SUCCESS, '已经绑定发单账号', 'reload|1');
	}

}

