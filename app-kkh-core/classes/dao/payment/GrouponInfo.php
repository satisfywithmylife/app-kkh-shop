<?php
apf_require_class("APF_DB_Factory");

class Dao_Payment_GrouponInfo {

	private $pdo;

	public function __construct() {
		$this->pdo = APF_DB_Factory::get_instance()->get_pdo("groupon_master");
	}

        public function set_payment_charge_by_id($o_kkid, $u_kkid, $data) {
                //
                $data['u_kkid'] = $u_kkid;
                $data['kkid'] = $o_kkid;
                unset($data['created']);
                unset($data['client_ip']);

                Logger::info(__FILE__, __CLASS__, __LINE__, var_export($data, true));

                $sql = "update `t_payment_charge` set `pid` = :pid, `kkid` = :kkid, `o_kkid` = :o_kkid, `charge_id` = :charge_id, `charge_created` = :charge_created, `channel` = :channel, `order_no` = :order_no, `client_ip_pp` = :client_ip_pp, `amount` = :amount, `currency` = :currency, `subject` = :subject, `body` = :body, `time_paid` = :time_paid, `time_expire` = :time_expire, `payment_status` = :payment_status, `status` = :status where `charge_id` = :charge_id ;";

                Logger::info(__FILE__, __CLASS__, __LINE__, $sql);

                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute($data);
        }

	public function set_payment_refund_msg_by_id($data) {
                $data['payment_status'] = 2;
                if($data['refund_status'] == 'failed'){
                   $data['payment_status'] = 3;
                }      
		$sql = "update `t_payment_charge` set refund_failure_msg = :msg, payment_status = :payment_status  where charge_id = :charge_id;";
		$stmt = $this->pdo->prepare($sql);
		return $stmt->execute($data);
	}

        public function set_payment_refund_by_id($data) {
                //
                $data['payment_status'] = 2;
                if($data['refund_status'] == 'failed'){
                   $data['payment_status'] = 3;
                }
                $sql = "update `t_payment_charge` set `refund_id` = :refund_id, `refund_status` = :refund_status, `refund_succeed` = :refund_succeed, `refund_time_succeed` = :refund_time_succeed, `refund_created` = :refund_created, payment_status = :payment_status, refund_failure_code = :refund_failure_code, refund_failure_msg = :refund_failure_msg where `charge_id` = :charge_id ;";

                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute($data);
        }
        public function set_payment_status($charge_id, $status, $time_paid) {

                $sql = "update `t_payment_charge` set `time_paid` = :time_paid, payment_status = :payment_status where `charge_id` = :charge_id ;";

                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam(':time_paid', $time_paid, PDO::PARAM_STR);
                $stmt->bindParam(':payment_status', $status, PDO::PARAM_INT);
                $stmt->bindParam(':charge_id', $charge_id, PDO::PARAM_STR);
                return $stmt->execute();
        }


        public function create_payment_charge($data) {
                #Logger::info(__FILE__, __CLASS__, __LINE__, var_export($data, true));
                
                $sql = "insert into `t_payment_charge` (`pid`, `kkid`, `o_kkid`, `charge_id`, `charge_created`, `channel`, `order_no`, `client_ip_pp`, `amount`, `currency`, `subject`, `body`, `time_paid`, `time_expire`, `payment_status`, `status`, `created`, `update_date`, `client_ip`) values(0, replace(upper(uuid()),'-',''), :o_kkid, :charge_id, :charge_created, :channel, :order_no, :client_ip_pp, :amount, :currency, :subject, :body, :time_paid, :time_expire, :payment_status, :status, :created, now(), :client_ip);";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                $last_id = $this->pdo->lastInsertId();
                $p_kkid = self::get_payment_kkid_by_hid($last_id);
                return $p_kkid;
        }

        //检查 payment charge 是否已存在
        public function check_payment_is_exist($data){
            $sql = "select `kkid` from `t_payment_charge` where `charge_id` = :charge_id limit 1;";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($data);
            $o_kkid = $stmt->fetchColumn();
            if($o_kkid){
                return $o_kkid;
            }else{
                return array();
            }
        }


