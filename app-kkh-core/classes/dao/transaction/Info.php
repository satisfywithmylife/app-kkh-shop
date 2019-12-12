<?php
apf_require_class("APF_DB_Factory");

class Dao_Transaction_Info {

	private $pdo;

	public function __construct() {
		$this->pdo = APF_DB_Factory::get_instance()->get_pdo("master");
	}

        public function set_transaction_by_kkid($u_kkid, $t_kkid, $data) {
                if(isset($data['id'])) unset($data['id']);
                if(isset($data['kkid'])) unset($data['kkid']);
                if(isset($data['t_kkid'])) unset($data['t_kkid']);
                if(isset($data['created'])) unset($data['created']);
                if(isset($data['update_date'])) unset($data['update_date']);
                $data['u_kkid'] = $u_kkid;
                $data['kkid'] = $t_kkid;

                $sql = "update `t_transaction` set `u_kkid` = :u_kkid, `h_kkid` = :h_kkid, `d_kkid` = :d_kkid, `datei` = :datei, `tid` = :tid, `name` = :name, `specs` = :specs, `t_num` = :t_num, `buyer_hospital` = :buyer_hospital, `trans_date` = :trans_date, `hospital_id` = :hospital_id, `product_id` = :product_id, `batch_number` = :batch_number, `email` = :email, `version` = :version, `status` = :status, `client_ip` = :client_ip where `kkid` = :kkid ;";

                Logger::info(__FILE__, __CLASS__, __LINE__, "sql: $sql");
                Logger::info(__FILE__, __CLASS__, __LINE__, var_export($data, true));

                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute($data);
        }

        private function get_transaction_kkid_by_id($id) {
                $sql = "select `kkid` from `t_transaction` where `id` = ? ;";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$id"));
                $kkid = $stmt->fetchColumn();
                if(!empty($kkid) && strlen($kkid) == 32){
                   //$kkid = '';
                }
                else{
                   $kkid = '';
                }
                return $kkid;
                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute($data);
        }

        public function add_transaction($u_kkid, $data) {
                if(isset($data['id'])) unset($data['id']);
                if(isset($data['kkid'])) unset($data['kkid']);
                if(isset($data['t_kkid'])) unset($data['t_kkid']);
                if(isset($data['update_date'])) unset($data['update_date']);
                $data['u_kkid'] = $u_kkid;
               $sql = "insert into `t_transaction` (`id`, `kkid`, `u_kkid`, `h_kkid`, `d_kkid`, `datei`, `tid`, `name`, `specs`, `t_num`, `buyer_hospital`, `trans_date`, `hospital_id`, `product_id`, `batch_number`, `email`, `version`, `status`, `client_ip`, `created`, `update_date`) values(0, replace(upper(uuid()),'-',''), :u_kkid, :h_kkid, :d_kkid, :datei, :tid, :name, :specs, :t_num, :buyer_hospital, :trans_date, :hospital_id, :product_id, :batch_number, :email, :version, :status, :client_ip, :created, now());";
                $stmt = $this->pdo->prepare($sql);
                Logger::info(__FILE__, __CLASS__, __LINE__, "sql: $sql");
                Logger::info(__FILE__, __CLASS__, __LINE__, var_export($data, true));
                $res = $stmt->execute($data);
                /* */
                $last_id = $this->pdo->lastInsertId();
                //Logger::info(__FILE__, __CLASS__, __LINE__, "last_id: $last_id");
                $t_kkid = self::get_transaction_kkid_by_id($last_id);
                return $t_kkid;
        }
/*
        private function get_transaction_kkid_by_id($id) {
                $sql = "select `kkid` from `t_transaction` where `id` = ? ;";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$id"));
                $kkid = $stmt->fetchColumn();
                if(!empty($kkid) && strlen($kkid) == 32){
                   //$kkid = '';
                }
                else{
                   $kkid = '';
                }
                return $kkid;
        }
*/


        public function get_transaction($kkid) {
                $sql = "select `kkid`, `u_kkid`, `h_kkid`, `d_kkid`, `datei`, `tid`, `name`, `specs`, `t_num`, `buyer_hospital`, `trans_date`, `hospital_id`, `product_id`, `batch_number`, `version`, `status`, `client_ip`, `created`, `update_date` from `t_transaction` where `kkid` = ? and status = 1 limit 1;";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$kkid"));
                $row = $stmt->fetch();
                if(isset($row['created'])) $row['created'] = date('y-m-d H:i:s', $row['created']);
                //Logger::info(__FILE__, __CLASS__, __LINE__, var_export($row, true));
                return $row;
        }




}
