<?php
apf_require_class("APF_Controller");

class Doctor_BaseInfoController extends APF_Controller
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
        Logger::info(__FILE__, __CLASS__, __LINE__, var_export($params, true));

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

        $sql = "select d_kkid kkid, doctor name, job_title, degree, photo, hospital, department, h_kkid, hd_kkid, created, update_date, r_score, reg_num_int, pat_num_int, loc_code, clinic_type, price from t_practice_points where hd_kkid=? and d_kkid=? and h_kkid=? and status=1 order by r_score desc, reg_num_int desc limit 1;";

        #Logger::info(__FILE__, __CLASS__, __LINE__, var_export($sql, true));
        #Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array("$hd_kkid", "$d_kkid", "$h_kkid"));
        $j = $stmt->fetch();
        if(isset($j['photo']) && !empty($j['photo'])){
          $j['photo'] = IMG_CDN_DOCTOR . $j['photo'] . "/" . "headpic.jpg";
        }
        $de = self::get_doctor_data($d_kkid);
        if(isset($de['expertise'])){
          $j['expertise'] = $de['expertise'];
          $j['tags']      = $de['tags'];
          $j['intro']     = $de['intro'];
        }
        return $j;
    }

    private function get_doctor_data($kkid)
    {
        $sql = "select expertise, intro, tags from t_doctor where kkid = ?  and status=1 limit 1;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array($kkid));
        return $stmt->fetch();
    }


}
