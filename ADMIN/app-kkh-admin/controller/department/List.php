<?php
apf_require_class("APF_Controller");
require CORE_PATH . 'classes/Solr/Service.php';
require CORE_PATH . 'classes/Solr/HttpTransport/Curl.php';

class Department_ListController extends APF_Controller
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

        $default_hkkid = "A21878A0534311E79E6C68F728954D54"; // 宜春市第三人民医院

        if ( isset($params['kkid']) && strlen($params['kkid']) == 32 ) {
                   $h_kkid = strtoupper($params['kkid']);
        } else {
                   $h_kkid = $default_hkkid;
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

        $hospital = array();
        $department_list = array();

        if($action == 'list'){
            $hospital = self::get_hospital_data($h_kkid);
            if(isset($hospital['kkid']) && !empty($hospital['kkid'])){
               $department_list = self::get_department_list($hospital['kkid'], $page_size, $page_start);
               $total = self::get_department_count($hospital['kkid']);
            }
        }

        else if($action == "search" && !empty($keywords)){

            $args['defType'] = 'edismax';
            $args['wt'] = 'json';
            $args['fq'] = 'status:1';
            $args['fl'] = 'kkid, name, h_kkid, intro';
            $args['qf'] = 'name';
            $args['pf'] = 'name^3000';

            $solr = new Apache_Solr_Service($this->solr_host, $this->solr_port, '/search/department/', new Apache_Solr_HttpTransport_Curl());
            $keywords = str_replace(':', '', $keywords);
            $result = $solr->search($keywords, $page_start, $page_size, $args);
            $department_list = json_decode($result->getRawResponse(), true);
            $department_list = isset($department_list['response']) ? $department_list['response'] : array();
            foreach($department_list['docs'] as $k=>$r){
               if(isset($r['h_kkid']) && !empty($r['h_kkid'])) $r['hospital'] = self::get_hospital_data($r['h_kkid']);
               $department_list['docs'][$k] = $r;
            }
            $total = isset($department_list['numFound']) ? $department_list['numFound'] : array();
            Logger::info(__FILE__, __CLASS__, __LINE__, var_export($department_list, true));
        }


        echo json_encode(Util_Beauty::wanna(array(
            'code' => 1,
            'codeMsg' => 'normal_request',
            //"params" => $params,
            "page_num" => $page_num,
            "page_size" => $page_size,
            "total" => $total,
            "hospital" => $hospital,
            "department_list" => $department_list,
        )));

        return false;
    }

    private function get_department_list($h_kkid, $limit=10, $offset=0)
    {
        if(empty($h_kkid)){
            return array();
        }

        $sql = "select kkid, name, intro from t_department where h_kkid = :h_kkid and status = 1  order by reg_num_int desc, pat_num_int desc LIMIT :limit OFFSET :offset ;";
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

    private function get_department_count($h_kkid)
    {
        if(empty($h_kkid)){
            return 0;
        }

        $c = 0;
        $get_count_sql = "select count(*) from t_department where h_kkid = :h_kkid and status = 1 ;";
        $stmt = $this->pdo->prepare($get_count_sql);
        $stmt->bindParam(':h_kkid', $h_kkid, PDO::PARAM_STR);
        $stmt->execute();
        $c = $stmt->fetchColumn();
        return $c;
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
