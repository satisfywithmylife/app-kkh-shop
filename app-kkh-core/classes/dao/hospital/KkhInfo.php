<?php
apf_require_class("APF_DB_Factory");

class Dao_Hospital_KkhInfo {

	private $pdo;

	public function __construct() {
		$this->pdo = APF_DB_Factory::get_instance()->get_pdo("green_master");
	}

        public function create_hospital($data) {
                unset($data['kkid']);
                unset($data['update_date']);
                $sql = "insert into `hospital` (`id`, `name`, `grade`, `feature`, `address`, `phone`, `lat_google`, `lng_google`, `lat_baidu`, `lng_baidu`, `area_id`, `enable`, `pinyin`, `ishot`, `sequence`, `external_id`, `short_name`, `extra`, `sales_id`, `short_address`, `abbr`, `kkid`, `update_date`) values(:id, :name, :grade, :feature, :address, :phone, :lat_google, :lng_google, :lat_baidu, :lng_baidu, :area_id, :enable, :pinyin, :ishot, :sequence, :external_id, :short_name, :extra, :sales_id, :short_address, :abbr, replace(upper(uuid()),'-',''), now());";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                $last_id = $this->pdo->lastInsertId();
                return $last_id;
        }

        public function set_hospital($data) {
                unset($data['kkid']);
                unset($data['update_date']);
                $sql = "update `hospital` set `name` = :name, `grade` = :grade, `feature` = :feature, `address` = :address, `phone` = :phone, `lat_google` = :lat_google, `lng_google` = :lng_google, `lat_baidu` = :lat_baidu, `lng_baidu` = :lng_baidu, `area_id` = :area_id, `enable` = :enable, `pinyin` = :pinyin, `ishot` = :ishot, `sequence` = :sequence, `external_id` = :external_id, `short_name` = :short_name, `extra` = :extra, `sales_id` = :sales_id, `short_address` = :short_address, `abbr` = :abbr where `id` = :id ;";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                return $res;
        }

        public function get_hospital($id) {
                $row = array();
                $sql = "select `id`, `name`, `grade`, `feature`, `address`, `phone`, `lat_google`, `lng_google`, `lat_baidu`, `lng_baidu`, `area_id`, `enable`, `pinyin`, `ishot`, `sequence`, `external_id`, `short_name`, `extra`, `sales_id`, `short_address`, `abbr`, `kkid`, `update_date` from `hospital` where `id` = ? ;";
                Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$id"));
                $row = $stmt->fetch();
                if(empty($row)){
                   $row = array();
                }
                return $row;
        }

        public function get_hospital_by_kkid($kkid) {
                $row = array();
                $sql = "select `id`, `name`, `grade`, `feature`, `address`, `phone`, `lat_google`, `lng_google`, `lat_baidu`, `lng_baidu`, `area_id`, `enable`, `pinyin`, `ishot`, `sequence`, `external_id`, `short_name`, `extra`, `sales_id`, `short_address`, `abbr`, `kkid`, `update_date` from `hospital` where `kkid` = ? ;";
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
