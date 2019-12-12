<?php
apf_require_class("APF_DB_Factory");

class Dao_Groupon_Customer {

	    private $pdo;

	    public function __construct() {
		        $this->pdo = APF_DB_Factory::get_instance()->get_pdo("groupon_master");
	    }

        public function create_customer($data) {
                $sql = "insert into `s_customer_group` (`id_customer_group`, `kkid`, `m_kkid`, `id_group`, `g_kkid`, `id_customer`, `c_kkid`, `amount`, `current_state`, `id_address_delivery`, `gift_message`, `is_active`, `created_at`, `updated_at`) values(:id_customer_group, replace(upper(uuid()),'-',''), :m_kkid, :id_group, :g_kkid, :id_customer, :c_kkid, :amount, :current_state, :id_address_delivery, :gift_message, :is_active, :created_at, now());";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                $last_id = $this->pdo->lastInsertId();
                return $last_id;
        }

        public function set_customer($data) {
                unset($data['created_at']);
                unset($data['updated_at']);
                $sql = "update `s_customer_group` set `id_group` = :id_group, `g_kkid` = :g_kkid, `id_customer` = :id_customer, `c_kkid` = :c_kkid, `current_state` = :current_state, `is_active` = :is_active where `id_customer_group` = :id_customer_group ;";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                return $res;
        }

        public function get_customer($id) {
                $row = array();
                $sql = "select `id_customer_group`, `kkid`, `m_kkid`, `id_group`, `g_kkid`, `id_customer`, `c_kkid`, `amount`, `current_state`, `id_address_delivery`, `gift_message`, `is_active`, `created_at`, `updated_at` from `s_customer_group` where `id_customer_group` = ? ;";
                //Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$id"));
                $row = $stmt->fetch();
                if(empty($row)){
                   $row = array();
                }
                return $row;
        }

        public function get_customer_by_kkid($kkid) {
                $row = array();
                $sql = "select `id_customer_group`, `kkid`, `m_kkid`, `id_group`, `g_kkid`, `id_customer`, `c_kkid`, `amount`, `current_state`, `is_active`, `created_at`, `updated_at` from `s_customer_group` where `kkid` = ? ;";
                //Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$kkid"));
                $row = $stmt->fetch();
                if(empty($row)){
                   $row = array();
                }
                return $row;
        }

        public function get_groupon_customer_list($m_kkid, $current_state, $limit, $offset)
        {
            if(!is_numeric($limit) || !is_numeric($offset)){
                return array();
            }
            $sql = "select `id_customer_group`, `kkid`, `m_kkid`, `id_group`, `g_kkid`, `id_customer`, `c_kkid`, `amount`, `current_state`, `id_address_delivery`, `gift_message`, `is_active`, `sync_data`, `sms_half`, `created_at`, `updated_at` from `s_customer_group` where `is_active` = 1 and (m_kkid = :m_kkid or kkid = :m_kkid) and current_state in(1,2,7) order by id_customer_group asc LIMIT :limit OFFSET :offset ;";
            //if($current_state == 2){
            //}

            //Logger::info(__FILE__, __CLASS__, __LINE__, "sql: $sql");
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':m_kkid', $m_kkid, PDO::PARAM_STR);
            $stmt->bindParam(':current_state', $current_state, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $jobs = $stmt->fetchAll();
            $job = array();
            foreach($jobs as $k=>$j){
                if(isset($j['id_group']) && !empty($j['id_group'])){
                }
                $job[$k] = $j;
            }
            return $job;
        }

