<?php
/**
 * Created by PhpStorm.
 * User: HP
 * Date: 2018/3/20
 * Time: 20:03
 */
apf_require_class("APF_DB_Factory");
class Dao_Yxf_Info
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = APF_DB_Factory::get_instance()->get_pdo("shop_master");
    }

    public function get_info($yid){
        $data['id'] = $yid;
        $sql = "SELECT * FROM `s_yxf_test`";
        Logger::info(__FILE__, __CLASS__, __LINE__, $sql." ".$yid);
        $stmt = $this->pdo->prepare($sql);
        $res = $stmt->execute();
        $row = $stmt->fetchAll();
       
        Logger::info(__FILE__, __CLASS__, __LINE__, json_encode($row));
        return $row;
    }
   
    public function yxf_insert($data){
      $sql = "INSERT INTO `s_yxf_test` (`id`,`name`,`action`) VALUES(:id,:name,:action)";
      $stmt = $this->pdo->prepare($sql);
      $res  = $stmt->execute($data);
      $last_id = $this->pdo->lastInsertId();
      return $last_id;
    }

	public function yxf_update($id,$data){
		$data['id'] = $id;
		$sql = "UPDATE `s_yxf_test` SET `name`=:name,`action`=:action WHERE `id`=:id";
		$stmt = $this->pdo->prepare($sql);
        $res = $stmt->execute($data);
    
	   	return $res;
	} 

}
