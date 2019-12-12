<?php

class  Bll_Attribute_Info {
	private $attributeInfoDao;

	public function __construct() {
            $this->attributeInfoDao = new Dao_Attribute_Info();
	}

        public function create_product_attribute($data) {
            if(empty($data)) return array();
            return $this->attributeInfoDao->create_product_attribute($data);
        }

        public function create_product_attribute_combination($data) {
            if(empty($data)) return array();
            return $this->attributeInfoDao->create_product_attribute_combination($data);
        }

        public function get_product_attribute($id_product_attribute) {
            if(empty($id_product_attribute)) return array();
            return $this->attributeInfoDao->get_product_attribute($id_product_attribute);
        }

        public function get_product_attribute_combination($id_attribute, $id_product_attribute) {
            if(empty($id_attribute) || empty($id_product_attribute)) return array();
            return $this->attributeInfoDao->get_product_attribute_combination($id_attribute, $id_product_attribute);
        }

        public function set_product_attribute($data) {
            if(empty($data)) return array();
            return $this->attributeInfoDao->set_product_attribute($data);
        }

        public function set_product_attribute_combination($data) {
            if(empty($data)) return array();
            return $this->attributeInfoDao->set_product_attribute_combination($data);
        }

        public function check_product_attribute_is_exist($id_product, $attribute_config) {
            if(empty($id_product) || empty($attribute_config)) return array();
            return $this->attributeInfoDao->check_product_attribute_is_exist($id_product, $attribute_config);
        }

        public function get_product_attribute_combination_list($id_product_attribute) {
            if(empty($id_product_attribute)) return array();
            return $this->attributeInfoDao->get_product_attribute_combination_list($id_product_attribute);
        }
        public function create_product_attribute_shop($data) {
            if(empty($data)) return array();
            return $this->attributeInfoDao->create_product_attribute_shop($data);
        }

        public function get_id_product_attribute($id_product, $p_attribute) {
            if(!empty($p_attribute)){
                $id_product_attribute = self::check_product_attribute_is_exist($id_product, $p_attribute);
                if(empty($id_product_attribute)){
                    $attribute_data = array(
                        'id_product_attribute' => 0,
                        'id_product' => $id_product,
                        'attribute_config' => $p_attribute,
                        'reference' => '',
                        'supplier_reference' => '',
                        'location' => '',
                        'ean13' => '',
                        'isbn' => '',
                        'upc' => '',
                        'wholesale_price' => 0.000000,
                        'price' => 0.000000,
                        'ecotax' => 0.000000,
                        'quantity' => 0,
                        'weight' => 0.000000,
                        'unit_price_impact' => 0.000000,
                        'default_on' => NULL,
                        'minimal_quantity' => 1,
                        'available_date' => '0000-00-00',
                    );
                    $id_product_attribute = self::create_product_attribute($attribute_data);
                    $attribute_shop_data = array(
                        'id_product' => $id_product,
                        'id_product_attribute' => $id_product_attribute,
                        'id_shop' => 1,
                        'wholesale_price' => 0.000000,
                        'price' => 0.000000,
                        'ecotax' => 0.000000,
                        'weight' => 0.000000,
                        'unit_price_impact' => 0.000000,
                        'default_on' => NULL,
                        'minimal_quantity' => 1,
                        'available_date' => '0000-00-00',
                    );
                    self::create_product_attribute_shop($attribute_shop_data);
                    $p_attribute_hash = json_decode($p_attribute, true);
                    foreach($p_attribute_hash as $k=>$j){
                        $job[$k] = $j;
                        $attribute_combination_data = array(
                            'id_attribute' => $j,
                            'id_product_attribute' => $id_product_attribute
                        );
                        if((int)$j > 0 && $id_product_attribute > 0) self::create_product_attribute_combination($attribute_combination_data);
                    }
                }
            }
            else{
                $id_product_attribute = "0";  // int(10) unsigned  // id_product_attribute 商品型号/规格
            }
            return $id_product_attribute;
        }
}
