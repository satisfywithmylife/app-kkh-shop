<?php
apf_require_class("APF_Controller");
class User_AuthorizeController extends APF_Controller{

    public function __construct(){
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET, POST, OPTIONS, DELETE");
        header("Access-Control-Allow-Headers:SIG,DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type, Accept-Language, Origin, Accept-Encoding");
        header("Content-type: application/json; charset=utf-8");
    }   

    public function handle_request(){

        $req = APF::get_instance()->get_request();
        $params = $req->get_parameters();
        Logger::info(__FILE__, __CLASS__, __LINE__, var_export($params, true));

        $code = isset($params["code"]) ? $params["code"] : ''; 

        if(!$code){
            echo Util_Json::json_str(400, 'code can not be empty', []);
            return false;
        }   

        $appid = MP_APP_ID;
        $secret = MP_APP_SECRET;

        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=".$appid."&secret=".$secret."&js_code=".$code."&grant_type=authorization_code";

        $res = Util_Curl::http_get_data($url);
        $res = json_decode($res, true);
        Logger::info(__FILE__, __CLASS__, __LINE__, var_export($res, true));
        if(array_key_exists('errmsg', $res)){
            $msg = $res['errmsg'];
            echo Util_Json::json_str(400, $msg, ['time' => time()]);
            return;
        }

        $data = $res;

        echo Util_Json::json_str(200, 'success', $data);
        return false;
    }

}

