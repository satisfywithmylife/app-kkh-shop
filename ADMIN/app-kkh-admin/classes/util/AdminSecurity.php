<?php

/**
 * Created by PhpStorm.
 * User: chanlevel
 * Date: 15/8/4
 * Time: 下午4:17
 */
class Util_AdminSecurity {
	//static $APIKEY = "6F86727E527411E79E6C68F728954D54188D51B5534511E79E6C68F728954D54";
        static $APIKEY = WECHAT_SECURITY_APIKEY;

	/**
	 *
	 * 将接收到的所有的参数排序，拼接APIKEY，再通过两次MD5加密，与header中的sig比对
	 *
	 *
	 * @param $params
	 * @return bool
	 */
	public static function  Security($params) {
                #echo json_encode($_SERVER);
                #exit;
		#if (isset($_SERVER['HTTP_SIG'])) {
                #   return TRUE;
		#}
        $username = isset($params['username']) && !empty($params['username']) ? $params['username'] : '';
        $access_token = isset($params['access_token']) && !empty($params['access_token']) ? $params['access_token'] : '';
        #check admin role
        $bll_admin_user = new Bll_Admin_Info();
        $check = $bll_admin_user->check_user_role($username ,$access_token);
        if(!$check) {
            return false;
        }else{
            return true;
        }

	}
}
