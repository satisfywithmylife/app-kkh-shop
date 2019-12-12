<?php
apf_require_class("APF_DB_Factory");

class Dao_Article_Info {
  
    private $pdo;

    public function __construct() {
        $this->pdo = APF_DB_Factory::get_instance()->get_pdo("article_master"); // admin master
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
		$sql = "select * from t_article where 1 and aid = ? limit 1;";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute(array($aid));
		$row = $stmt->fetch();
		return $row;
	}

	public function get_article_by_p_kkid($p_kkid){
		$row = [];
		$sql = "select * from t_relate a left join t_article b on a.aid = b.aid where a.active = 1 and b.active = 1 and a.id_product = ? order by b.created_at desc;";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute(array($p_kkid));
		$row = $stmt->fetchAll();
		return $row;
	}
  	
	public function get_article_by_keyword_admin($kwd, $data){
		$row = array();
		$sql = "select * from t_article where 1";
		if($kwd) {
			$sql .= " and title like '%$kwd%' or content like '%$kwd%'";
		}
		
		$sql .= " and active != -1 order by created_at desc";
		
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
		$sql = "update t_article set active = :active, updated_at = :updated_at, updated_by = :updated_by  where aid = :aid;";
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

	//获取所有文章列表
    public function get_article_list()
    {
        $sql = "SELECT `aid`,`title`,`show_type`,`active`,`updated_at`,`created_at` FROM `t_article` WHERE active =1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetchAll();
        if(!$row){
            return array();
        }
        return $row;
    }
    //获取标题信息
    public function get_headline(){
        $sql = "SELECT * FROM `t_headline` ORDER BY hid DESC LIMIT 1";
		Logger::info(__FILE__, __CLASS__, __LINE__, '+++++++++++++'.$sql);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch();
        if(!$row){
            return array();
        }
        return $row;
    }

	//通过标题id获取标题信息
	public function get_headline_by_id($hid)
	{
	   $sql = "SELECT * FROM `t_headline` WHERE hid =?";
	   $stmt = $this->pdo->prepare($sql);
	   Logger::info(__FILE__, __CLASS__, __LINE__, '+++++++++++++'.$hid);
	   $stmt->execute(array($hid));
	   $row = $stmt->fetch();
	   if(!$row){
	     return array();
	   }
	   return $row;

	}

    /**
     * @param $hid 大标题id
     */
    public function get_headline_article_list1($hid,$limit="")
    {
	    if($limit)
		{
		  $num = " LIMIT $limit";
		  $sql = "SELECT
		  	             a.`aid`,
				         a.`hid`,
					     a.`title`,
						 a.`position`,
					     a.`show_type`,
						 a.`active`,
						 a.`updated_at`,
						 a.`created_at`,
						 i.imgurl,
						 i.id
				  FROM
						`t_article` a 
				  LEFT JOIN 
				        `t_image` i 
				  ON 
				         a.aid = i.aid
				  WHERE
						 a.active = 1
				  AND 
				        i.is_share = 1 
				  AND 
				        i.active  =1 
				  AND 
				         a.hid =?
				  GROUP BY 
				         a.aid
				  ORDER BY
						a.position desc,
						a.updated_at 
				  DESC ";
		  $sql .= $num;
		}
		else
		{
		    $sql  = "SELECT `aid`,`hid`,`title`,`position`,`show_type`,`active`,`updated_at`,`created_at` FROM `t_article` WHERE active =1 AND hid=? ORDER BY updated_at DESC";
		}
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array($hid));
        $row = $stmt->fetchAll();
        if(empty($row)){
            return array();
        }
        return $row;
    }
	//获取大标题下的文章列表
	public function get_headline_article_list($hid,$limit="")
	{
	   if($limit)
	   {
	       $num = " LIMIT $limit";
		   $sql = "SELECT
		   				hl.*,
			  			i.id as iid,
			  			i.imgurl,
						a.title,
						a.show_type
					FROM
						t_headline_article_list AS hl,
						t_article AS a,
						t_image AS i
					WHERE
						hl.hid =?
					AND hl.aid = a.aid
					AND hl.is_online = 1
					AND a.aid = i.aid
					AND a.active = 1
					AND i.active = 1
					AND i.is_share = 1
					GROUP BY a.aid
					ORDER BY
						hl.position DESC,
						hl.created_at DESC";
			$sql .= $num;

	   }
	   else
	   {
	       $sql = "SELECT
					    hl.*,
						i.id as iid,
						i.imgurl,
						a.title,
						a.show_type
					FROM
						t_headline_article_list AS hl,
						t_article AS a,
						t_image AS i
					WHERE
						hl.hid =?
						AND hl.aid = a.aid
						AND hl.is_online = 1
						AND a.aid = i.aid
						AND a.active = 1
						AND i.active = 1
						AND i.is_share = 1
						GROUP BY a.aid
					ORDER BY
						hl.created_at DESC";
	   }
		
	    $stmt = $this->pdo->prepare($sql);
		$stmt->execute(array($hid));
		$row = $stmt->fetchAll();
		if(empty($row)){
			return array();
		}
		return $row;

	    
	}
    //大标题下文章新增
    public function add_headline_article($data)
	{
	    $data['updated_at'] = time();
		$data['created_at'] = time();
		$sql = "INSERT INTO `t_headline_article_list`(aid,hid,position,created_at,updated_at) VALUES(:aid,:hid,:position,:created_at,:updated_at) ";
		$stmt = $this->pdo->prepare($sql);
		$res = $stmt->execute($data);
		return $res;
	}

	public function del_headline_article($data)
	{
	    $sql = "DELETE FROM `t_headline_article_list` WHERE aid=:aid AND hid=:hid";
		$stmt = $this->pdo->prepare($sql);
		$res = $stmt->execute($data);
		return $res;
	}

    /**
     * @param $data
     * @return bool
     * 修改标题内容
     */
    public function edit_headline($data)
    {
        $data['update_time'] = date("Y-m-d H:i:s");
        $sql = "UPDATE `t_headline` SET headline=:headline,subhead=:subhead,updated_at=:update_time WHERE hid = :hid";
        $stmt = $this->pdo->prepare($sql);
        $res = $stmt->execute($data);
        return $res;
    }

    /**
     * @param $data
     * @return string
     * 添加标题
     */
    public function add_headline($data)
    {
        $data['update_time'] = date("Y-m-d H:i:s");
		$data['created_at']  = date("Y-m-d H:i:s");
        $sql = "INSERT INTO `t_headline` (`headline`,`subhead`,`updated_at`,`created_at`) VALUES(:headline,:subhead,:update_time,:created_at)";
        $stmt = $this->pdo->prepare($sql);
        $res = $stmt->execute($data);
        $hid = $this->pdo->lastInsertId();
        return $hid;
    }

}
