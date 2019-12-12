<?php
apf_require_class("APF_Controller");
class Wxmp_CallBackController extends APF_Controller{

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
        $params = file_get_contents("http//input");
        Logger::Info(var_export($params, true));

        echo Util_Json::json_str(200, 'success', $params);
        return false;
    }

}

