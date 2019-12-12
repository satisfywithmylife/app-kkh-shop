<?php
apf_require_class("APF_DB_Factory");

class Dao_Coupon_Info {

	private $pdo;

	public function __construct() {
		$this->pdo = APF_DB_Factory::get_instance()->get_pdo("coupon_master");
	}

        public function create_coupon($data) {
            unset($data['kkid']);
            unset($data['update_date']);
            $sql = "insert into `t_coupon` (`id`, `kkid`, `u_kkid`, `o_kkid`, `coupon_code`, `coupon_value`, `last_used`, `expiry_date`, `submitted_by`, `success`, `fail`, `status`, `create_date`, `update_date`, `locked`, `channel`, `coupon_type`, `category`, `min_use_price`) values(:id, replace(upper(uuid()),'-',''), :u_kkid, :o_kkid, :coupon_code, :coupon_value, :last_used, :expiry_date, :submitted_by, :success, :fail, :status, :create_date, now(), :locked, :channel, :coupon_type, :category, :min_use_price);";
            $stmt = $this->pdo->prepare($sql);
            $res = $stmt->execute($data);
            $last_id = $this->pdo->lastInsertId();
            return $last_id;
        }

        public function set_coupon($id, $data) {
            $data['id'] = $id;
            $sql = "update `t_coupon` set `id` = :id, `kkid` = :kkid, `u_kkid` = :u_kkid, `o_kkid` = :o_kkid, `coupon_code` = :coupon_code, `coupon_value` = :coupon_value, `last_used` = :last_used, `expiry_date` = :expiry_date, `submitted_by` = :submitted_by, `success` = :success, `fail` = :fail, `status` = :status, `create_date` = :create_date, `update_date` = :update_date, `locked` = :locked, `channel` = :channel, `coupon_type` = :coupon_type, `category` = :category, `min_use_price` = :min_use_price where `id` = :id ;";
            $stmt = $this->pdo->prepare($sql);
            $res = $stmt->execute($data);
            return $res;
        }

        public function get_coupon($id) {
            $row = array();
            $sql = "select `id`, `kkid`, `u_kkid`, `o_kkid`, `coupon_code`, `coupon_value`, `last_used`, `expiry_date`, `submitted_by`, `success`, `fail`, `status`, `create_date`, `update_date`, `locked`, `channel`, `coupon_type`, `category`, `min_use_price` from `t_coupon` where `id` = ? ;";
            Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array("$id"));

            $row = $stmt->fetch();
            if(isset($row['id']) && empty($row['id'])){
            }

