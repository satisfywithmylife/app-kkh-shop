<?php
apf_require_class("APF_DB_Factory");

class Dao_Gift_Info {

	private $pdo;

	public function __construct() {
		$this->pdo = APF_DB_Factory::get_instance()->get_pdo("green_master");
	}

        public function create_gift($data) {
                $sql = "insert into `user_gift` (`id`, `user_type`, `user_id`, `gift_id`, `amount`, `create_time`, `update_time`, `history_amount`) values(:id, :user_type, :user_id, :gift_id, :amount, :create_time, :update_time, :history_amount);";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                $last_id = $this->pdo->lastInsertId();
                return $last_id;
        }

        public function set_gift($data) {
                $sql = "update `user_gift` set `user_type` = :user_type, `user_id` = :user_id, `gift_id` = :gift_id, `amount` = :amount, `create_time` = :create_time, `update_time` = :update_time, `history_amount` = :history_amount where `id` = :id ;";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                return $res;
        }

        public function get_gift($id) {
                $row = array();
                $sql = "select `id`, `user_type`, `user_id`, `gift_id`, `amount`, `create_time`, `update_time`, `history_amount` from `user_gift` where `id` = ? ;";
                Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$id"));
                $row = $stmt->fetch();
                if(empty($row)){
                   $row = array();
                }
                return $row;
        }

        public function get_gift_by_userid($userid) {
                $row = array();
                $sql = "select `id`, `user_type`, `user_id`, `gift_id`, `amount`, `create_time`, `update_time`, `history_amount` from `user_gift` where `user_id` = ? ;";
                Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$userid"));
                $row = $stmt->fetch();
                if(empty($row)){
                   $row = array();
                }
                return $row;
        }

/*
*/
/* user_gift_entry */
/*
*/
        public function create_gift_entry($data) {
                $sql = "insert into `user_gift_entry` (`id`, `user_type`, `user_id`, `source_type`, `source_id`, `category`, `description`, `gift_id`, `amount`, `direction`, `create_time`, `effect_time`, `effect_to_history`, `gift_value`) values(:id, :user_type, :user_id, :source_type, :source_id, :category, :description, :gift_id, :amount, :direction, :create_time, :effect_time, :effect_to_history, :gift_value);";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                $last_id = $this->pdo->lastInsertId();
                return $last_id;
        }

        public function set_gift_entry($data) {
                $sql = "update `user_gift_entry` set `user_type` = :user_type, `user_id` = :user_id, `source_type` = :source_type, `source_id` = :source_id, `category` = :category, `description` = :description, `gift_id` = :gift_id, `amount` = :amount, `direction` = :direction, `create_time` = :create_time, `effect_time` = :effect_time, `effect_to_history` = :effect_to_history, `message` = :message, `gift_value` = :gift_value where `id` = :id ;";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                return $res;
        }

        public function get_gift_entry($id) {
                $row = array();
                $sql = "select `id`, `user_type`, `user_id`, `source_type`, `source_id`, `category`, `description`, `gift_id`, `amount`, `direction`, `create_time`, `effect_time`, `effect_to_history`, `message`, `gift_value` from `user_gift_entry` where `id` = ? ;";
                Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$id"));
                $row = $stmt->fetch();
                if(empty($row)){
                   $row = array();
                }
                return $row;
        }

        public function get_gift_entry_by_userid($userid) {
                $row = array();
                $sql = "select `id`, `user_type`, `user_id`, `source_type`, `source_id`, `category`, `description`, `gift_id`, `amount`, `direction`, `create_time`, `effect_time`, `effect_to_history`, `message`, `gift_value` from `user_gift_entry` where `user_id` = ? ;";
                Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$userid"));
                $row = $stmt->fetch();
                if(empty($row)){
                   $row = array();
                }
                return $row;
        }

}
