<?php
apf_require_class("APF_DB_Factory");

class Dao_Cornertags_Info {
  
    private $pdo;

    public function __construct() {
        $this->pdo = APF_DB_Factory::get_instance()->get_pdo("shop_master"); // admin master
    }
	
	public function add($data){
		$data['created_at'] = $data['updated_at'] = time();
		$sql = "insert into s_cornertags (title, img_url, active, created_by, updated_by, created_at, updated_at)values(:title, :img_url, :active, :created_by, :updated_by, :created_at, :updated_at);";
		Logger::info(__FILE__, __CLASS__, __LINE__, var_export($data, true));
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($data);
		$hid = $this->pdo->lastInsertId();
		if(!$hid) {
			return 0;
		}
		return $hid;
	}
		
	public function edit($data){
		$data['updated_at'] = time();
        $sql = "update s_cornertags set title = :title, img_url = :img_url, updated_at = :updated_at, updated_by = :updated_by where id = :id;"; 
		Logger::info(__FILE__, __CLASS__, __LINE__, var_export($data, true));
        $stmt = $this->pdo->prepare($sql);
		$res = $stmt->execute($data);
        if(!$res) {
            return false;
        }   
        return $res;
	}
		
	public function del($data){
		$data['updated_at'] = time();
		$sql = "update s_cornertags set active = :active , updated_at = :updated_at, updated_by = :updated_by where id = :id;";
		Logger::info(__FILE__, __CLASS__, __LINE__, var_export($data, true));
		$stmt = $this->pdo->prepare($sql);
		$res = $stmt->execute($data);
		return $res;
	}

	public function get_list_admin(){
		$row = array();
		$sql = "select * from s_cornertags where active != -1 order by created_at desc;";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();
		$row = $stmt->fetchAll();
		if(empty($row)){
			return array();
		}
		return $row;
	}

	public function view($id){
		$row = array();
		$sql = "select * from s_cornertags where active != -1 and id = ?;";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute(array($id));
		$row = $stmt->fetch();
		if(empty($row)){
			return array();
		}
		return $row;	
	}

}
