<?php
apf_require_class("APF_DB_Factory");

class Dao_Article_Test {
  
    private $pdo;

    public function __construct() {
        $this->pdo = APF_DB_Factory::get_instance()->get_pdo("article_master"); // admin master
    }
	public function test($a){
		$sql = "select * from t_article;";
		//$this->pdo->query("set names utf8");
		$this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$this->pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();
		return $stmt->fetchall(PDO::FETCH_ASSOC);
	}
	
	public function add_log($data){
		$data['created_at'] = time();
		Logger::info(__FILE__, __CLASS__, __LINE__, var_export($data, true));
		$sql = "insert into t_log (aid, kkid, token, type, created_at, ip)values(:aid, :kkid, :token, :type, :created_at, :ip);";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($data);
		self::incr_num($data['type'], $data['aid']);
	}

	public function incr_num($type, $aid){
		$sql = "update t_article set ";
		if ($type == 1){
			$sql .= " viewed = viewed + 1";
		} elseif ($type ==2) {
			$sql .= " shared = shared + 1";
		}
		$sql .= " where aid = {$aid};";
		$stmt = $this->pdo->prepare($sql);
		return $stmt->execute();
	}
	
	public function get_article_by_aid($aid){
		$row = [];
		$sql = "select * from t_article where active = 1 and aid = ? limit 1;";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute(array($aid));
		$row = $stmt->fetch();
		return $row;
	}

	public function get_article_by_p_kkid($p_kkid){
		$row = [];
		$sql = "select * from t_relate where active = 1 and id_product = ?;";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute(array($p_kkid));
		$row = $stmt->fetchAll();
		return $row;
	}
  	
	public function get_article_by_keyword_admin($kwd, $data){
		$row = array();
		$sql = "select * from t_article where 1";
		if($kwd) {
			$sql .= " and title like '%$kwd%'";
		}
		
		$sql .= " and active =1 order by created_at desc";
		
		if ($data) {
			$sql .= " limit ".$data['offset'].", ".$data['page_size'].";";
		}
		Logger::info(__FILE__, __CLASS__, __LINE__, 'sql: '.$sql);
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();
		$row = $stmt->fetchAll();
		if(empty($row)) {
			return array();
		}
		
		return $row;
	} 
	public function add_article($data){
		$data['created_at'] = $data['updated_at'] = time();
		$sql = "insert into t_article (title, content, author, active, show_type, created_by, updated_by, created_at, updated_at)values(:title, :content, :author, :active, :show_type, :created_by, :updated_by, :created_at, :updated_at);";
		Logger::info(__FILE__, __CLASS__, __LINE__, var_export($data, true));
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($data);
		$hid = $this->pdo->lastInsertId();
		if(!$hid) {
			return 0;
		}
		return $hid;
	}
		
	public function edit_article($data){
		unset($data['belong']);
		$data['updated_at'] = time();
        $sql = "update t_article set title = :title, content = :content, author = :author, active = :active, show_type = :show_type, updated_by = :updated_by, updated_at = :updated_at where aid = :aid;"; 
		Logger::info(__FILE__, __CLASS__, __LINE__, var_export($data, true));
        $stmt = $this->pdo->prepare($sql);
		/*
		$stmt->bindParam(':title', $data['title'], PDO::PARAM_STR);
		$stmt->bindParam(':content', $data['content'], PDO::PARAM_STR);
		$stmt->bindParam(':author', $data['author'], PDO::PARAM_INT);
        $stmt->bindParam(':active', $data['active'], PDO::PARAM_INT);
		$stmt->bindParam(':show_type', $data['show_type'], PDO::PARAM_INT);
		*/
		$res = $stmt->execute($data);
        if(!$res) {
            return false;
        }   
        return $res;
	}
		
	public function del_article($data){
		$data['updated_at'] = time();
		$sql = "update t_article set active = -1, updated_at = :updated_at, updated_by = :updated_by  where aid = :aid;";
		Logger::info(__FILE__, __CLASS__, __LINE__, var_export($data, true));
		$stmt = $this->pdo->prepare($sql);
		$res = $stmt->execute($data);
		return $res;
	}

	public function get_article_admin($aid){
		$row = array();
		$sql = "select * from t_article where aid = ?;";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute(array($aid));
		$row = $stmt->fetch();
		if(empty($row)){
			return array();
		}
		return $row;
	}

	public function view_article($aid){
		$row = array();
		$sql = "select * from t_article where active != -1 and aid = ?;";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($array($aid));
		$row = $stmt->fetch();
		if(empty($row)){
			return array();
		}
		return $row;	
	}

	public function count_article($data){
		$m = 0;
		$sql = "select count(*) from t_article where 1";
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
}
