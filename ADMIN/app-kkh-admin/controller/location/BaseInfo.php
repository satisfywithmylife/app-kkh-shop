<?php
apf_require_class("APF_Controller");

class Location_BaseInfoController extends APF_Controller
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

        $kkid = "7E89795C527411E79E6C68F728954D54";
        if(isset($params['kkid']) && strlen($params['kkid']) == 32){
          $kkid = $params['kkid'];
        }
        $kkid = strtolower($kkid);
       

        $location = array();
        $location = self::get_location_data($kkid);
       

        echo json_encode(Util_Beauty::wanna(array(
            'code' => 1,
            'codeMsg' => 'normal_request',
            //"params" => $params,
            "location" => $location,
        )));

        return false;
    }

    private function get_location_data($kkid)
    {
        if(empty($kkid)){
           return array();
        }

        $sql = "select kkid, name, type_code, type_desc, rank, name_code, map_x, map_y, map_zoom area_level from t_location where kkid = ? and status = 1 limit 1 ;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array("$kkid"));
        $location = $stmt->fetch();
        return $location;
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
