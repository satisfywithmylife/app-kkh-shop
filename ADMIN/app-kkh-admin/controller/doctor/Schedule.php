<?php
apf_require_class("APF_Controller");

class Doctor_ScheduleController extends APF_Controller
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

        $default_kkid = "9AE3C6B2587E11E79E6C68F728954D54";

        if ( isset($params['kkid']) && strlen($params['kkid']) == 32 ) {
                   $kkid = strtoupper($params['kkid']);
        } else {
                   $kkid = $default_kkid;
        }

        $h_kkid = "";
        $hd_kkid = "";

        if ( isset($params['h_kkid']) && strlen($params['h_kkid']) == 32 ) {
            $h_kkid = strtoupper($params['h_kkid']);
        }

        if ( isset($params['hd_kkid']) && strlen($params['hd_kkid']) == 32 ) {
            $hd_kkid = strtoupper($params['hd_kkid']);
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

        $doctor = self::get_practice_data($hd_kkid, $kkid, $h_kkid);

        echo json_encode(Util_Beauty::wanna(array(
            "code"    => 1,
            "codeMsg" => 'normal_request',
            "doctor"  => $doctor,
        )));

        return false;
    }


    private function get_practice_data($hd_kkid, $d_kkid, $h_kkid)
    {
        if(empty($hd_kkid)){
            return array();
        }

        $sql = "select shiftcase_url from t_practice_points where hd_kkid=? and d_kkid=? and h_kkid=? and status=1 order by r_score desc, reg_num_int desc limit 1;";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array("$hd_kkid", "$d_kkid", "$h_kkid"));
        $j = $stmt->fetch();
        if(!empty($j['shiftcase_url'])){
          $j['shiftcase_data'] = self::get_extend_schedule($j['shiftcase_url'], array());
          unset($j['shiftcase_url']);
        }
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
