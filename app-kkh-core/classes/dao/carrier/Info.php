<?php
apf_require_class("APF_DB_Factory");

class Dao_Carrier_Info {

	private $pdo;

	public function __construct() {
	    $this->pdo = APF_DB_Factory::get_instance()->get_pdo("shop_master");
	}


        public function get_carrier_fee($name) {
            $row = array();
/*            $sql = "select `id`, `name`, `name_code`, `feevalue`, `deleted`, `created`, `update_date` from `t_carrier_fee_config` where `name` = ? ;";
            //Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array("$name"));
            
            $row = $stmt->fetch();
            if(isset($row['id']) && empty($row['id'])){
            }
*/            if(empty($row)){
               $row = array(
                    'id' => 0,
                    'name' => '',
                    'name_code' => '',
                    'feevalue' => '0',//'15',
                    'deleted' => 0,
                    'created' => 0,
                    'update_date' => '' 
                );
            }
            return $row;
        }

/*
*/
}
