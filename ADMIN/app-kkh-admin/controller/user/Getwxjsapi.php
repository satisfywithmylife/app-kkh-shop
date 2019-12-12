<?php
apf_require_class("APF_Controller");

class User_GetwxjsapiController extends APF_Controller
{

    public function handle_request()
    {
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET, POST, OPTIONS, DELETE");
        header("Access-Control-Allow-Headers:SIG,DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type, Accept-Language, Origin, Accept-Encoding");

        header("Content-type: application/json; charset=utf-8");
        
        $req = APF::get_instance()->get_request();
        $params = $req->get_parameters();

        Logger::info(__FILE__, __CLASS__, __LINE__, "Getwxjsapi");
        Logger::info(__FILE__, __CLASS__, __LINE__, var_export($params, true));

        $url = isset($params['url']) && !empty($params['url']) ? $params['url'] : '';
        
        $weixin = new Weixin();
        $weixin_params = $weixin->weixin_params($url);

        $msg = "normal request";
        $msg1 = "normal request";

        Util_Json::render(200, $weixin_params, $msg, $msg1);

        return ;
    }
}
