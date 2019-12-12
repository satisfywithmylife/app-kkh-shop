<?php
apf_require_class("APF_Controller");

class User_WxgetticketController extends APF_Controller
{
    private $pdo;

    public function __construct() {
        $this->pdo = APF_DB_Factory::get_instance()->get_pdo("master");
    }

    public function handle_request()
    {
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET, POST, OPTIONS, DELETE");
        header("Access-Control-Allow-Headers:SIG,DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type, Accept-Language, Origin, Accept-Encoding");
        header("Content-type: application/json; charset=utf-8");

        $req = APF::get_instance()->get_request();
        $params = $req->get_parameters();



        $access_token = "";

        if ( isset($params['access_token']) && !empty($params['access_token']) ) {
            $code = $params['access_token'];
        }



        $security = Util_Security::Security($params);

        if (!$security) {
            echo json_encode(Util_Beauty::wanna(array(
                'code' => 0,
                'codeMsg' => 'Illegal_request',
                'status' => 'fail',
                'msg' => "request forbidden",
                "userMsg" => 'Illegal_request',
                "params" => $params,
            )));

            return false;
        }

        $res = self::get_practice_data($access_token);
        Logger::info(__FILE__, __CLASS__, __LINE__, var_export($res, true));

        echo json_encode(Util_Beauty::wanna(array(
            "code"    => 1,
            "codeMsg" => 'normal_request',
            "data"  => $res['data'],
            "page_name"  => 'wx get ticket',
        )));

        return false;
    }


    private function get_practice_data($access_token)
    {
        $j = array();

        $appid = "wx67ad393cd1ba6f08";
        $secret = "1b0c7219d91157d2a8ec50451694bcaf";

        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$access_token."&type=jsapi";

        Logger::info(__FILE__, __CLASS__, __LINE__, "getticket wx url: ".$url); 
        $j['data'] = self::get_extend_schedule($url, array());
        return $j;
    }

        private function get_extend_schedule($url, $data){

            if(empty($url)){
              return array();
            }

            $ch = curl_init();

            /* 设置验证方式 */

            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', 'Content-Type:application/json;charset=UTF-8','charset=utf-8'));

            /* 设置返回结果为流 */
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            /* 设置超时时间*/
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            /* 设置通信方式 */
            curl_setopt($ch, CURLOPT_POST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $json_data = '';


            curl_setopt ($ch, CURLOPT_URL, $url);
            #curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

            $json_data = curl_exec($ch);

            return $json_data;
            
        }




}
