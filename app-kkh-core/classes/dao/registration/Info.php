<?php
apf_require_class("APF_DB_Factory");

class Dao_Registration_Info {

	private $pdo;

	public function __construct() {
		$this->pdo = APF_DB_Factory::get_instance()->get_pdo("master");
	}

        public function set_registration_by_kkid($r_kkid, $u_kkid, $data) {
                //
                $data['u_kkid'] = $u_kkid;
                $data['kkid'] = $r_kkid;
                unset($data['created']);
                unset($data['client_ip']);

                Logger::info(__FILE__, __CLASS__, __LINE__, var_export($data, true));

                $sql = "update `t_registration` set  `truename` = :truename, `identitycard` = :identitycard, `mobile_num` = :mobile_num, `h_kkid` = :h_kkid, `hd_kkid` = :hd_kkid, `d_kkid` = :d_kkid, `first_visit` = :first_visit, `checkin_date` = :checkin_date, `checkin_hour` = :checkin_hour, `disease_type` = :disease_type, `outpatient_type` = :outpatient_type, `price` = :price, `payment_method` = :payment_method, `service_charge` = :service_charge, `payment_channel` = :payment_channel, `payment_order_sid` = :payment_order_sid, `payment_status` = :payment_status, `payment_modify` = :payment_modify, `status` = :status where `kkid` = :kkid and u_kkid = :u_kkid ;";

                Logger::info(__FILE__, __CLASS__, __LINE__, $sql);

                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute($data);
        }

        public function cancel_registration_by_kkid($r_kkid, $u_kkid, $data) {
                //
                $data['u_kkid'] = $u_kkid;
                $data['kkid'] = $r_kkid;
                unset($data['created']);
                unset($data['client_ip']);
                $reg = self::get_registration($r_kkid, $u_kkid);
                if(!empty($reg) && $reg['payment_status'] == 1){
                   $data['payment_status'] = 5; // 5: 取消退款中 6: 取消
                }
                if(!empty($reg) && $reg['payment_status'] == 3){
                   $data['payment_status'] = 5; // 5: 取消退款中 6: 取消
                }
                if(!empty($reg) && $reg['payment_status'] == 4){
                   $data['payment_status'] = 6; // 5: 取消退款中 6: 取消
                }

                $sql = "update `t_registration` set `payment_status` = :payment_status where `kkid` = :kkid and u_kkid = :u_kkid ;";

                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute($data);
        }
        public function set_registration_paystatus_by_kkid($r_kkid, $u_kkid, $status) {
                //
                $data = array();
                $data['u_kkid'] = $u_kkid;
                $data['kkid'] = $r_kkid;
                $data['payment_status'] = $status;
                $sql = "update `t_registration` set `payment_status` = :payment_status where `kkid` = :kkid and u_kkid = :u_kkid ;";
                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute($data);
        }

        public function add_registration($u_kkid, $data) {
                #Logger::info(__FILE__, __CLASS__, __LINE__, var_export($data, true));
                #Logger::info(__FILE__, __CLASS__, __LINE__, $u_kkid);
                $data['u_kkid'] = $u_kkid;
                $sql = "insert into `t_registration` (`rid`, `kkid`, `u_kkid`, `truename`, `identitycard`, `mobile_num`, `h_kkid`, `hd_kkid`, `d_kkid`, `first_visit`, `checkin_date`, `checkin_hour`, `disease_type`, `outpatient_type`, `price`, `payment_method`, `service_charge`, `payment_channel`, `payment_order_sid`, `payment_status`, `payment_modify`, `status`, `created`, `update_date`, `client_ip`) values(0, replace(upper(uuid()),'-',''), :u_kkid, :truename, :identitycard, :mobile_num, :h_kkid, :hd_kkid, :d_kkid, :first_visit, :checkin_date, :checkin_hour, :disease_type, :outpatient_type, :price, :payment_method, :service_charge, :payment_channel, :payment_order_sid, :payment_status, :payment_modify, :status, :created, now(), :client_ip);";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                $last_id = $this->pdo->lastInsertId();
                $r_kkid = self::get_registration_kkid_by_hid($last_id);
                return $r_kkid;
        }

        //检查 registration 是否已存在
        public function check_registration_is_exist($u_kkid, $data){
            $sql = "select `kkid` from `t_registration` where `u_kkid` = :u_kkid and `h_kkid` = :h_kkid and `hd_kkid` = :hd_kkid and `d_kkid` = :d_kkid and `checkin_date` = :checkin_date and `checkin_hour` = :checkin_hour and `truename` = :truename limit 1;";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($data);
            $r_kkid = $stmt->fetchColumn();
            if($r_kkid){
                return $r_kkid;
            }else{
                return array();
            }
        }


        private function get_registration_kkid_by_hid($rid) {
                $kkid = '';
                $sql = "select `kkid` from `t_registration` where `rid` = ? ;";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$rid"));
                $kkid = $stmt->fetchColumn();
                if(!empty($kkid) && strlen($kkid) == 32){
                   //$kkid = '';
                }
                else{
                   $kkid = '';
                }
                return $kkid;
        }

        public function get_registration_sk_by_hkkid($h_kkid) {
                $kkid = '';
                $sql = "select `kkid` from `t_registration_sk` where `h_kkid` = ? limit 1;";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$h_kkid"));
                $kkid = $stmt->fetchColumn();
                return $kkid;
        }

