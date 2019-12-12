<?php
apf_require_class("APF_Controller");
require CORE_PATH . 'classes/Solr/Service.php';
require CORE_PATH . 'classes/Solr/HttpTransport/Curl.php';

class Doctor_ListController extends APF_Controller
{
    private $pdo;

    private $solr_host;
    private $solr_port;

    public function __construct() {
        $this->pdo = APF_DB_Factory::get_instance()->get_pdo("master");
        $this->solr_host = APF::get_instance()->get_config('solr_host');
        $this->solr_port = APF::get_instance()->get_config('solr_port');
    }

    public function handle_request()
    {

        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET, POST, OPTIONS, DELETE");
        header("Access-Control-Allow-Headers:SIG,DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type, Accept-Language, Origin, Accept-Encoding");
        header("Content-type: application/json; charset=utf-8");

        $req = APF::get_instance()->get_request();
        $params = $req->get_parameters();

        $default_kkid = "6F86727E527411E79E6C68F728954D54"; // 422,1,2  / 朝阳

        if ( isset($params['kkid']) && strlen($params['kkid']) == 32 ) {
                   $kkid = strtoupper($params['kkid']);
        } else {
                   $kkid = $default_kkid;
        }

        $action = isset($params['action']) ? $params['action'] : "list";
        $keywords = isset($params['keywords']) ? $params['keywords'] : "";

        $page_num = 0;
        $page_size = 5;

        if (isset($params['page']) && is_numeric($params['page'])) {
           $page_num = intval($params['page']);
        }
        $page_num = $page_num <= 0 ? 1 : $page_num;
        $page_start = ($page_num - 1) * $page_size;
        $total = 0;

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

        $location = array();
        $doctor_list = array();
 
        if($action == "list"){
            $location = self::get_location($kkid);
            if(isset($location['type_code']) && !empty($location['type_code'])){
                $doctor_list = self::get_doctor_list($location['type_code'], $page_size, $page_start);
                $total = self::get_doctor_count($location['type_code']);
            }
        }
        else if($action == "search" && !empty($keywords)){

            $args['defType'] = 'edismax';
            $args['wt'] = 'json';
            $args['fq'] = 'status:1';
            $args['fl'] = 'kkid, name, job_title, degree, photo, expertise, h_kkid, hd_kkid';
            $args['qf'] = 'name';
            $args['pf'] = 'name^3000';

            $solr = new Apache_Solr_Service($this->solr_host, $this->solr_port, '/search/doctor/', new Apache_Solr_HttpTransport_Curl());
            $keywords = str_replace(':', '', $keywords);
            $result = $solr->search($keywords, $page_start, $page_size, $args);
            $doctor_list = json_decode($result->getRawResponse(), true);
            $doctor_list = isset($doctor_list['response']) ? $doctor_list['response'] : array();
            foreach($doctor_list['docs'] as $k=>$r){
               if(isset($r['photo']) && !empty($r['photo'])) $r['photo'] = IMG_CDN_DOCTOR . $r['photo'] . '/' . 'headpic.jpg';
               $r['practice_list'] = self::get_practice_list($r['kkid']);
               $doctor_list['docs'][$k] = $r;
            }
            $total = isset($doctor_list['numFound']) ? $doctor_list['numFound'] : array();
            Logger::info(__FILE__, __CLASS__, __LINE__, var_export($doctor_list, true));
        }


        echo json_encode(Util_Beauty::wanna(array(
            'code' => 1,
            'codeMsg' => 'normal_request',
            "page_num" => $page_num,
            "page_size" => $page_size,
            "total" => $total,
            "location" => $location,
            "doctor_list" => $doctor_list,
        )));

        return false;
    }

    private function get_doctor_list($loc_code, $limit, $offset)
    {
        if(empty($loc_code) || !is_numeric($limit) || !is_numeric($offset)){
            return array();
        }

        $sql = "select kkid, name, job_title, degree, photo, expertise, h_kkid, hd_kkid, u_kkid, loc_code, oncall, r_score, views, created, update_date, depart_name from t_doctor where loc_code LIKE :keyword and status=1 order by did asc LIMIT :limit OFFSET :offset ;";
        $keyword = "".$loc_code."%";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':keyword', $keyword, PDO::PARAM_STR);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $jobs = $stmt->fetchAll();
        $job = array();
        foreach($jobs as $k=>$j){
          if(isset($j['photo']) && !empty($j['photo'])){
            $j['photo'] = IMG_CDN_DOCTOR . $j['photo'] . "/" . "headpic.jpg";
          }
          $j['practice_list'] = self::get_practice_list($j['kkid']);
          $job[$k] = $j;
        }

        return $job;
    }

    private function get_doctor_count($loc_code)
    {
        if(empty($loc_code)) {
            return array();
        }
        
        $c = 0;
        $get_count_sql = "select count(*) c from t_doctor where loc_code like ? and status=1;";
        $stmt = $this->pdo->prepare($get_count_sql);
        $stmt->execute(array("$loc_code%"));
        $c = $stmt->fetchColumn();
        return $c;
    }

    private function get_location($kkid)
    {
        if(empty($kkid)) {
            return array();
        }

        $sql = "select kkid, name, parent_id, type_code, name_code, area_level from t_location where status=1 and kkid = ? limit 1;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array($kkid));
        $rows = $stmt->fetchAll();
        $row = array();
        foreach($rows as $k=>$v){
          $row = $v;
        }

        return $row;
    }

    private function get_practice_list($d_kkid)
    {
        if(empty($d_kkid)){
            return array();
        }

        $sql = "select d_kkid kkid, doctor name, job_title, degree, photo, hospital, department, h_kkid, hd_kkid, created, update_date, r_score, reg_num_int, pat_num_int, loc_code, clinic_type, price from t_practice_points where d_kkid=? and status=1 order by r_score desc, reg_num_int desc;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array("$d_kkid"));
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
    select id, kkid, name, parent_id, type_code, type_desc, status, rank, name_code, map_x, map_y, map_zoom, hospital_num, area_level, hospital_list, created, update_date from t_location where id = ? ;

    select did, kkid, name, job_title, degree, photo, expertise, h_kkid, hd_kkid, u_kkid, spider_url, loc_code, oncall, r_score, status, views, created, update_date, hospital_url, department_url, photo_url, dwsite_url, intro, depart_name from t_doctor where did = ? ;
   */


}