        private function get_payment_kkid_by_hid($pid) {
                $kkid = '';
                $sql = "select `kkid` from `t_payment_charge` where `pid` = ? ;";
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


        public function get_payment_charge($charge_id) {
                
                $row = array();
                $sql = "select `pid`, `kkid`, `o_kkid`, `charge_id`, `charge_created`, `channel`, `order_no`, `client_ip_pp`, `amount`, `currency`, `subject`, `body`, `time_paid`, `time_expire`, `payment_status`, `status`, `created`, `update_date`, `client_ip` from `t_payment_charge` where `status` = 1 and `charge_id` = ? ;";
                Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$charge_id"));
                
                $row = $stmt->fetch();
                if(isset($row['created'])) $row['created'] = date('y-m-d H:i:s', $row['created']);
                if(isset($row['charge_created'])) $row['charge_created'] = date('y-m-d H:i:s', $row['charge_created']);
                if(empty($row)){
                   $row = array();
                }
                return $row;

        }


        public function get_payment_charge_list($o_kkid, $limit, $offset)
        {
            if( empty($o_kkid) || !is_numeric($limit) || !is_numeric($offset)){
                return array();
            }
    
            $sql = "select `pid`, `kkid`, `o_kkid`, `charge_id`, `charge_created`, `channel`, `order_no`, `client_ip_pp`, `amount`, `currency`, `subject`, `body`, `time_paid`, `time_expire`, `payment_status`, `status`, `created`, `update_date`, `client_ip` from `t_payment_charge` where status = 1 and o_kkid = :o_kkid order by pid desc LIMIT :limit OFFSET :offset;";
            //Logger::info(__FILE__, __CLASS__, __LINE__, "sql: $sql");
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':o_kkid', $o_kkid, PDO::PARAM_STR);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $jobs = $stmt->fetchAll();
            $job = array();
            foreach($jobs as $k=>$j){

                if(empty($row)){
                   $row = array();
                }
                $job[$k] = $j;
            }
    
            return $job;
        }
        public function get_payment_charge_list_paid($o_kkid)
        {
            if( empty($o_kkid) ){
                return array();
            }
    
            $sql = "select `pid`, `kkid`, `o_kkid`, `charge_id`, `charge_created`, `channel`, `order_no`, `client_ip_pp`, `amount`, `currency`, `subject`, `body`, `time_paid`, `time_expire`, `payment_status`, `status`, `created`, `update_date`, `client_ip` from `t_payment_charge` where status = 1 and o_kkid = :o_kkid and payment_status = 1;";
            Logger::info(__FILE__, __CLASS__, __LINE__, "sql: $sql");
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':o_kkid', $o_kkid, PDO::PARAM_STR);
            $stmt->execute();
            $jobs = $stmt->fetchAll();
            $job = array();
            foreach($jobs as $k=>$j){

                if(empty($row)){
                   $row = array();
                }
                $job[$k] = $j;
            }
    
            return $job;
        }
    
        public function get_payment_charge_count($o_kkid)
        {
            if(empty($o_kkid)) {
                return array();
            }
    
            $c = 0;
            $get_count_sql = "select count(*) c from `t_payment_charge` where status = 1 and o_kkid = :o_kkid order by pid desc LIMIT :limit OFFSET :offset;";
            Logger::info(__FILE__, __CLASS__, __LINE__, "sql: $get_count_sql");
            $stmt = $this->pdo->prepare($get_count_sql);
            $stmt->bindParam(':o_kkid', $o_kkid, PDO::PARAM_STR);
            $stmt->execute();
            $c = $stmt->fetchColumn();
            return $c;

        }

    

/*

####################################################################
Insert Statement
####################################################################
insert into `t_payment_charge` (`pid`, `kkid`, `o_kkid`, `charge_id`, `charge_created`, `channel`, `order_no`, `openid`, `client_ip_pp`, `amount`, `currency`, `subject`, `body`, `time_paid`, `time_expire`, `payment_status`, `status`, `created`, `update_date`, `client_ip`, `refund_id`, `refund_status`, `refund_succeed`, `refund_time_succeed`, `refund_created`) values(:pid, :kkid, :o_kkid, :charge_id, :charge_created, :channel, :order_no, :openid, :client_ip_pp, :amount, :currency, :subject, :body, :time_paid, :time_expire, :payment_status, :status, :created, :update_date, :client_ip, :refund_id, :refund_status, :refund_succeed, :refund_time_succeed, :refund_created);
####################################################################
Update Statement
####################################################################
update `t_payment_charge` set `pid` = :pid, `kkid` = :kkid, `o_kkid` = :o_kkid, `charge_id` = :charge_id, `charge_created` = :charge_created, `channel` = :channel, `order_no` = :order_no, `openid` = :openid, `client_ip_pp` = :client_ip_pp, `amount` = :amount, `currency` = :currency, `subject` = :subject, `body` = :body, `time_paid` = :time_paid, `time_expire` = :time_expire, `payment_status` = :payment_status, `status` = :status, `created` = :created, `update_date` = :update_date, `client_ip` = :client_ip, `refund_id` = :refund_id, `refund_status` = :refund_status, `refund_succeed` = :refund_succeed, `refund_time_succeed` = :refund_time_succeed, `refund_created` = :refund_created where `pid` = :pid ;
####################################################################
Select Statement
####################################################################
select `pid`, `kkid`, `o_kkid`, `charge_id`, `charge_created`, `channel`, `order_no`, `openid`, `client_ip_pp`, `amount`, `currency`, `subject`, `body`, `time_paid`, `time_expire`, `payment_status`, `status`, `created`, `update_date`, `client_ip`, `refund_id`, `refund_status`, `refund_succeed`, `refund_time_succeed`, `refund_created` from `t_payment_charge` where `pid` = ? ;

*/


}
