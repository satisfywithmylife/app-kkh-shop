<?php
apf_require_class("APF_Controller");

class Location_ListController extends APF_Controller
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
        
        $default_kkid = '10';
        if ( isset($params['kkid'])) {
                   $kkid = strtoupper($params['kkid']);
        } else {
                   $kkid = $default_kkid;
        }

        $page_num = 1;
        $page_size = 100;

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

        $location_list = array();


        $location_list = self::get_location_list($kkid, $page_size, $page_start);
        $total = self::get_location_count($kkid);


        echo json_encode(Util_Beauty::wanna(array(
            'code' => 1,
            'codeMsg' => 'normal_request',
            //"params" => $params,
            "page_num" => $page_num,
            "page_size" => $page_size,
            "total" => $total,
            "location_list" => $location_list,
        )));

        return false;
    }

    private function get_location_list($kkid, $limit, $offset)
    {

        if(!is_numeric($limit) || !is_numeric($offset)){
            return array();
        }
        $bll_hospital = new Bll_Hospital_Info();
        $location = $bll_hospital->get_location($kkid);
        $w1 = "";
        if(isset($location['type_code']) && !empty($location['type_code'])){
            $w1 = " and type_code like '".$location['type_code'].",%' ";
        }

        $sql = "select kkid, name, type_code, type_desc, rank, name_code, map_x, map_y, map_zoom, area_level from t_location where status = 1 $w1 order by rank asc LIMIT :limit OFFSET :offset ;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $jobs = $stmt->fetchAll();
        $job = array();
        foreach($jobs as $k=>$j){
          #$j['w1'] = $w1;
          $job[$k] = $j;
        }

        return $job;
    }

    private function get_location_count($kkid='')
    {

        $c = 0;
        $bll_hospital = new Bll_Hospital_Info();
        $location = $bll_hospital->get_location($kkid);
        $w1 = "";
        if(isset($location['type_code']) && !empty($location['type_code'])){
            $w1 = " and type_code like '".$location['type_code'].",%' ";
        }
        $get_count_sql = "select count(*) c from t_location where status=1 $w1;";
        $stmt = $this->pdo->prepare($get_count_sql);
        $stmt->execute();
        $c = $stmt->fetchColumn();
        return $c;
    }



   /*
####################################################################
Variables List
####################################################################
$id = "";
$kkid = "";
$name = "";
$parent_id = "";
$type_code = "";
$type_desc = "";
$status = "";
$rank = "";
$name_code = "";
$map_x = "";
$map_y = "";
$map_zoom = "";
$hospital_num = "";
$area_level = "";
$hospital_list = "";
$created = "";
$update_date = "";
####################################################################
Array Statement
####################################################################
$res = array(
    'id' => $id,
    'kkid' => $kkid,
    'name' => $name,
    'parent_id' => $parent_id,
    'type_code' => $type_code,
    'type_desc' => $type_desc,
    'status' => $status,
    'rank' => $rank,
    'name_code' => $name_code,
    'map_x' => $map_x,
    'map_y' => $map_y,
    'map_zoom' => $map_zoom,
    'hospital_num' => $hospital_num,
    'area_level' => $area_level,
    'hospital_list' => $hospital_list,
    'created' => $created,
    'update_date' => $update_date
);
####################################################################
Insert Statement
####################################################################
insert into t_location (id, kkid, name, parent_id, type_code, type_desc, status, rank, name_code, map_x, map_y, map_zoom, hospital_num, area_level, hospital_list, created, update_date) values(:id, :kkid, :name, :parent_id, :type_code, :type_desc, :status, :rank, :name_code, :map_x, :map_y, :map_zoom, :hospital_num, :area_level, :hospital_list, :created, :update_date);
####################################################################
Update Statement
####################################################################
update t_location set id = ?, kkid = ?, name = ?, parent_id = ?, type_code = ?, type_desc = ?, status = ?, rank = ?, name_code = ?, map_x = ?, map_y = ?, map_zoom = ?, hospital_num = ?, area_level = ?, hospital_list = ?, created = ?, update_date = ? where id = ? ;
####################################################################
Select Statement
####################################################################
select id, kkid, name, parent_id, type_code, type_desc, status, rank, name_code, map_x, map_y, map_zoom, hospital_num, area_level, hospital_list, created, update_date from t_location where id = ? ;

   */


}
