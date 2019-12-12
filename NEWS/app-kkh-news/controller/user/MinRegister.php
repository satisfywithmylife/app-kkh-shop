<?php
apf_require_class("APF_Controller");
class User_MinRegisterController extends APF_Controller{

    public function handle_request(){
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET, POST, OPTIONS, DELETE");
        header("Access-Control-Allow-Headers:SIG,DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type, Accept-Language, Origin, Accept-Encoding");
        header("Content-type: application/json; charset=utf-8");


        $req = APF::get_instance()->get_request();
        $param_arr = $req->get_parameters();
        Logger::info(__FILE__, __CLASS__, __LINE__, var_export($param_arr, true));

        $session_key = isset($param_arr['session_key']) ? $param_arr['session_key'] : ''; 
        $encryptedData = isset($param_arr['encryptedData']) ? $param_arr['encryptedData'] : ''; 
        $iv = isset($param_arr['iv']) ? $param_arr['iv'] : ''; 
        /**/
        if(!$session_key || !$encryptedData || !$iv){
            echo Util_Json::json_str(400, '缺少参数', []);
            return;
        }   

        $appid = MP_APP_ID;
        //Logger::info(__FILE__, __CLASS__, __LINE__, MP_APP_ID);
        $pc = new WXBizDataCrypt($appid, $session_key);
        $data = ""; 
        $errCode = $pc->decryptData($encryptedData, $iv, $data );
        $errCode = abs($errCode);
        //Logger::info('err_code:'.$errCode);
        if ($errCode == 0) {
            $secretData = json_decode($data, true);
            //'{"openId":"oa4kb5EmLBPMc50gIJNZzcVXhi7E","nickName":"未莫乙","gender":2,"language":"zh_CN","city":"","province":"","country":"Andorra","avatarUrl":"https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTICvg18zAqdHzIKIjKKL3n6VsG1czBMlDAJTlDeUyTd0pAZH8MyPNSxSiaMwjqUiaXeX3Kic6ewQZdibw/0","watermark":{"timestamp":1519800640,"appid":"wx35326675599f54e9"}}'
            Logger::info(__FILE__, __CLASS__, __LINE__, var_export($secretData, true));
            $min_openid = $secretData['openId'];
            //$wx_unionid = $secretData['unionId'];//需绑定开放平台，否则无unionid
        } else {
            $error_arr = [
                41001 => 'encodingAesKey 非法',
                41003 => 'aes 解密失败',
                41004 => '解密后得到的buffer非法',
                41005 => 'base64加密失败',
                41016 => 'base64解密失败'
            ];
            echo Util_Json::json_str(400, $error_arr[$errCode], []);
            return;
        }

		/*
        if(!$wx_unionid && $min_openid){
            echo Util_Json::json_str(400, '请将小程序绑定到开放平台', []);
            return;
        }
		*/
		$bll_user = new Bll_News_User();
		$user_info = $bll_user->get_user_by_min_openid($min_openid);
		if(!$user_info){
        	$data_u = [
				'nick_name' => $secretData['nickName'],
				'avatar' => $secretData['avatarUrl'],
				'min_openid' => $min_openid,
			];
			$bll_user->add_user($data_u);
		}else{
			$data_u = [
				'nick_name' => $secretData['nickName'],
				'avatar' => $secretData['avatarUrl'],
				'min_openid' => $user_info['min_openid'],
			];
			$bll_user->update_user($data_u);
		}
		$user_info = $bll_user->get_user_by_min_openid($min_openid);
		echo Util_Json::json_str(200, 'success', $user_info);
        return;
    }

}
