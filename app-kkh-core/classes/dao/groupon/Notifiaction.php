<?php
apf_require_class("APF_DB_Factory");

class Dao_Groupon_Notifiaction {

	    private $pdo;

	    public function __construct() {
		        $this->pdo = APF_DB_Factory::get_instance()->get_pdo("groupon_master");
	    }

        public function create_notifiaction($data) {
                $sql = "insert into `s_sms_notification` (`id_message`, `kkid`, `id_group`, `g_kkid`, `id_customer`, `c_kkid`, `status`, `current_state`, `content`, `created_at`, `updated_at`) values(:id_message, replace(upper(uuid()),'-',''), :id_group, :g_kkid, :id_customer, :c_kkid, :status, :current_state, :content, :created_at, now());";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                $last_id = $this->pdo->lastInsertId();
                return $last_id;
        }

        public function set_notifiaction($data) {
                unset($data['created_at']);
                unset($data['updated_at']);
                $sql = "update `s_sms_notification` set `id_group` = :id_group, `g_kkid` = :g_kkid, `id_customer` = :id_customer, `c_kkid` = :c_kkid, `status` = :status, `current_state` = :current_state, `content` = :content where `id_message` = :id_message ;";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                return $res;
        }

        public function get_notifiaction($id) {
                $row = array();
                $sql = "select `id_message`, `kkid`, `id_group`, `g_kkid`, `id_customer`, `c_kkid`, `status`, `current_state`, `content`, `created_at`, `updated_at` from `s_sms_notification` where `id_message` = ? ;";
                Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$id"));
                $row = $stmt->fetch();
                if(empty($row)){
                   $row = array();
                }
                return $row;
        }

        public function get_notifiaction_by_kkid($kkid) {
                $row = array();
                $sql = "select `id_message`, `kkid`, `id_group`, `g_kkid`, `id_customer`, `c_kkid`, `status`, `current_state`, `content`, `created_at`, `updated_at` from `s_sms_notification` where `kkid` = ? ;";
                Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$kkid"));
                $row = $stmt->fetch();
                if(empty($row)){
                   $row = array();
                }
                return $row;
        }


/*
*/

}
