<?php
apf_require_class("APF_DB_Factory");

class Dao_Hospital_Info {

	private $pdo;

	public function __construct() {
		$this->pdo = APF_DB_Factory::get_instance()->get_pdo("master");
	}

        public function set_hospital_by_kkid($u_kkid, $h_kkid, $data) {
                if(isset($data['traffic_guide'])) unset($data['traffic_guide']);
                if(isset($data['introduction'])) unset($data['introduction']);
                if(isset($data['medical_guide'])) unset($data['medical_guide']);
                //
                $data['u_kkid'] = $u_kkid;
                if(isset($data['h_kkid'])) unset($data['h_kkid']);
                if(isset($data['created'])) unset($data['created']);
                if(isset($data['ver'])) unset($data['ver']);
                $data['kkid'] = $h_kkid;
                $sql = "update `t_hospital` set `u_kkid` = :u_kkid, `name` = :name, `grade` = :grade, `tel_num` = :tel_num, `address` = :address, `p_department` = :p_department, `loc_code` = :loc_code, `status` = :status, `views` = :views where `kkid` = :kkid ;";
                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute($data);
        }

        public function add_hospital($u_kkid, $data) {
                $data['u_kkid'] = $u_kkid;
                if(isset($data['h_kkid'])) unset($data['h_kkid']);
                if(isset($data['ver'])) unset($data['ver']);
                $sql = "insert into `t_hospital` (`hid`, `kkid`, `u_kkid`, `name`, `grade`, `tel_num`, `address`,`traffic_guide`, `medical_guide`, `introduction`, `p_department`, `active_doctor`, `loc_code`, `status`, `views`, `map_long`, `map_lat`, `map_zoom`, `imgs_num`, `created`, `update_date`) values(0, replace(upper(uuid()),'-',''), :u_kkid, :name, :grade, :tel_num, :address, :traffic_guide, :medical_guide, :introduction, :p_department, 0, :loc_code, :status, :views, '0', '0', '15', 0, :created, now());";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                $last_id = $this->pdo->lastInsertId();
                $h_kkid = self::get_hospital_kkid_by_hid($last_id);
                return $h_kkid;
        }

