<?php
apf_require_class("APF_Controller");

class Hospital_BaseInfoController extends APF_Controller
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

        $default_kkid = "A11EDA95534311E79E6C68F728954D54";

        if ( isset($params['kkid']) && strlen($params['kkid']) == 32 ) {
                   $kkid = strtoupper($params['kkid']);
        } else {
                   $kkid = $default_kkid;
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
                "matches" => $matches,
            )));

            return false;
        }

        $bll_hospital = new Bll_Hospital_Info();
        $hospital = $bll_hospital->get_hospital($kkid);
        $department = array();
        if(isset($hospital['kkid']) && !empty($hospital['kkid'])){
           $department = self::get_department_list($hospital['kkid'], 300, 0);
        }

        echo json_encode(Util_Beauty::wanna(array(
            'code' => 1,
            'codeMsg' => 'normal_request',
            //"params" => $params,
            //"matches" => $matches,
            "hospital" => $hospital,
            "department" => $department,
        )));


        return false;
    }

    private function get_department_list($h_kkid, $limit=10, $offset=0)
    {
        if(empty($h_kkid)){
            return array();
        }

        $sql = "select kkid, name, intro from t_department where h_kkid = :h_kkid and status = 1  order by rank desc LIMIT :limit OFFSET :offset ;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':h_kkid', $h_kkid, PDO::PARAM_STR);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $jobs = $stmt->fetchAll();
        $job = array();
        foreach($jobs as $k=>$j){
          #$j['doctor_list'] = self::get_doctor_list($j['kkid']);
          $job[$k] = $j;
        }

        return $job;
    }


}
