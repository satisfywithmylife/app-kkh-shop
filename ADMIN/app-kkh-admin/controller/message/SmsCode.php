<?php
apf_require_class("APF_Controller");

class Message_SmsCodeController extends APF_Controller
{
    public function __construct() {
    }


    public function handle_request()
    {
        header("Access-Control-Allow-Origin:*.kangkanghui.com");
        header("Access-Control-Allow-Methods:GET, POST, OPTIONS, DELETE");
        header("Access-Control-Allow-Headers:SIG,DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type, Accept-Language, Origin, Accept-Encoding");
        header("Content-type: application/json; charset=utf-8");
        
        $req = APF::get_instance()->get_request();
        $params = $req->get_parameters();

        Logger::info(__FILE__, __CLASS__, __LINE__, 'SmsCode: Mobile');
        Logger::info(__FILE__, __CLASS__, __LINE__, var_export($params, true));

        $msg = 'SmsCode';
        $res = $params;
        Util_Json::render(200, null, $msg, $res);
        return ;
    }


}
