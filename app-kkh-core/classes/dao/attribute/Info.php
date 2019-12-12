<?php
apf_require_class("APF_DB_Factory");

class Dao_Attribute_Info {

	private $pdo;

	public function __construct() {
	    $this->pdo = APF_DB_Factory::get_instance()->get_pdo("shop_master");
	}

        public function create_product_attribute($data) {
            $sql = "insert into `s_product_attribute` (`id_product_attribute`, `id_product`, `attribute_config`, `reference`, `supplier_reference`, `location`, `ean13`, `isbn`, `upc`, `wholesale_price`, `price`, `ecotax`, `quantity`, `weight`, `unit_price_impact`, `default_on`, `minimal_quantity`, `available_date`) values(:id_product_attribute, :id_product, :attribute_config, :reference, :supplier_reference, :location, :ean13, :isbn, :upc, :wholesale_price, :price, :ecotax, :quantity, :weight, :unit_price_impact, :default_on, :minimal_quantity, :available_date);";
            $stmt = $this->pdo->prepare($sql);
            $res = $stmt->execute($data);
            $last_id = $this->pdo->lastInsertId();
            return $last_id;
        }

        public function create_product_attribute_combination($data) {
            $sql = "insert into `s_product_attribute_combination` (`id_attribute`, `id_product_attribute`) values(:id_attribute, :id_product_attribute);";
            $stmt = $this->pdo->prepare($sql);
            return $res = $stmt->execute($data);
        }

        public function create_product_attribute_shop($data) {
            $sql = "insert into `s_product_attribute_shop` (`id_product`, `id_product_attribute`, `id_shop`, `wholesale_price`, `price`, `ecotax`, `weight`, `unit_price_impact`, `default_on`, `minimal_quantity`, `available_date`) values(:id_product, :id_product_attribute, :id_shop, :wholesale_price, :price, :ecotax, :weight, :unit_price_impact, :default_on, :minimal_quantity, :available_date);";
            $stmt = $this->pdo->prepare($sql);
            return $res = $stmt->execute($data);
        }

        public function get_product_attribute($id_product_attribute) {
            $row = array();
            $sql = "select `id_product_attribute`, `id_product`, `attribute_config`, `reference`, `supplier_reference`, `location`, `ean13`, `isbn`, `upc`, `wholesale_price`, `price`, `ecotax`, `quantity`, `weight`, `unit_price_impact`, `default_on`, `minimal_quantity`, `available_date` from `s_product_attribute` where `id_product_attribute` = ? ;";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array("$id_product_attribute"));
            $row = $stmt->fetch();
            if(empty($row)){
               $row = array();
            }
            return $row;
        }

        public function get_product_attribute_combination($id_attribute, $id_product_attribute) {
            $row = array();
            $sql = "select `id_attribute`, `id_product_attribute` from `s_product_attribute_combination` where `id_attribute` = ? and `id_product_attribute` = ? ;";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array("$id_attribute", "$id_product_attribute"));
            $row = $stmt->fetch();
            if(empty($row)){
               $row = array();
            }
            return $row;
        }

        public function set_product_attribute($data) {
            //
            $sql = "update `s_product_attribute` set `id_product` = :id_product, `attribute_config` = :attribute_config, `reference` = :reference, `supplier_reference` = :supplier_reference, `location` = :location, `ean13` = :ean13, `isbn` = :isbn, `upc` = :upc, `wholesale_price` = :wholesale_price, `price` = :price, `ecotax` = :ecotax, `quantity` = :quantity, `weight` = :weight, `unit_price_impact` = :unit_price_impact, `default_on` = :default_on, `minimal_quantity` = :minimal_quantity, `available_date` = :available_date where `id_product_attribute` = :id_product_attribute ;";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($data);
        }

        public function set_product_attribute_combination($data) {
            //
            $sql = "update `s_product_attribute_combination` set `id_attribute` = :id_attribute, `id_product_attribute` = :id_product_attribute where `id_attribute` = :id_attribute and `id_product_attribute` = :id_product_attribute;";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($data);
        }

        //检查 product attribute 是否已存在
        public function check_product_attribute_is_exist($id_product, $attribute_config){
            $sql = "select `id_product_attribute` from `s_product_attribute` where `id_product` = ?  and `attribute_config` = ?;";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array("$id_product", "$attribute_config"));
            $c_id_product_attribute = $stmt->fetchColumn();
            if($c_id_product_attribute){
                return $c_id_product_attribute;
            }else{
                return '';
            }
        }

        public function get_product_attribute_combination_list($id_product_attribute)
        {
            $sql = "select `id_attribute`, `id_product_attribute` from `s_product_attribute_combination` where `id_product_attribute` = ? ;";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_product_attribute', $id_product_attribute, PDO::PARAM_INT);
            $stmt->execute();
            $jobs = $stmt->fetchAll();
            $job = array();
            foreach($jobs as $k=>$j){
                $job[$k] = $j;
            }
            return $job;
        }
/*
*/
}
