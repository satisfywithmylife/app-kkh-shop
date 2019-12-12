<?php

class  Bll_News_User{
	private $Dao;

	public function __construct() {
		$this->Dao = new Dao_News_User();
    }

    public function get_user_by_min_openid($openid){
        return $this->Dao->get_user_by_min_openid($openid);
    }


    public function add_user($data){
        return $this->Dao->add_user($data);
    }

    public function update_user($data){
        return $this->Dao->update_user($data);
    }
}
