<?php

class  Bll_Video_Info{
	private $Dao;

	public function __construct() {
		$this->Dao = new Dao_Video_Info();
    }

    public function get_random_video_list($limit){
        return $this->Dao->get_random_video_list($limit);
    }

    public function get_news_list($chaid, $page_start, $page_size){
        return $this->Dao->get_news_list($chaid, $page_start, $page_size);
    }

    public function get_news_imgs($uid, $limit){
        return $this->Dao->get_news_imgs($uid, $limit);
    }

    public function get_video_detail($nid){
        return $this->Dao->get_video_detail($nid);
    }

    public function get_video_comment($nid){
        return $this->Dao->get_video_comment($nid);
    }
}
