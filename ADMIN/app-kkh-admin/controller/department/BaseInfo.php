<?php
apf_require_class("APF_Controller");

class Department_BaseInfoController extends APF_Controller
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

        $default_hdkkid = "5F211682537311E79E6C68F728954D54"; // 宜春市第三人民医院 精神科

        if ( isset($params['kkid']) && strlen($params['kkid']) == 32 ) {
                   $hd_kkid = strtoupper($params['kkid']);
        } else {
                   $hd_kkid = $default_hkkid;
        }

        $security = Util_Security::Security($params);

        if (!$security) {
            echo json_encode(Util_Beauty::wanna(array(
                'code' => 0,
                'codeMsg' => 'Illegal_request',
                'status' => 'fail',
                'msg' => "request forbidden",
                "userMsg" => 'Illegal_request',
            )));

            return false;
        }

        $hospital = array();
        $department = array();

        $department = self::get_department_data($hd_kkid);
        if(isset($department['h_kkid']) && !empty($department['h_kkid'])){
           $hospital = self::get_hospital_data($department['h_kkid']);
        }

        echo json_encode(Util_Beauty::wanna(array(
            'code' => 1,
            'codeMsg' => 'normal_request',
            //"params" => $params,
            "hospital" => $hospital,
            "department" => $department,
        )));

        return false;
    }

    private function get_department_data($hd_kkid)
    {
        if(empty($hd_kkid)){
            return array();
        }
        $department = array();
        $sql = "select kkid, h_kkid, name, intro from t_department where kkid = ? and status = 1  limit 1;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array("$hd_kkid"));
        $department = $stmt->fetch();
        if(isset($department['kkid']) && !empty($department['kkid'])){
           $department['doctor_list'] = self::get_practice_list($department['kkid']);
        }
        return $department;

    }

    private function get_hospital_data($kkid)
    {
        $row = array();
        $sql = "select kkid, name, loc_code, grade, tel_num, address, map_long, map_lat, map_zoom, traffic_guide, medical_guide from t_hospital where kkid= ? and status=1 limit 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array($kkid));
        $row = $stmt->fetch();
        return $row;
    }


    private function get_practice_list($hd_kkid)
    {
        if(empty($hd_kkid)){
            return array();
        }

        $sql = "select d_kkid kkid, doctor name, job_title, degree, photo, hospital, department, h_kkid, hd_kkid, created, update_date, r_score, reg_num_int, pat_num_int, loc_code, clinic_type, price from t_practice_points where hd_kkid=? and status=1 order by r_score desc, reg_num_int desc;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array("$hd_kkid"));
        $jobs = $stmt->fetchAll();
        $job = array();
        foreach($jobs as $k=>$j){
          if(isset($j['photo']) && !empty($j['photo'])){
            $j['photo'] = IMG_CDN_DOCTOR . $j['photo'] . "/" . "headpic.jpg";
          }
          $de = self::get_doctor_data($j['kkid']);
          if(isset($de['expertise'])){
            $j['expertise'] = $de['expertise'];
            $j['tags']      = $de['tags'];
            $j['intro']     = $de['intro'];
          }
          $job[$k] = $j;
        }

        return $job;
    }


    private function get_doctor_data($kkid)
    {
        $sql = "select expertise, intro, tags from t_doctor where kkid = ?  and status=1 limit 1;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array($kkid));
        return $stmt->fetch();
    }


   /*
tonycai@whale:$ ~/bin/mysqltl4php.pl -tt_department
####################################################################
Variables List
####################################################################
$id = "";
$kkid = "";
$name = "";
$h_kkid = "";
$rank = "";
$status = "";
$spider_url = "";
$hospital_url = "";
$intro = "";
$created = "";
$update_date = "";
####################################################################
Array Statement
####################################################################
$res = array(
    'id' => $id,
    'kkid' => $kkid,
    'name' => $name,
    'h_kkid' => $h_kkid,
    'rank' => $rank,
    'status' => $status,
    'spider_url' => $spider_url,
    'hospital_url' => $hospital_url,
    'intro' => $intro,
    'created' => $created,
    'update_date' => $update_date
);
####################################################################
Insert Statement
####################################################################
insert into t_department (id, kkid, name, h_kkid, rank, status, spider_url, hospital_url, intro, created, update_date) values(:id, :kkid, :name, :h_kkid, :rank, :status, :spider_url, :hospital_url, :intro, :created, :update_date);
####################################################################
Update Statement
####################################################################
update t_department set id = ?, kkid = ?, name = ?, h_kkid = ?, rank = ?, status = ?, spider_url = ?, hospital_url = ?, intro = ?, created = ?, update_date = ? where id = ? ;
####################################################################
Select Statement
####################################################################
select id, kkid, name, h_kkid, rank, status, spider_url, hospital_url, intro, created, update_date from t_department where id = ? ;
####################################################################
   */


}
