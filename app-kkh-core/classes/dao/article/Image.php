<?php
apf_require_class("APF_DB_Factory");

class Dao_Article_Image {
  
    private $pdo;

    public function __construct() {
        $this->pdo = APF_DB_Factory::get_instance()->get_pdo("article_master"); // admin master
    }

	public function get_show_image($aid, $show_type){
		$row = $img_arr = [];
		$show_type = (int)$show_type;
		$sql = "select imgurl from t_image where active = 1 and aid = :aid and is_share = 0 order by updated_at ASC LIMIT :limit;";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindParam(':aid', $aid, PDO::PARAM_INT);
		$stmt->bindParam(':limit', $show_type, PDO::PARAM_INT);
		$stmt->execute();
		$row = $stmt->fetchAll();
		foreach ($row as $k=>$v){
			$img_arr[] = $v['imgurl'];
		}
		return $img_arr;
	}

	public function get_share_image($aid){
		$res = '';
		$sql = "select imgurl from t_image where aid = ? and active = 1 and is_share = 1 order by updated_at desc limit 1;";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute(array($aid));
		$res = $stmt->fetchColumn();

		return $res;
	}
   
	public function add_image($data, $aid, $type){
		if($type ==1) {
			$sql = "insert into t_image (pos, imgurl, created_at, updated_at, aid, is_share)values(0, :imgurl, :created_at, :updated_at, :aid, 1);";
			$stmt = $this->pdo->prepare($sql);
            //$stmt->bindParam(':pos', 0, PDO::PARAM_INT);
            $stmt->bindParam(':imgurl', $data, PDO::PARAM_STR);
            $stmt->bindParam(':created_at', time(), PDO::PARAM_INT);
            $stmt->bindParam(':updated_at', time(), PDO::PARAM_INT);
            $stmt->bindParam(':aid', $aid, PDO::PARAM_INT);
            $res = $stmt->execute();
			return $res;
		}
		self::update($aid);
		$sql = "insert into t_image (pos, imgurl, created_at, updated_at, aid, is_share)values(:pos, :imgurl, :created_at, :updated_at, :aid, 0);";
		$stmt = $this->pdo->prepare($sql);
		Logger::info(__FILE__, __CLASS__, __LINE__, var_export($data, true));
		Logger::info(__FILE__, __CLASS__, __LINE__, 'aid: '.$aid);
		$i = 0;
		foreach ($data as $k=>$v) {
			$stmt->bindParam(':pos', $k, PDO::PARAM_INT);
			$stmt->bindParam(':imgurl', $v, PDO::PARAM_STR);
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
        $sql = "update t_image set active = 0 where aid = ? and active = 1;";
        Logger::info(__FILE__, __CLASS__, __LINE__, var_export($aid, true));
        $stmt = $this->pdo->prepare($sql);
        $res = $stmt->execute(array($aid));
        if(!$res) {
            return false;
        }   
        return $res;
	}
		
	public function count_image($data){
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
