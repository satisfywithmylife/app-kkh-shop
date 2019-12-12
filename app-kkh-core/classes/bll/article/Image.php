<?php

class  Bll_Article_Image {

	private $articleImageDao;

	public function __construct() {
		$this->articleImageDao = new Dao_Article_Image();
	}

	public function get_show_image($aid, $show_type){
		if (!$show_type || !$aid) return array();
		return $this->articleImageDao->get_show_image($aid, $show_type);
	}

	public function get_share_image($aid){
		if(!$aid) return '';
		return $this->articleImageDao->get_share_image($aid);
	}

	public function add_image($data ,$aid ,$type){
		if (empty($data) || !$aid) return array();
		return $this->articleImageDao->add_image($data, $aid, $type);
	}
		
	public function edit_article($data){
		if(empty($data)) return array();
		return $this->articleInfoDao->edit_article($data);
	}
		
	public function del_article($data){
		if(empty($data)) return array();
		return $this->articleInfoDao->del_article($data);
	}

	public function view_article($data){
		if(empty($data)) return array();
		return $this->articleInfoDao->view_article($data);
	}

	public function count_article($data){
		if(empty($data)) return 0;
		return $this->articleInfoDao->count_article($data);
	}
}