        //检查医院名称是否已存在
        public function check_hospital_is_exist($name){
            $sql = "select `name` from `t_hospital` where `name` = ? ;";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array($name));
            $name = $stmt->fetchColumn();
            if($name){
                return $name;
            }else{
                return array();
            }
        }


        private function get_hospital_kkid_by_hid($hid) {
                $kkid = '';
                $sql = "select `kkid` from `t_hospital` where `hid` = ? ;";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$hid"));
                $kkid = $stmt->fetchColumn();
                if(!empty($kkid) && strlen($kkid) == 32){
                   //$kkid = '';
                }
                else{
                   $kkid = '';
                }
                return $kkid;
        }

        public function get_hospital_sk_by_hkkid($h_kkid) {
                $kkid = '';
                $sql = "select `kkid` from `t_hospital_sk` where `h_kkid` = ? limit 1;";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$h_kkid"));
                $kkid = $stmt->fetchColumn();
                return $kkid;
        }

        public function get_hospital_by_kkid($kkid) {
                $sql = "select  `kkid` h_kkid, `name`, `grade`, `tel_num`, `address`, `traffic_guide`, `medical_guide`, `introduction`, `p_department`, `loc_code`, `views`, status, `created` from `t_hospital` where `kkid` = ? limit 1;";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$kkid"));
                //Logger::info(__FILE__, __CLASS__, __LINE__, "h_kkid: $kkid");
                $row = $stmt->fetch();
                if(isset($row['created'])) $row['ver'] = date('ymdHi', $row['created']);
                if(isset($row['traffic_guide'])) $row['traffic_guide'] = "hello";
                if(isset($row['introduction'])) $row['introduction'] = "hello";
                if(isset($row['medical_guide'])) $row['medical_guide'] = "hello";
                //Logger::info(__FILE__, __CLASS__, __LINE__, var_export($row, true));
                return $row;
        }

        public function get_hospital($kkid, $u_kkid) {
                $cond1 = "";
                $row = array();
                if(!empty($u_kkid)){
                    $cond1 = " and kkid in (select h_kkid from t_serve_scope where status = 1 and u_kkid = ? ) ";
                }
                $sql = "select `kkid`, `u_kkid`, `name`, `photo`, `grade`, `tel_num`, `address`, `traffic_guide`, `medical_guide`, `introduction`, `p_department`, `active_doctor`, `loc_code`, `status`, `views`, `map_long`, `map_lat`, `map_zoom`, `imgs_num`, `created`, `update_date` from `t_hospital` where `kkid` = ? and status = 1 $cond1 limit 1 ;";
                Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                if(!empty($u_kkid)){
                    $stmt->execute(array("$kkid", "$u_kkid"));
                }
                else{
                    $stmt->execute(array("$kkid"));
                }
                $row = $stmt->fetch();
                if(isset($row['created'])) $row['created'] = date('y-m-d H:i:s', $row['created']);
                if(isset($row['photo']) && !empty($row['photo'])) $row['photo'] = IMG_CDN_HOSPITAL . $row['photo'] . '/' . '180x130.jpg';
                if(empty($row)){
                   $row = array();
                }
                return $row;
        }

        public function add_hospital_sk($u_kkid, $data) {
                $data['u_kkid'] = $u_kkid;
                $sql = "insert into `t_hospital_sk` (`hid`, `kkid`, `h_kkid`, `u_kkid`, `name`, `grade`, `tel_num`, `address`, `traffic_guide`, `medical_guide`, `introduction`, `p_department`, `active_doctor`, `loc_code`, `status`, `views`, `map_long`, `map_lat`, `map_zoom`, `imgs_num`, `ver`, `created`, `update_date`) values(0, replace(upper(uuid()),'-',''), :h_kkid, :u_kkid, :name, :grade, :tel_num, :address, :traffic_guide, :medical_guide, :introduction, :p_department, 0, :loc_code, :status, :views, '0', '0', '15', 0, :ver, :created, now());";
                $s = $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                self::add_index_MQ($data['h_kkid'], $data);
                return $res;
        }

        private function add_index_MQ($kkid, $data) {
                $created = $data['created'];
                $res = array(
                    'kkid' => $kkid,
                    'info_type' => 1, // 0为只作记录 1为医院，2为科室，3为医生，4为药口，5为代理人，6为用户，7为交易记录
                    'action' => 1, // 0为只作记录 1为添加或更新，2为删除
                    'status' => 1,
                    'datei' => date('y-m-d', $created),
                    'created' => $created
                );
                $sql = "insert into `t_index_mq` (`id`, `kkid`, `info_type`, `action`, `status`, `datei`, `created`) values(0, :kkid, :info_type, :action, :status, :datei, :created);";
                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute($res);
        }

        public function get_hospital_list($loc_code, $limit, $offset)
        {
            if(empty($loc_code) || !is_numeric($limit) || !is_numeric($offset)){
                return array();
            }
    
            $sql = "select kkid, name, photo, loc_code, grade, tel_num, address, map_long, map_lat, map_zoom,reg_num_int,pat_num_int,followers from t_hospital where (loc_code LIKE :keyword   or loc_code = :keyword1 ) and status=1 order by reg_num_int desc, followers desc LIMIT :limit OFFSET :offset ;";
            $keyword = "".$loc_code.",%";
            $keyword1 = $loc_code;
            #Logger::info(__FILE__, __CLASS__, __LINE__, "sql: $keyword");
            #Logger::info(__FILE__, __CLASS__, __LINE__, "sql: $keyword1");
            Logger::info(__FILE__, __CLASS__, __LINE__, "sql: $sql");
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':keyword', $keyword, PDO::PARAM_STR);
            $stmt->bindParam(':keyword1', $keyword1, PDO::PARAM_STR);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $jobs = $stmt->fetchAll();
            $job = array();
            foreach($jobs as $k=>$j){
                if(isset($j['photo']) && !empty($j['photo'])) $j['photo'] = IMG_CDN_HOSPITAL . $j['photo'] . '/' . '180x130.jpg';
                $job[$k] = $j;
            }
    
            return $job;
        }
    
        public function get_hospital_count($loc_code)
        {
            if(empty($loc_code)) {
                return array();
            }
    
            $c = 0;
            $keyword = "".$loc_code.",%";
            $keyword1 = $loc_code;
            $get_count_sql = "select count(*) c from t_hospital where (loc_code LIKE :keyword   or loc_code = :keyword1 )  and status=1;";
            Logger::info(__FILE__, __CLASS__, __LINE__, "sql: $keyword");
            Logger::info(__FILE__, __CLASS__, __LINE__, "sql: $keyword1");
            Logger::info(__FILE__, __CLASS__, __LINE__, "sql: $get_count_sql");
            $stmt = $this->pdo->prepare($get_count_sql);
            $stmt->bindParam(':keyword', $keyword, PDO::PARAM_STR);
            $stmt->bindParam(':keyword1', $keyword1, PDO::PARAM_STR);
            $stmt->execute();
            $c = $stmt->fetchColumn();
            return $c;
        }
    
        public function get_location($kkid)
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



}
