<?php
apf_require_class("APF_Controller");

class User_LogoutController extends APF_Controller
{

    public function handle_request()
    {
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET, POST, OPTIONS, DELETE");
        header("Access-Control-Allow-Headers:SIG,DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type, Accept-Language, Origin, Accept-Encoding");

        header("Content-type: application/json; charset=utf-8");
        
        $req = APF::get_instance()->get_request();
        $params = $req->get_parameters();
        $security = Util_Security::Security($params);
        if (!$security) {
            Util_Json::render(400, null, 'request forbidden', 'Illegal_request');
            return false;
        }
        Logger::info(__FILE__, __CLASS__, __LINE__, var_export($params, true));

        $kkid = $params['kkid'];
        $token = $params['user_token'];
        /* */
        /* */
        $base_info  = array();
        $extend_info = array();
        $msg = "normal request";
        $logout = 0;

        $bll_user = new Bll_User_UserInfo();
        if($bll_user->verify_user_access_token($kkid, $token)){
            $logout = $bll_user->delete_user_access_token($kkid, $token);
        }
        else{
            $msg = "ACCESS DENIED";
            $msg1 = "ACCESS_DENIED";
        }


        $data = array('logout'=> $logout , );
        Util_Json::render(200, $data, $msg, $msg1);

        return ;
    }
}
