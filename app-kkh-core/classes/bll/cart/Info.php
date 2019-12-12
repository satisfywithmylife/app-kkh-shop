<?php

class  Bll_Cart_Info {
	private $cartInfoDao;

	public function __construct() {
		$this->cartInfoDao = new Dao_Cart_Info();
	}

        public function create_cart($data) {
                if(empty($data)) return array();
                return $this->cartInfoDao->create_cart($data);
        }

		public function get_attribute_config($id_product){
				if(!$id_product) return array();
				return $this->cartInfoDao->get_attribute_config($id_product);
		}

        public function set_cart($data) {
                if(empty($data)) return array();
                return $this->cartInfoDao->set_cart($data);
        }

		public function clear_all_cart_selected_product($id_cart){
				if(!$id_cart) return array();
				return $this->cartInfoDao->clear_all_cart_selected_product($id_cart);
		}

        public function clear_cart($id_cart) {
                if(empty($id_cart)) return array();
                return $this->cartInfoDao->clear_cart($id_cart);
        }

        public function get_cart($id_cart) {
                if(empty($id_cart)) return array();
                return $this->cartInfoDao->get_cart($id_cart);
        }

        public function get_cart_by_customer($id_customer) {
                if(empty($id_customer)) return array();
                return $this->cartInfoDao->get_cart_by_customer($id_customer);
        }

		public function get_customer_cart_by_id_customer($id_customer) {
				if(empty($id_customer)) return array();
				return $this->cartInfoDao->get_customer_cart_by_id_customer($id_customer);
		}
        public function get_cart_by_guest($id_guest) {
                if(empty($id_guest)) return array();
                return $this->cartInfoDao->get_cart_by_guest($id_guest);
        }

        public function get_cart_product($id_cart, $id_product, $id_product_attribute = 0) {
                if(empty($id_cart) || empty($id_product)) return array();
                return $this->cartInfoDao->get_cart_product($id_cart, $id_product, $id_product_attribute);
        }

        public function add_cart_product($data) {
                if(empty($data)) return array();
                return $this->cartInfoDao->add_cart_product($data);
        }

        public function set_cart_product($id_cart, $id_product, $data) {
                if(empty($data)) return array();
                return $this->cartInfoDao->set_cart_product($id_cart, $id_product, $data);
        }

        public function del_cart_product($id_cart, $id_product, $id_product_attribute = 0) {
                if(empty($id_cart) || empty($id_product)) return array();
                return $this->cartInfoDao->del_cart_product($id_cart, $id_product, $id_product_attribute);
        }

        public function merge_cart_product($id_cart1, $id_cart2) {
                if(empty($id_cart1) || empty($id_cart2)) return array();
                return $this->cartInfoDao->merge_cart_product($id_cart1, $id_cart2);
        }

        public function del_cart_all_product($id_cart) {
                if(empty($id_cart)) return array();
                return $this->cartInfoDao->del_cart_all_product($id_cart);
        }
}