            if(empty($row)){
               $row = array();
            }
            return $row;
        }


        public function set_coupon_by_kkid($c_kkid, $u_kkid, $data) {
                //
                $data['u_kkid'] = $u_kkid;
                $data['kkid'] = $c_kkid;

                Logger::info(__FILE__, __CLASS__, __LINE__, var_export($data, true));

                $sql = "update `t_coupon` set `last_used` = :last_used, `submitted_by` = :submitted_by, `success` = :success, `fail` = :fail, `status` = :status, `locked` = :locked, `channel` = :channel where `kkid` = :kkid and u_kkid = :u_kkid ;";

                Logger::info(__FILE__, __CLASS__, __LINE__, $sql);

                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute($data);
        }

        public function cancel_coupon_by_kkid($c_kkid, $u_kkid, $data) {
                //
                $data['u_kkid'] = $u_kkid;
                $data['kkid'] = $c_kkid;
                unset($data['created']);
                unset($data['client_ip']);
                $reg = self::get_coupon($c_kkid, $u_kkid);
                if(!empty($reg) && $reg['payment_status'] == 1){
                   $data['payment_status'] = 5; // 5: 取消退款中 6: 取消
                }
                if(!empty($reg) && $reg['payment_status'] == 3){
                   $data['payment_status'] = 5; // 5: 取消退款中 6: 取消
                }
                if(!empty($reg) && $reg['payment_status'] == 4){
                   $data['payment_status'] = 6; // 5: 取消退款中 6: 取消
                }

                $sql = "update `t_coupon` set `payment_status` = :payment_status where `kkid` = :kkid and u_kkid = :u_kkid ;";

                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute($data);
        }
        public function set_coupon_paystatus_by_kkid($c_kkid, $u_kkid, $status) {
                //
                $data = array();
                $data['u_kkid'] = $u_kkid;
                $data['kkid'] = $c_kkid;
                $data['payment_status'] = $status;
                $sql = "update `t_coupon` set `payment_status` = :payment_status where `kkid` = :kkid and u_kkid = :u_kkid ;";
                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute($data);
        }

        public function add_coupon($u_kkid, $data) {
                #Logger::info(__FILE__, __CLASS__, __LINE__, var_export($data, true));
                #Logger::info(__FILE__, __CLASS__, __LINE__, $u_kkid);
                $data['u_kkid'] = $u_kkid;
                $sql = "insert into `t_coupon` (`rid`, `kkid`, `u_kkid`, `truename`, `identitycard`, `mobile_num`, `h_kkid`, `hd_kkid`, `d_kkid`, `first_visit`, `checkin_date`, `checkin_hour`, `disease_type`, `outpatient_type`, `price`, `payment_method`, `service_charge`, `payment_channel`, `payment_order_sid`, `payment_status`, `payment_modify`, `status`, `created`, `update_date`, `client_ip`) values(0, replace(upper(uuid()),'-',''), :u_kkid, :truename, :identitycard, :mobile_num, :h_kkid, :hd_kkid, :d_kkid, :first_visit, :checkin_date, :checkin_hour, :disease_type, :outpatient_type, :price, :payment_method, :service_charge, :payment_channel, :payment_order_sid, :payment_status, :payment_modify, :status, :created, now(), :client_ip);";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                $last_id = $this->pdo->lastInsertId();
                $c_kkid = self::get_coupon_kkid_by_hid($last_id);
                return $c_kkid;
        }

        //检查 coupon 是否已存在
        public function check_coupon_is_exist($u_kkid, $data){
            $sql = "select `kkid` from `t_coupon` where `u_kkid` = :u_kkid and `h_kkid` = :h_kkid and `hd_kkid` = :hd_kkid and `d_kkid` = :d_kkid and `checkin_date` = :checkin_date and `checkin_hour` = :checkin_hour and `truename` = :truename limit 1;";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($data);
            $c_kkid = $stmt->fetchColumn();
            if($c_kkid){
                return $c_kkid;
            }else{
                return array();
            }
        }


        private function get_coupon_kkid_by_hid($rid) {
                $kkid = '';
                $sql = "select `kkid` from `t_coupon` where `rid` = ? ;";
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

        public function get_coupon_sk_by_hkkid($h_kkid) {
                $kkid = '';
                $sql = "select `kkid` from `t_coupon_sk` where `h_kkid` = ? limit 1;";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$h_kkid"));
                $kkid = $stmt->fetchColumn();
                return $kkid;
        }

        public function get_coupon_by_kkid($c_kkid, $kkid) {
                $sql = "select `kkid`, `u_kkid`, `o_kkid`, `coupon_code`, `coupon_value`, `last_used`, `expiry_date`, `submitted_by`, `success`, `fail`, `status`, `create_date`, `update_date`, `locked`, `channel`, `coupon_type`, `category`, `min_use_price` from `t_coupon` where `kkid` = ? and u_kkid = ? ;";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$c_kkid", "$kkid"));
                $row = $stmt->fetch();
                //Logger::info(__FILE__, __CLASS__, __LINE__, var_export($row, true));
                return $row;
        }


        private function get_coupon_category($c_type) {
                
                $row = array();
                $sql = "select `id`, `c_type`, `coupon_value`, `expiry_date`, `channel`, `min_use_price`, `create_time`, `update_date`, `status`, `description` from `t_coupon_category` where `c_type` = ? ;";
                //Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$c_type"));
                $row = $stmt->fetch();
                if(empty($row)){
                   $row = array();
                }
                return $row;

        }


        public function get_coupon_list($u_kkid, $limit, $offset, $av = 0)
        {
            if(empty($u_kkid) || !is_numeric($limit) || !is_numeric($offset)){
                return array();
            }
            $wh = "";
            if($av){
              $wh = " and ( status = 0 or  expiry_date < date(now()) ) "; 
            }
            else{
              $wh = " and status = 1 and  expiry_date >= date(now()) "; 
            }  
    
            $sql = "select `kkid`, `u_kkid`, `coupon_code`, `coupon_value`, `last_used`, `expiry_date`, `submitted_by`, `success`, `fail`, `status`, `create_date`, `update_date`, `locked`, `channel`, `coupon_type`, `category`, `min_use_price` from `t_coupon` where u_kkid = :u_kkid   $wh order by status desc, coupon_value asc, min_use_price asc LIMIT :limit OFFSET :offset;";
            Logger::info(__FILE__, __CLASS__, __LINE__, "sql: $sql");
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':u_kkid', $u_kkid, PDO::PARAM_STR);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $jobs = $stmt->fetchAll();
            $job = array();
            foreach($jobs as $k=>$j){
                if(isset($j['create_date'])) $j['created'] = date('y-m-d H:i:s', $j['create_date']);
                if(isset($j['category'])) $j['category_data'] = self::get_coupon_category($j['category']);
                $job[$k] = $j;
            }
            return $job;
        }

        public function get_coupon_share_bouns_list()
        {
            //$sql = "select id, kkid, u_kkid, o_kkid from t_coupon where status = 0 and success = 1 and submitted_by != '' and bouns = 0 and o_kkid != '' and o_kkid='F12AA3EF984B11E7B2AF00163E0EB924' order by id desc limit 1;";
            $sql = "select id, kkid, u_kkid, o_kkid from t_coupon where status = 0 and success = 1 and submitted_by != '' and bouns = 0 and o_kkid != '' order by id desc limit 100;";
            Logger::info(__FILE__, __CLASS__, __LINE__, "sql: $sql");
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $jobs = $stmt->fetchAll();
            $job = array();
            foreach($jobs as $k=>$j){
                $job[$k] = $j;
            }
            return $job;
        }

        public function set_coupon_share_bouns($kkid)
        {
            $sql = "update t_coupon set bouns = 1 where status = 0 and success = 1 and submitted_by != '' and bouns = 0 and o_kkid != '' and kkid = :kkid;";
            Logger::info(__FILE__, __CLASS__, __LINE__, "sql: $sql");
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':kkid', $kkid, PDO::PARAM_STR);
            return $stmt->execute();
        }

        public function get_coupon_list_filter_price($u_kkid, $limit, $offset, $order_total_price)
        {
            if(empty($u_kkid) || !is_numeric($limit) || !is_numeric($offset)){
                return array();
            }
    
            $sql = "select `kkid`, `u_kkid`, `coupon_code`, `coupon_value`, `last_used`, `expiry_date`, `submitted_by`, `success`, `fail`, `status`, `create_date`, `update_date`, `locked`, `channel`, `coupon_type`, `category`, `min_use_price` from `t_coupon` where u_kkid = :u_kkid  and locked = 0 and status = 1 and coupon_value < :order_total_price and expiry_date >= date(now()) and min_use_price <= :order_total_price order by status desc, coupon_value asc, min_use_price asc LIMIT :limit OFFSET :offset;";
            //Logger::info(__FILE__, __CLASS__, __LINE__, "sql: $sql");
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':u_kkid', $u_kkid, PDO::PARAM_STR);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
			$stmt->bindParam(':order_total_price', $order_total_price, PDO::PARAM_STR);
            $stmt->execute();
            $jobs = $stmt->fetchAll();
            $job = array();
            $availability_num = 0;
			if($jobs){
            	foreach($jobs as $k=>$j){
                	// availability
                	if(isset($j['create_date'])) $j['created'] = date('y-m-d H:i:s', $j['create_date']);
                	if(isset($j['category'])) $j['category_data'] = self::get_coupon_category($j['category']);
                	if($order_total_price >= $j['min_use_price'] && $order_total_price != 0){
                    	$j['availability'] = 1;
                    	$availability_num += 1;
                	}
                	else{
                    	$j['availability'] = 0;
                	}
                	$jobs[$k] = $j;
            	}
			}
            #$job['availability_num'] = $availability_num;
            return $jobs;
        }

        public function get_coupon_list_filter_price_count($u_kkid, $order_total_price)
        {
            if(empty($u_kkid)){
                return array();
            }

            if($order_total_price == 0){
               return 0;
            }

            $sql = "select count(*) c from `t_coupon` where u_kkid = :u_kkid and min_use_price <= :min_use_price and coupon_value < :order_total_price and locked=0 and  expiry_date >= date(now()) and status = 1;";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':u_kkid', $u_kkid, PDO::PARAM_STR);
            $stmt->bindParam(':min_use_price', $order_total_price, PDO::PARAM_INT);
			$stmt->bindParam(':order_total_price', $order_total_price, PDO::PARAM_STR);
            $stmt->execute();
            $c = $stmt->fetchColumn();
            return $c;
        }
    
        public function get_coupon_count($u_kkid, $av = 0)
        {
            $c = 0;
            $wh = "";
            if($av){
              $wh = " and ( status = 0 or  expiry_date < date(now()) ) "; 
            }
            else{
              $wh = " and status = 1 and locked = 0 "; 
            }
            $get_count_sql = "select count(*) c from `t_coupon` where u_kkid = :u_kkid $wh";
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

    public function mv_coupon_code($u_kkid, $mobile)
    {
        if(empty($u_kkid) || empty($mobile)){
            return array();
        }
        $sql = "update t_coupon set u_kkid=:u_kkid where u_kkid=:mobile limit 10;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':u_kkid', $u_kkid, PDO::PARAM_STR);
        $stmt->bindParam(':mobile', $mobile, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function get_coupon_value_by_kkid($c_kkid)
	{
		$value = 0;
		$sql = "select coupon_value from t_coupon where kkid = ?;";
		$stmt = $this->pdo->prepare($sql);
		$value =$stmt->execute(array($c_kkid))->fetchColumn();
		return $value;
	} 

/*
*/

}
