<?php
apf_require_class("APF_DB_Factory");

class Dao_Department_KkhInfo {

	private $pdo;

	public function __construct() {
		$this->pdo = APF_DB_Factory::get_instance()->get_pdo("green_master");
	}

        public function create_department($data) {
                unset($data['kkid']);
                unset($data['update_date']);
                $sql = "insert into `department` (`id`, `hospital_id`, `name`, `sequence`, `ref_specialty_id`, `category`, `kkid`, `update_date`) values(:id, :hospital_id, :name, :sequence, :ref_specialty_id, :category, replace(upper(uuid()),'-',''), now());";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                $last_id = $this->pdo->lastInsertId();
                return $last_id;
        }

        public function set_department($data) {
                unset($data['kkid']);
                unset($data['update_date']);
                $sql = "update `department` set `hospital_id` = :hospital_id, `name` = :name, `sequence` = :sequence, `ref_specialty_id` = :ref_specialty_id, `category` = :category where `id` = :id ;";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                return $res;
        }

        public function get_department($id) {
                $row = array();
                $sql = "select `id`, `hospital_id`, `name`, `sequence`, `ref_specialty_id`, `category`, `kkid`, `update_date` from `department` where `id` = ? ;";
                Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$id"));
                $row = $stmt->fetch();
                if(empty($row)){
                   $row = array();
                }
                return $row;
        }

        public function get_department_by_kkid($kkid) {
                $row = array();
                $sql = "select `id`, `hospital_id`, `name`, `sequence`, `ref_specialty_id`, `category`, `kkid`, `update_date` from `department` where `kkid` = ? ;";
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