        public function get_groupon_my_list($u_kkid, $current_state, $limit, $offset)
        {
            if(!is_numeric($limit) || !is_numeric($offset)){
                return array();
            }

            $sql = "select `id_customer_group`, `kkid`, `m_kkid`, `id_group`, `g_kkid`, `id_customer`, `amount`, `c_kkid`, `current_state`, `is_active`, `created_at`, `updated_at` from `s_customer_group` where `is_active` = 1 and c_kkid = :c_kkid and current_state = 1 order by id_customer_group desc LIMIT :limit OFFSET :offset ;";
            if($current_state == 2){
                $sql = "select `id_customer_group`, `kkid`, `m_kkid`, `id_group`, `g_kkid`, `id_customer`, `amount`, `c_kkid`, `current_state`, `is_active`, `created_at`, `updated_at` from `s_customer_group` where `is_active` = 1 and c_kkid = :c_kkid and current_state in (2, 7) order by id_customer_group desc LIMIT :limit OFFSET :offset ;";
            }
            //Logger::info(__FILE__, __CLASS__, __LINE__, "sql: $sql");
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':c_kkid', $u_kkid, PDO::PARAM_STR);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $jobs = $stmt->fetchAll();
            $job = array();
            foreach($jobs as $k=>$j){
                if(isset($j['id_group']) && !empty($j['id_group'])){
                }
                $job[$k] = $j;
            }
            return $job;
        }

        public function get_groupon_customer_count($m_kkid, $current_state)
        {
            $c = 0;
            $get_count_sql = "select count(*) c from `s_customer_group` where `is_active` = 1 and (m_kkid = :m_kkid or kkid = :m_kkid) and current_state = :current_state;";
            //Logger::info(__FILE__, __CLASS__, __LINE__, "sql: $get_count_sql");
            $stmt = $this->pdo->prepare($get_count_sql);
            $stmt->bindParam(':m_kkid', $m_kkid, PDO::PARAM_STR);
            $stmt->bindParam(':current_state', $current_state, PDO::PARAM_STR);
            $stmt->execute();
            $c = $stmt->fetchColumn();
            return $c;

        }

        public function get_groupon_my_count($u_kkid, $current_state)
        {
            $c = 0;
            $get_count_sql = "select count(*) c from `s_customer_group` where `is_active` = 1 and c_kkid = :c_kkid and current_state = :current_state;";
            if($current_state == 2){
                $get_count_sql = "select count(*) c from `s_customer_group` where `is_active` = 1 and c_kkid = :c_kkid and current_state in (2, 7);";
            }
            //Logger::info(__FILE__, __CLASS__, __LINE__, "sql: $get_count_sql");
            $stmt = $this->pdo->prepare($get_count_sql);
            $stmt->bindParam(':c_kkid', $u_kkid, PDO::PARAM_STR);
            //$stmt->bindParam(':current_state', $current_state, PDO::PARAM_STR);
            $stmt->execute();
            $c = $stmt->fetchColumn();
            return $c;

        }

        public function set_order_paystatus_by_kkid($o_kkid, $c_kkid, $current_state) {
                //  
                $data = array();
                $data['c_kkid'] = $c_kkid;
                $data['kkid']   = $o_kkid;
                $data['current_state'] = $current_state;
                $sql = "update `s_customer_group` set `current_state` = :current_state where `kkid` = :kkid and c_kkid = :c_kkid ;";
                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute($data);
        }   

        public function get_order_by_customer_list_admin($current_state, $page_size, $page_start) { 
            $row = array();
            $sql = "select `id_customer_group`, `kkid`, `m_kkid`, `id_group`, `g_kkid`, `id_customer`, `c_kkid`, `amount`, `current_state`, `id_address_delivery`, `gift_message`, `is_active`, `created_at`, `updated_at` from `s_customer_group` where `current_state` = :current_state and from_unixtime(created_at) > DATE_ADD(now(), INTERVAL -1440 minute) order by id_customer_group desc LIMIT :limit OFFSET :offset ;";
            //Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':current_state', $current_state, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $page_size, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $page_start, PDO::PARAM_INT);
            $stmt->execute();

            $product_list = array();
            $rows = $stmt->fetchAll();
            foreach($rows as $k=>$row){

                $rows[$k] = $row;
            }   

            if(empty($rows)){
               $rows = array();
            }   
            return $rows;
        }  

/*
*/
       //根据团购g_kkid回去customer信息
	   public function get_customer_by_gkkid($g_kkid)
	   {
	       $sql = "SELECT
						`id_customer_group`,
						`kkid`,
						`m_kkid`,
						`id_group`,
						`g_kkid`,
						`id_customer`,
						`c_kkid`,
						`amount`,
						`current_state`,
						`is_active`,
						`created_at`,
						`updated_at`
				FROM
					   `s_customer_group`
				WHERE
						`g_kkid` = ?;";
			$stmt = $this->pdo->prepare($sql);
			$stmt->execute(array("$g_kkid"));
			$row = $stmt->fetch();
			if(empty($row)){
			   $row = array();
			}
		    return $row;
	   }
	   
	   //获取某个拼团商品下的拼团人数 $gkkid = 拼团kkid
	   public function get_limit_time_customer_count($gkkid)
	   {
	      $sql = "SELECT
		  	          COUNT(*) as num 
			      FROM
				      s_customer_group
				  WHERE
					g_kkid = ?";
		  $stmt = $this->pdo->prepare($sql);
		  $stmt->execute(array($g_kkid));
		  $row = $stmt->fetch();
		  if(empty($row)){
		     $row = array();
		  }
		  return $row;


	   }
}
