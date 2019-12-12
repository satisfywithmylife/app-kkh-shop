<?php

class  Bll_Apost_Info {

	private $apostInfoDao;

	public function __construct() {
		$this->apostInfoDao = new Dao_Apost_Info();
	}

	public function add($data){
		if (!$data) return array();
		return $this->apostInfoDao->add($data);
	}
	
	public function edit($id){
		if (!$id) return array();
		return $this->apostInfoDao->edit($id);
	}
	
	public function del($id) {
		if (!$id) return array();
		return $this->apostInfoDao->del($id);
	}

	public function view($id) {
		if (!$id) return array();
		return $this->apostInfoDao->view($id);
	}

	public function get_banner_admin($id) {
		if (!$id) return array();
		return $this->apostInfoDao->get_banner_admin($id);
	}

	public function banner_list(){
		return $this->apostInfoDao->banner_list();
	}
}
