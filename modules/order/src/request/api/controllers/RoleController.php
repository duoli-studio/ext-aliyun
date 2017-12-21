<?php namespace Order\Http\Api\Controllers;

use Poppy\Framework\Application\ApiController;
use Order\Http\Api\Transformer\RoleTransformer;
use User\Models\PamRole;

class RoleController extends ApiController
{

	/**
	 * @api                 {get} /dailian/bank/lists [O][B]银行账户列表
	 * @apiVersion          1.0.0
	 * @apiName             UserBankList
	 * @apiGroup            User
	 * @apiSuccess {String} id                  账号ID
	 * @apiSuccess {String} bank_account        账号信息
	 * @apiSuccess {String} bank_true_name      真实姓名
	 * @apiSuccess {String} bank_type           账号类型 : 支付宝
	 * @apiSuccess {String} note                备注
	 * @apiSuccessExample   成功返回:
	 *  [
	 *      {
	 *          "id": 2,
	 *          "bank_account": "123123123",
	 *          "bank_true_name": "二狗",
	 *          "bank_type": "支付宝",
	 *          "note": ""
	 *      }
	 *  ]
	 */
	public function index()
	{
		$role = PamRole::all();

		return $this->response->collection(
			$role, new RoleTransformer()
		);
	}

	/**
	 * @api                 {get} /dailian/bank/lists [O][B]银行账户列表
	 * @apiVersion          1.0.0
	 * @apiName             UserBankList
	 * @apiGroup            User
	 * @apiSuccess {String} id                  账号ID
	 * @apiSuccess {String} bank_account        账号信息
	 * @apiSuccess {String} bank_true_name      真实姓名
	 * @apiSuccess {String} bank_type           账号类型 : 支付宝
	 * @apiSuccess {String} note                备注
	 * @apiSuccessExample   成功返回:
	 *  [
	 *      {
	 *          "id": 2,
	 *          "bank_account": "123123123",
	 *          "bank_true_name": "二狗",
	 *          "bank_type": "支付宝",
	 *          "note": ""
	 *      }
	 *  ]
	 */
	public function show($id)
	{
		$lesson = PamRole::find($id);
		if (!$lesson) {
			$this->response->errorNotFound('Lesson not found');
		}
		return $this->response->item($lesson, new RoleTransformer());
	}
}