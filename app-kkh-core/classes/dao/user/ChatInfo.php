<?php
apf_require_class("APF_DB_Factory");

class Dao_User_ChatInfo {

	    private $pdo;

	    public function __construct() {
		        $this->pdo = APF_DB_Factory::get_instance()->get_pdo("chat_master");
	    }

        public function create_user($data) {
                $sql = "insert into `t_users` (`uid`, `username`, `nickname`, `user_type`, `ease_uuid`, `actived`, `client_ip`, `created`, `update_date`) values(:uid, :username, :nickname, :user_type, :ease_uuid, :actived, :client_ip, :created, now());";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                $last_id = $this->pdo->lastInsertId();
                return $last_id;
        }

        public function set_user($data) {
                unset($data['username']);
                unset($data['created']);
                unset($data['update_date']);
                $sql = "update `t_users` set `nickname` = :nickname, `user_type` = :user_type, `ease_uuid` = :ease_uuid, `actived` = :actived, `client_ip` = :client_ip where `uid` = :uid ;";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                return $res;
        }

        public function get_user($id) {
                $row = array();
                $sql = "select `uid`, `username`, `nickname`, `user_type`, `ease_uuid`, `actived`, `client_ip`, `created`, `update_date` from `t_users` where `uid` = ? ;";
                Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$id"));
                $row = $stmt->fetch();
                if(empty($row)){
                   $row = array();
                }
                return $row;
        }

        public function get_user_by_username($username) {
                $row = array();
                $sql = "select `uid`, `username`, `nickname`, `user_type`, `ease_uuid`, `actived`, `client_ip`, `created`, `update_date` from `t_users` where `username` = ? ;";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$username"));
                $row = $stmt->fetch();
                if(empty($row)){
                   $row = array();
                }
                return $row;
        }

/*
*/

}
