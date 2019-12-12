<?php
apf_require_class("APF_DB_Factory");
class Dao_Postage_Info
{
    private $pdo;
    public function __construct()
	{
	    $this->pdo = APF_DB_Factory::get_instance()->get_pdo("shop_master");
	}

    public function get_postage_info()
	{
	    $sql  = "SELECT * FROM `s_postage`";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();
		$row = $stmt->fetchAll();
		if(!$row)
		{
		    return array();
		}
		else
		{
		    return $row;
		}

	}
}
