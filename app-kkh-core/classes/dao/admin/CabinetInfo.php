<?php
apf_require_class("APF_DB_Factory");

class Dao_Admin_CabinetInfo {
  
  	private $pdo;

  	public function __construct() {
   		$this->pdo = APF_DB_Factory::get_instance()->get_pdo("admin_master"); // admin master
  	}

	public function set_cabinet_default_id_customer($id_customer, $cd_key){
		$sql = "update t_cabinet_cabinet set id_customer = ? where cd_key = ?;";
		$stmt = $this->pdo->prepare($sql);
		return $stmt->execute(array($id_customer, $cd_key));
	}

	public function get_cabinet($cd_key){
		$sql = "select * from t_cabinet_cabinet where cd_key = ? limit 1;";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute(array($cd_key));
		$res = $stmt->fetch();
		if(!$res){
			return array();
		}
		return $res;
	}

	public function get_device_by_hospital_id($hospital_id){
		if(!$hospital_id) return array();

		$sql = "select id_hospital, cd_key,id from t_cabinet_cabinet where id_hospital = ? and cabinet_status = 1;";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute(array($hospital_id));
		$res = $stmt->fetchAll();
		if(!$res){
			return array();
		}
		$resm = [];
		foreach($res as $k=>$v){
			$resm[$v['id_hospital']][] = $v['cd_key'];
		}
		return $resm;
	}

	public function get_unlocked_num($cd_key, $id_product){
		if(!$cd_key || !$id_product) return 0;
		$sql = "select current_num from t_cabinet_stock where id_product = ? and cd_key = ? limit 1;";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute(array($id_product, $cd_key));
		$res = $stmt->fetchColumn();
		if(!$res){
			return 0;
		}
		return $res;
	}

	public function get_productlist_by_cd_key($cd_key, $page_start, $page_size){
		if(!$cd_key) return array();
		//Logger::info(__FILE__, __CLASS__, __LINE__, '||'.$cd_key . $page_start . $page_size);
		$sql = "select * from t_cabinet_stock where cd_key = :cd_key LIMIT :limit OFFSET :offset;";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindParam(':cd_key', $cd_key, PDO::PARAM_STR);
		$stmt->bindParam(':limit', $page_size, PDO::PARAM_INT);
		$stmt->bindParam(':offset', $page_start, PDO::PARAM_INT);
		$stmt->execute();
		$res = $stmt->fetchAll();
		//Logger::info(__FILE__, __CLASS__, __LINE__, var_export($res, true));
		if(!$res){
			return array();
		}
		return $res;
	}

	public function get_product_count($cd_key){
        if(!$cd_key) return 0;
        $sql = "select count(*) as c from t_cabinet_stock where cd_key = :cd_key;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':cd_key', $cd_key, PDO::PARAM_STR);
        $stmt->execute();
        $res = $stmt->fetchColumn();
        if(!$res){
            return 0;
        }   
        return $res;
		
	}

	public function get_product($cd_key, $id_product){
		$sql = "select * from t_cabinet_stock where cd_key = ? and id_product = ? limit 1;";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute(array($cd_key, $id_product));
		$res = $stmt->fetch();
		if(!$res){
			return array();
		}
		return $res;
	}
}
