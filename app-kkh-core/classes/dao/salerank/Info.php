<?php
apf_require_class("APF_DB_Factory");

class Dao_Salerank_Info {
  
    private $pdo;

    public function __construct() {
        $this->pdo = APF_DB_Factory::get_instance()->get_pdo("shop_master"); // admin master
    }
	
	public function add($data){
		$data['created_at'] = $data['updated_at'] = time();
		$sql = "insert into s_salerank (id_product, pos, active, created_by, updated_by, created_at, updated_at)values(:id_product, :pos, :active, :created_by, :updated_by, :created_at, :updated_at);";
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
        $sql = ""; 
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
		$sql = "update s_salerank set active = -1 , updated_at = :updated_at, updated_by = :updated_by where id = :id;";
		Logger::info(__FILE__, __CLASS__, __LINE__, var_export($data, true));
		$stmt = $this->pdo->prepare($sql);
		$res = $stmt->execute($data);
		return $res;
	}

	public function get_list_admin(){
		$row = array();
		$sql = "select * from s_salerank where active != -1 order by created_at desc;";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();
		$row = $stmt->fetchAll();
		if(empty($row)){
			return array();
		}
		return $row;
	}

	public function get_product_detail($data){
		$row = [];
		$sql = "select name, id_product from s_product_lang where id_product = :id_product and id_lang=1 limit 1;";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($data);
		$row = $stmt->fetch();
		if(!$row){
			return array();
		}
		return $row;
	}

	public function check_repeat($data){
		$row = [];
		$sql = "select * from s_salerank where id_product = :id_product and active != -1;";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($data);
		$row = $stmt->fetch();
		if(!$row){
			return array();
		}
		return $row;
	}

	public function view($aid){
		$row = array();
		$sql = "select * from s_salerank where active != -1 and id = ?;";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute(array($aid));
		$row = $stmt->fetch();
		if(empty($row)){
			return array();
		}
		return $row;	
	}

	#获取商品列表
	public function get_product_list(){
		$row = [];
		$id_str = '0,';
		#已存在排行榜得，不会在列表出现
		$existe  = self::get_list_admin();
		if($existe){
			foreach($existe as $k=>$v){
				$id_str .= $v['id_product'] . ',';
			}
		}
		$id_str = rtrim($id_str, ',');
		$sql = "select a.kkid, a.id_product, a.id_product_kkh, b.name from s_product a left join s_product_lang b on a.id_product = b.id_product where a.active=1 and b.id_lang=1 and a.id_product not in ($id_str) order by a.id_product desc;";
		Logger::info(__FILE__,__CLASS__,__LINE__,'sql :'.$sql);
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();
		$row = $stmt->fetchAll();
		if(!$row){
			return array();
		}
		return $row;
	}

}
