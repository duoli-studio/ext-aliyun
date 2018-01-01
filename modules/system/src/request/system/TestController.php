<?php namespace System\Request\System;

use Poppy\Extension\Alipay\Aop\AopClient;
use Poppy\Extension\Alipay\OpenApi\Fund\TransToAccountTransfer;
use Poppy\Framework\Application\Controller;
use Poppy\Framework\Classes\Traits\ViewTrait;
use System\Classes\Traits\SystemTrait;

/**
 * 后端入口
 */
class TestController extends Controller
{
	use SystemTrait, ViewTrait;


	/**
	 * @throws \Exception
	 */
	public function test()
	{
		$aop = new AopClient ();
		$aop->setEnv('sandbox');
		// sandbox id : 2016082100303692
		$aop->setAppId('2016082100303692');

		// 私钥字串
		$aop->setRsaPrivateKey('MIIEoAIBAAKCAQEAqVzjJia99zbPJe6HSXQbusv3fmhPz8/jPDkXtE41ApexbMLNBkklLhMecm9p3NWuThV5We8qpdNfjfavLt+x1nreQJWolhlCVSnUzlSI72pgOtMH6mgfu71NFJz3wj6KHZ7MRPA7viJl55Mk8SCAHxkJZyz9s/fBMzmQeuMQ5PX/IDpJMUKMxwRm64O5SZ5Hbfi4y50En8cc+quDa9s7MuXGn3pAnD8UKh5Q6oktRFUvaQg4Gyed2FWol3eKRSitOxEzWpOwzRIbsjkVz0SheI513pIzKVF9oB51qIhW+SkiPEqPNwUiEul7bECWo8ut3LpQmXVkofGbVLei0MbkSQIDAQABAoIBADCbWRHlApZF47PWPnulWCQHT/O2illxJ51sIVJ9M5eX47L8QY1xRrtvf0iGk1Ju/USpwxc9nfbTsFP1HZgNWWPeBZVxnl3dx/zbMZk6B8b2t8GKOXZcBeeCz/F/j1fvTQJtReDvNaY/BxIsV+jgVAUY0WsMLZAOJiPGfKHYM0wSmlN0XfPsO4NyBDvfALNtzjIRbeqRouHf9d/4s3rXvBRSGCWNML50w0cix5S2EAMiG2y3Asu6zwZemFgw2b/zh40tqBhwIyS7JigHotGcqT7CntX5tzAOINWPNfP4bxYi0OwAdCQCh9IlyGT3g5JtdJRzBqqmuFNEMLmJRaPOAfUCgYEA2Qmbeha9KhOxSAIxM0KhSIACPazoYS5GLCc6l9cIKaMmE23XdYrsPUuM7kD/aqouhj4WM/ITZAS0k69A9kY4CMXc5NGB38pKUhon8CjGBtOW8oY6Ky13EKd92VkNeHpjvh/uLhslqeouvdBtX7OM5khRx0i6NU+Obg4kWv7qe7cCgYEAx8RLaXFagas0ozh9z48ZnDbaKF47YFYtCQ22pAYFKASiLCREC+Y50DAiiByXs3psXcEeJe6BON8bqWoKiQrHAEPBKH4mmvqcMc6l3wIrGwbNDcT5tUob/S3DHq7ba9idhmzjnIMuXyyyDL5bLRzKw3h8JcXDPhpfh4Rd9z6Zn/8CgYApoLgbcKUTnvdP0mvRYyRAHZ1QawufKBr5eQS5/tpn8gzpiRXcS6sIDqeXQww6Ty3hPaNQj0u80VI5SVHyaoFw3VKC6NQ6MjiTCsVCQO/Ke2bmWWxqv6uonBd9SqFUzFS5MLKkUTymHG6epY1036FUweY9jOt6MiolXb0HXwFmfQJ/G0+6/69/sDq395jBmp714WWebeZ0N7eQcKxvS/2GtvHrOh27L+VKAiySjAlctC0Io8jDVmxFPoFCRuc4iYPvsRmSTvbwUD/zGtwl0Vd6jTdg0YEcoqx/Jx4ajxdY6GW1I6u/cqZ8sIZr0VI1JPXKwu62CnP/PX5dkSmHr0XfuwKBgE8SCvkFT+gVTMg3mxe7XpcX+Fmy2Wo0wtCnkUsose4vqh2DmyiN7AUB2+TE2Le8f3Hjj+ru6yXUK0z31o+3yLua1XzVdaVKjTl5uvsvzqNwi33CCfK91dnx9I7Ap8nRdYW6181aoU3JCL4b24+h985L1/dZRf7zwyoxNLjN4vPm');
		// 请填写支付宝公钥，一行字符串
		$aop->setRsaPublicKey('MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA3LqVQEmw38EBpZLFwaT2RFmziS3klluT7ekHfdda4t7q87MueVN2I+VBoE/XDTYZ67HZEHmOAFTxFYwAXWuWKczxo54Bg+SaWw+qWhWxrIz2dmbDCEsTfWVIncBnHGaMK9ZkAvs+waMap77WXTFsw9Ak3eSoeLkLkxfhzjEvk/elLyLThngfkfoKHegw5W5tcfjh5eWGHmRxTk5qVHTB6f9DEyBUqaLpu2kjX4TNoSTgDgnEBAeGE4SxY3FfYTj/Zo5blZxQ3H+IkjCDuV2C9y70CvtP8T8uPjddGq5mqV0XYSwv10rsyNW5VEiJSha4i4ESmsg2H2QUP/dT8J7Q0wIDAQAB');
		$request = new TransToAccountTransfer();
		$data    = [
			'out_biz_no'      => '31423214233432',
			'payee_type'      => 'ALIPAY_LOGONID',
			'payee_account'   => 'rnaqlu5532@sandbox.com',
			'amount'          => '12.23',
			'payer_show_name' => '上海交通卡退款',
			'payee_real_name' => '沙箱环境',
			'remark'          => '转账备注',
		];
		$request->setBizContent(json_encode($data));
		$result = $aop->execute($request);

		$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		$resultCode   = $result->$responseNode->code;

		/**
		 * "alipay_fund_trans_toaccount_transfer_response": {#469 ▼
		 *     "code": "10000"
		 *     "msg": "Success"
		 *     "order_id": "20180101110070001502280000078438"
		 *     "out_biz_no": "3142321423432"
		 *     "pay_date": "2018-01-01 16:08:04"
		 * }
		 */
		if (!empty($resultCode) && $resultCode == 10000) {
			dd($result);
			echo "成功";
		}
		else {
			$message = data_get($result, 'alipay_fund_trans_toaccount_transfer_response.sub_msg');
			dd($message);
		}
	}
}
