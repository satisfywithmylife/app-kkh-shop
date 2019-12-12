<?php
apf_require_class("APF_DB_Factory");

class Dao_Apost_Info {
  
    private $pdo;

    public function __construct() {
        $this->pdo = APF_DB_Factory::get_instance()->get_pdo("shop_master"); // admin master
    }
	
	public function add($data){
		$data['date_upd'] = date('Y-m-d H:i:s', time());
		$data['created_at'] = date('Y-m-d H:i:s', time()); 
		$sql = "insert into s_banner (id_product, description,  act_url, pos, imgurl, type, active, date_upd, created_at,name,share_title,share_img)values(:id_product, :description, :act_url, :pos, :imgurl, :type, :active, :date_upd, :created_at,:name,:share_title,:share_img);";
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
		$data['date_upd'] = date('Y-m-d H:i:s', time());
		$sql = "update s_banner set id_product = :id_product, description = :description, act_url = :act_url, pos = :pos, imgurl = :imgurl, type = :type, active = :active, date_upd = :date_upd,share_title=:share_title,share_img=:share_img where id = :id";
		Logger::info(__FILE__, __CLASS__, __LINE__, var_export($data, true));
        $stmt = $this->pdo->prepare($sql);
		$res = $stmt->execute($data);
        if(!$res) {
            return false;
        }   
        return $res;
	}
		
	public function del($data){
		$data['date_upd'] = date("Y-m-d H:i:s", time());
		$sql = "update s_banner set active = -1, date_upd = :date_upd  where id = :id;";
		Logger::info(__FILE__, __CLASS__, __LINE__, var_export($data, true));
		$stmt = $this->pdo->prepare($sql);
		$res = $stmt->execute($data);
		return $res;
	}

	public function get_banner_admin($id){
		$row = array();
		$sql = "select * from s_banner where id = ?;";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute(array($id));
		$row = $stmt->fetch();
		if(empty($row)){
			return array();
		}
		return $row;
	}

	public function view($id){
		$row = array();
		$sql = "select * from s_banner where active != -1 and id = ?;";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute(array($id));
		$row = $stmt->fetch();
		if(empty($row)){
			return array();
		}
		return $row;	
	}

	public function count_list($data){
		$m = 0;
		$sql = "select count(*) from s_banner where 1";
		if ($data['active']) {
			$sql .= " and active = 1";
		}
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();
		$m = $stmt->fetchColumn();
		if (!$m) {
			return 0;
		}
		return $m;
	}
	
	public function banner_list(){
		$row = array();
		$sql = "select * from s_banner where active != -1 order by created_at desc;";
	    $stmt = $this->pdo->prepare($sql);
		$stmt->execute();
		$row = $stmt->fetchAll();
		if(empty($row)){
			return array();
		}
		return $row;
	}




}
