<?php

class Bll_Search_Info {

	private $dao;

    public function __construct() {
        $this->dao = new Dao_Search_Info();
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
		return $this->dao->get_list_admin();
	}
}
