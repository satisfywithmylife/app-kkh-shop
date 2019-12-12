<?php
apf_require_class("APF_DB_Factory");

class Dao_Article_Belong {
  
    private $pdo;

    public function __construct() {
        $this->pdo = APF_DB_Factory::get_instance()->get_pdo("article_master"); // admin master
    }
   
	public function add_article_to_product($data, $aid){
		self::update($aid);
		$sql = "insert into t_relate (id_product, created_at, updated_at, aid)values(:id_product, :created_at, :updated_at, :aid);";
		$stmt = $this->pdo->prepare($sql);
		Logger::info(__FILE__, __CLASS__, __LINE__, var_export($data, true));
		Logger::info(__FILE__, __CLASS__, __LINE__, 'aid: '.$aid);
		$i = 0;
		foreach ($data as $k=>$v) {
			$v = (int)$v;
			if (!is_numeric($v) || $v <= 0){
				continue;
			}
			$stmt->bindParam(':id_product', $v, PDO::PARAM_INT);
			$stmt->bindParam(':created_at', time(), PDO::PARAM_INT);
			$stmt->bindParam(':updated_at', time(), PDO::PARAM_INT);
			$stmt->bindParam(':aid', $aid, PDO::PARAM_INT);
			$res = $stmt->execute();
			if(!$res){
				$i++;
			} 
		}
		if ($i > 0) {
			return false;
		}
		return true;
	}
		
	public function update($aid){
        $sql = "update t_relate set active = 0 where aid = ? and active = 1;";
        Logger::info(__FILE__, __CLASS__, __LINE__, var_export($aid, true));
        $stmt = $this->pdo->prepare($sql);
        $res = $stmt->execute(array($aid));
        if(!$res) {
            return false;
        }   
        return $res;	
	}

	public function get_belong_admin($aid){
		$res = [];
		$sql = "select id_product from t_relate where aid = ? and active = 1;";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute(array($aid));
		$res = $stmt->fetchAll();
		if (empty($res)) {
			return array();
		}
		return $res;
	}
		
	public function count_list($data){
		$m = 0;
		$sql = "select count(*) c from t_image where 1";
		if ($data['active']) {
			$sql .= " and active = 1";
		}
		if ($data['aid']) {
			$sql .= " where aid = ".$data['aid'];
		}
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();
		$m = $stmt->fetchColumn();
		if (!$m) {
			return 0;
		}
		return $m;
	}
}
