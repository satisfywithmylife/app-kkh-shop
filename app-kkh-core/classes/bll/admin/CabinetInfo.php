<?php

class  Bll_Admin_CabinetInfo {

	private $dao;

	public function __construct() {
		$this->dao = new Dao_Admin_CabinetInfo();
	}
	
	public function get_device_by_hospital_id($hospital_id){
		if(!$hospital_id) return array();
		return $this->dao->get_device_by_hospital_id($hospital_id);
	}

	public function get_unlocked_num($cd_key, $id_product){
		if(!$cd_key || !$id_product) return 0;
		return $this->dao->get_unlocked_num($cd_key, $id_product);
	}

	public function get_productlist_by_cd_key($cd_key, $page_start, $page_size){
		if(!$cd_key) return array();
		return $this->dao->get_productlist_by_cd_key($cd_key, $page_start, $page_size);
	}

	public function get_product_count($cd_key){
		if(!$cd_key) return 0;
		return $this->dao->get_product_count($cd_key);
	}

	public function get_product($cd_key, $id_product){
		if(!$cd_key || !$id_product) return array();
		return $this->dao->get_product($cd_key, $id_product);
	}

	public function get_cabinet($cd_key){
		if(!$cd_key) return array();
		return $this->dao->get_cabinet($cd_key);
	}

	public function set_cabinet_default_id_customer($id_customer, $cd_key){
		if(!$id_customer) return array();
		return $this->dao->set_cabinet_default_id_customer($id_customer, $cd_key);
	}

}
