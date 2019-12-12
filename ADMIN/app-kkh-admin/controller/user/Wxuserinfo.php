<?php
apf_require_class("APF_Controller");

class User_WxuserinfoController extends APF_Controller
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



        $code = "";
        $pass_ticket = "";

        if ( isset($params['code']) && !empty($params['code']) ) {
            $code = $params['code'];
        }

        if ( isset($params['pass_ticket']) && !empty($params['pass_ticket']) ) {
            $pass_ticket = $params['pass_ticket'];
        }

        $token = isset($params['user_token']) && !empty($params['user_token']) ? $params['user_token'] : '';
        $kkid = isset($params['kkid']) && !empty($params['kkid']) ? $params['kkid'] : '';

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

        $res = self::get_practice_data($code, $pass_ticket);

        $bll_user = new Bll_User_UserInfo();
        if($bll_user->verify_user_access_token($kkid, $token)){
           $jd = json_decode($res['data'], true);
           $wx_openid =  isset($jd['openid']) && !empty($jd['openid']) ? $jd['openid'] : '';
           $wx_unionid = isset($jd['unionid']) && !empty($jd['unionid']) ? $jd['unionid'] : ''; 
           Logger::info(__FILE__, __CLASS__, __LINE__, 'wx_openid: '.$wx_openid);
           Logger::info(__FILE__, __CLASS__, __LINE__, 'wx_unionid: '.$wx_unionid);
           $bll_user->set_user_wx_openid_by_kkid($kkid, $wx_openid, $wx_unionid);
           
        }

        Logger::info(__FILE__, __CLASS__, __LINE__, var_export($res, true));

/*
'data' => '{"access_token":"yLRmjiEfJyztOHCQalrIq-YRUXNClGejver-G4_JFrP31wzv3fKI1bKQyFqWJ-uNsgphBtTj-pOfNNKYzQHFdg","expires_in":7200,"refresh_token":"YVTIyunEasqeWPxi6U79BIXkKAaZfqU_tJ_gsBKpdVoOza2JPcRmrcE2oZypwXoUAQjjrfjPazCtPAe9AIHRCw","openid":"o_6qLuJDlObaj6tKV4bsiHSu4KHc","scope":"snsapi_base","unionid":"onFyguHWZekxRl3mxCYwRdgDI-Jw"}',
*/

        echo json_encode(Util_Beauty::wanna(array(
            "code"    => 1,
            "codeMsg" => 'normal_request',
            "data"  => $res['data'],
        )));

        return false;
    }


    private function get_practice_data($code, $pass_ticket)
    {
        $j = array();

        $appid = WEIXIN_APP_ID;
        $secret = WEIXIN_APP_SECRET;

        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$secret."&code=".$code."&grant_type=authorization_code&pass_ticket=".$pass_ticket;

        Logger::info(__FILE__, __CLASS__, __LINE__, "wx url: ".$url); 
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
