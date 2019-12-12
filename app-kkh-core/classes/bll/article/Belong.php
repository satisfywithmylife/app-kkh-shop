<?php

class  Bll_Article_Belong {

	private $articleBelongDao;

	public function __construct() {
		$this->articleBelongDao = new Dao_Article_Belong();
	}

	public function add_article_to_product($data, $aid){
		if (empty($data) || empty($aid)) return array();
		return $this->articleBelongDao->add_article_to_product($data, $aid);
	}

	public function get_belong_admin($aid){
		return $this->articleBelongDao->get_belong_admin($aid);
	}
		
	public function update($aid){
		return $this->articleBelongDao->update($aid);
	}		
}
