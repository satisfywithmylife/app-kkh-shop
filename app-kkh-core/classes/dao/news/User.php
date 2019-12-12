<?php
apf_require_class("APF_DB_Factory");

class Dao_News_User {

	private $pdo;

	public function __construct() {
		$this->pdo = APF_DB_Factory::get_instance()->get_pdo("news_master");
	}
    
    public function get_user_by_min_openid($openid){
        $sql = "select * from t_user where min_openid = ? limit 1;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array($openid));
        $res = $stmt->fetch();
        if(!$res){
            $res = [];
        }
        return $res;
    }

    public function add_user($data){
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $cols = array();
        $vals = array();
        foreach ($data as $key => $val) {
            $cols[] = $key;
            $vals[] = $val;
        }


        $sql = "INSERT INTO t_user (" . implode(', ', $cols) . ") VALUES ('" . implode("', '", $vals) . "');";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $id = $this->pdo->lastInsertId();
        return $id;
    }

    public function update_user($data){
        $sql = "update t_user set nick_name = :nick_name, avatar = :avatar where min_openid = :min_openid;";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }
}

