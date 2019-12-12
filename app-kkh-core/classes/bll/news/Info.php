<?php

class  Bll_News_Info{
	private $Dao;

	public function __construct() {
		$this->Dao = new Dao_News_Info();
    }

    public function get_channel_list(){
        return $this->Dao->get_channel_list();
    }

    public function get_news_list($chaid, $page_start, $page_size){
        return $this->Dao->get_news_list($chaid, $page_start, $page_size);
    }

    public function get_news_imgs($uid, $limit){
        return $this->Dao->get_news_imgs($uid, $limit);
    }

    public function get_news_detail($nid){
        return $this->Dao->get_news_detail($nid);
    }

    public function get_news_comment($nid){
        return $this->Dao->get_news_comment($nid);
    }

    public function get_travel_list($limit){
        return $this->Dao->get_travel_list($limit);
    }
}
