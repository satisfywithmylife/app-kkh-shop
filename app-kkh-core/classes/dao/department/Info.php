<?php
apf_require_class("APF_DB_Factory");

class Dao_Department_Info {

	private $pdo;

	public function __construct() {
		$this->pdo = APF_DB_Factory::get_instance()->get_pdo("master");
	}

        public function get_department($hd_kkid)
        {
            if(empty($hd_kkid)){
                return array();
            }
            $department = array();
            $sql = "select kkid, h_kkid, name, intro from t_department where kkid = ? and status = 1  limit 1;";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array("$hd_kkid"));
            $department = $stmt->fetch();
            return $department;
        }

}