        public function get_registration_by_kkid($kkid) {
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

        public function get_registration($r_kkid, $u_kkid) {
                
                $row = array();
                $sql = "select `kkid`, `u_kkid`, `truename`, `identitycard`, `mobile_num`, `h_kkid`, `hd_kkid`, `d_kkid`, `first_visit`, `checkin_date`, `checkin_hour`, `disease_type`, `outpatient_type`, `price`, `payment_method`, `service_charge`, `payment_channel`, `payment_order_sid`, `payment_status`, `payment_modify`, `status`, `created`, `update_date`, `client_ip`, `retry_num` from `t_registration` where status = 1 and `kkid` = ? and `u_kkid` = ? limit 1;";
                Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$r_kkid", "$u_kkid"));
                
                $row = $stmt->fetch();
                if(isset($row['created'])) $row['created'] = date('y-m-d H:i:s', $row['created']);
                $practice = self::get_practice_data($row['hd_kkid'], $row['d_kkid'], $row['h_kkid']);
                if(empty($row)){
                   $row = array();
                }
                return array_merge($practice, $row);

        }


        public function get_registration_list($u_kkid, $limit, $offset)
        {
            if(empty($u_kkid) || !is_numeric($limit) || !is_numeric($offset)){
                return array();
            }
    
            $sql = "select `kkid`, `u_kkid`, `truename`, `identitycard`, `mobile_num`, `h_kkid`, `hd_kkid`, `d_kkid`, `first_visit`, `checkin_date`, `checkin_hour`, `disease_type`, `outpatient_type`, `price`, `payment_method`, `service_charge`, `payment_channel`, `payment_order_sid`, `payment_status`, `payment_modify`, `status`, `created`, `update_date`, `client_ip`, `retry_num` from `t_registration` where status = 1 and u_kkid = :u_kkid order by rid desc LIMIT :limit OFFSET :offset;";
            Logger::info(__FILE__, __CLASS__, __LINE__, "sql: $sql");
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':u_kkid', $u_kkid, PDO::PARAM_STR);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $jobs = $stmt->fetchAll();
            $job = array();
            foreach($jobs as $k=>$j){

                $practice = self::get_practice_data($j['hd_kkid'], $j['d_kkid'], $j['h_kkid']);
                if(empty($row)){
                   $row = array();
                }
                $j = array_merge($practice, $j);
                $job[$k] = $j;
            }
    
            return $job;
        }
    
        public function get_registration_count($u_kkid)
        {
            if(empty($loc_code)) {
                return array();
            }
    
            $c = 0;
            $get_count_sql = "select count(*) c from `t_registration` where status = 1 and u_kkid = :u_kkid";
            Logger::info(__FILE__, __CLASS__, __LINE__, "sql: $get_count_sql");
            $stmt = $this->pdo->prepare($get_count_sql);
            $stmt->bindParam(':u_kkid', $u_kkid, PDO::PARAM_STR);
            $stmt->execute();
            $c = $stmt->fetchColumn();
            return $c;

        }

    private function get_practice_data($hd_kkid, $d_kkid, $h_kkid)
    {
        if(empty($hd_kkid)){
            return array();
        }

        $sql = "select doctor doctor_name, job_title, degree, photo, hospital, department, r_score, reg_num_int, pat_num_int, clinic_type, price from t_practice_points where hd_kkid=? and d_kkid=? and h_kkid=? and status=1 order by r_score desc, reg_num_int desc limit 1;";

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
        }
        return $j;
    }

    private function get_doctor_data($kkid)
    {
        $sql = "select expertise, tags from t_doctor where kkid = ?  and status=1 limit 1;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array($kkid));
        return $stmt->fetch();
    }
    

/*
####################################################################
Insert Statement
####################################################################
insert into `t_registration` (`rid`, `kkid`, `u_kkid`, `truename`, `identitycard`, `mobile_num`, `h_kkid`, `hd_kkid`, `d_kkid`, `first_visit`, `checkin_date`, `checkin_hour`, `disease_type`, `outpatient_type`, `price`, `payment_method`, `service_charge`, `payment_channel`, `payment_order_sid`, `payment_status`, `payment_modify`, `status`, `created`, `update_date`, `client_ip`) values(:rid, :kkid, :u_kkid, :truename, :identitycard, :mobile_num, :h_kkid, :hd_kkid, :d_kkid, :first_visit, :checkin_date, :checkin_hour, :disease_type, :outpatient_type, :price, :payment_method, :service_charge, :payment_channel, :payment_order_sid, :payment_status, :payment_modify, :status, :created, :update_date, :client_ip);
####################################################################
Update Statement
####################################################################
update `t_registration` set `rid` = :rid, `kkid` = :kkid, `u_kkid` = :u_kkid, `truename` = :truename, `identitycard` = :identitycard, `mobile_num` = :mobile_num, `h_kkid` = :h_kkid, `hd_kkid` = :hd_kkid, `d_kkid` = :d_kkid, `first_visit` = :first_visit, `checkin_date` = :checkin_date, `checkin_hour` = :checkin_hour, `disease_type` = :disease_type, `outpatient_type` = :outpatient_type, `price` = :price, `payment_method` = :payment_method, `service_charge` = :service_charge, `payment_channel` = :payment_channel, `payment_order_sid` = :payment_order_sid, `payment_status` = :payment_status, `payment_modify` = :payment_modify, `status` = :status, `created` = :created, `update_date` = :update_date, `client_ip` = :client_ip where `rid` = :rid ;
####################################################################
Select Statement
####################################################################
select `rid`, `kkid`, `u_kkid`, `truename`, `identitycard`, `mobile_num`, `h_kkid`, `hd_kkid`, `d_kkid`, `first_visit`, `checkin_date`, `checkin_hour`, `disease_type`, `outpatient_type`, `price`, `payment_method`, `service_charge`, `payment_channel`, `payment_order_sid`, `payment_status`, `payment_modify`, `status`, `created`, `update_date`, `client_ip` from `t_registration` where `rid` = ? ;
*/


}
