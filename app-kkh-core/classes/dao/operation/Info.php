<?php
apf_require_class("APF_DB_Factory");

class Dao_Operation_Info {
  
    private $pdo;

    public function __construct() {
        $this->pdo = APF_DB_Factory::get_instance()->get_pdo("shop_master"); // admin master
    }
	
	public function add($data){
		$data['date_upd']    = date('Y-m-d H:i:s', time());
		$data['create_at'] = date('Y-m-d H:i:s', time());
		$sql = "insert into s_operation_img (type,name,is_show,update_at,img_url,name,created_at)values(:type, :name, :is_show, :date_upd, :img_url, :name,:create_at);";
		Logger::info(__FILE__, __CLASS__, __LINE__, var_export($data, true));
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($data);
		$hid = $this->pdo->lastInsertId();
		if(!$hid) {
			return 0;
		}
		return $hid;
	}
		
	public function name_edit($data){
		$data['date_upd'] = date('Y-m-d H:i:s', time());
		$sql = "update s_operation_img set name = :name,updated_at=:date_upd  where id = :id";
		Logger::info(__FILE__, __CLASS__, __LINE__, var_export($data, true),$sql);
        $stmt = $this->pdo->prepare($sql);
		$res = $stmt->execute($data);
        if(!$res) {
            return false;
        }   
        return $res;
	}

    public function img_edit($data){
        $data['date_upd'] = date('Y-m-d H:i:s', time());
        $sql = "update s_operation_img set img_url = :img_url,updated_at=:date_upd  where id = :id";
        Logger::info(__FILE__, __CLASS__, __LINE__, var_export($data, true));
        $stmt = $this->pdo->prepare($sql);
        $res = $stmt->execute($data);
        if(!$res) {
            return false;
        }
        return $res;
    }

	//同时修改name 跟 img

	public function edit_info($data)
	{
	    $data['date_upd'] = date('Y-m-d H:i:s', time());
		$sql = "update s_operation_img set img_url = :img_url,updated_at=:date_upd,name=:name  where id =:id";
		Logger::info(__FILE__, __CLASS__, __LINE__, json_encode($data)."=====$sql");
		$stmt = $this->pdo->prepare($sql);
		$res = $stmt->execute($data);
		if(!$res) {
		    return false;
		}
		return $res;


	}

	public function operation_list(){
		$row = array();
		$sql = "select * from s_operation_img where is_show != 0;";
	    $stmt = $this->pdo->prepare($sql);
		$stmt->execute();
		$row = $stmt->fetchAll();
		if(empty($row)){
			return array();
		}
		return $row;
	}




}
