<?php

class Bll_Salerank_Info {

	private $dao;

    public function __construct() {
        $this->dao = new Dao_Salerank_Info();
    }

	public function add($data) {
		if (!$data) return array();
		$res = $this->dao->add($data);
		return $res;
	}
	
	public function del($data) {
		if (!$data) return array();
		return $this->dao->del($data);
	}

	public function view($id) {
		return $this->dao->view($id);
	}

	public function get_list_admin() {
		$res = $this->dao->get_list_admin();
		if(!$res){
			return array();
		}
		foreach($res as $k=>$v){
			$data['id_product'] = $v['id_product'];
			$v['p_info'] = self::get_product_detail($data);
			unset($v['id_product']);
			$res[$k] = $v;
		}
		return $res;
	}

	public function check_repeat($data){
		if(!$data) return array();
		return $this->dao->check_repeat($data);
	}

	public function get_product_detail($data){
		if(!$data) return array();
		return $this->dao->get_product_detail($data);
	}

	public function get_product_list(){
		return $this->dao->get_product_list();
	}
}
