<?php
apf_require_class("APF_DB_Factory");

class Dao_Newsale_Info {

	    private $pdo;

	    public function __construct() {
		        $this->pdo = APF_DB_Factory::get_instance()->get_pdo("newsale_master");
	    }

        public function order_create($data) {
                $sql = "insert into s_customer_group (id_customer_group, kkid, cd_key, id_customer, id_product, c_kkid, amount, current_state, id_address_delivery, gift_message, is_active, sync_data, created_at, updated_at)values(:id_customer_group, :kkid, :cd_key, :id_customer, :id_product, :c_kkid, :amount, :current_state, :id_address_delivery, :gift_message, :is_active, :sync_data, :created_at, :updated_at);";
				$stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                $last_id = $this->pdo->lastInsertId();
                return $last_id;
        }

		public function get_order($id_order){
				$sql = "select * from s_customer_group where id_customer_group = ? limit 1;";
				$stmt = $this->pdo->prepare($sql);
				$stmt->execute(array($id_order));
				$res = $stmt->fetch();
				if(!$res){
					return array();
				}
				return $res;
		}

        public function get_order_by_customer_list_admin($current_state, $page_size, $page_start) { 
            $row = array();
            $sql = "select * from `s_customer_group` where `current_state` = :current_state and from_unixtime(created_at) > DATE_ADD(now(), INTERVAL -1440 minute) order by id_customer_group desc LIMIT :limit OFFSET :offset ;";
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

        public function get_payment_charge_list($o_kkid, $page_size, $page_start) {
            $row = array();
            $sql = "select * from `t_payment_charge` where `o_kkid` = :o_kkid  LIMIT :limit OFFSET :offset ;";
            //Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':o_kkid', $o_kkid, PDO::PARAM_STR);
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

		public function set_payment_status($charge_id, $state, $time_paid){
			$sql = "update t_payment_charge set payment_status = :payment_status, time_paid = :time_paid where charge_id = :charge_id;";
			$stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':payment_status', $state, PDO::PARAM_INT);
            $stmt->bindParam(':time_paid', $time_paid, PDO::PARAM_INT);
            $stmt->bindParam(':charge_id', $charge_id, PDO::PARAM_STR);
			$stmt->execute();
			return true;
		}

        public function set_order_paystatus_by_kkid($kkid, $c_kkid, $state){
            $sql = "update s_customer_group set current_state = :current_state where kkid = :kkid and c_kkid = :c_kkid;";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':current_state', $state, PDO::PARAM_INT);
            $stmt->bindParam(':kkid', $kkid, PDO::PARAM_STR);
            $stmt->bindParam(':c_kkid', $c_kkid, PDO::PARAM_STR);
            $stmt->execute();
            return true;
        }
/*
*/

}
