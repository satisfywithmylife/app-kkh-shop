<?php
apf_require_class("APF_DB_Factory");

class Dao_Video_Info {

	private $pdo;

	public function __construct() {
		$this->pdo = APF_DB_Factory::get_instance()->get_pdo("news_master");
	}

    public function get_random_video_list($limit){
        $sql = "select * from video_collection order by rand() limit $limit;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $res = $stmt->fetchAll();
        if(!$res){
            $res = [];
        }
        return $res;
    }
    
    public function get_channel_list(){
        $sql = "select * from news_col_channel where status = 1 order by porder desc;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $res = $stmt->fetchAll();
        if(!$res){
            $res = [];
        }
        return $res;
    }

    public function get_news_list($chaid, $page_start, $page_size){
        $sql = "select nid, chaid, title, imgurl, from_unixtime(created_at) as created_at, '大饼干' as author, (select count(*) from news_col_img where nid = a.nid limit 1) as img_num from news_collection a where a.chaid = :chaid order by nid desc LIMIT :limit OFFSET :offset;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->BindParam(':chaid', $chaid, PDO::PARAM_INT);
        $stmt->BindParam(':offset', $page_start, PDO::PARAM_INT);
        $stmt->BindParam(':limit', $page_size, PDO::PARAM_INT);
        $stmt->execute();
        $res = $stmt->fetchAll();
        if(!$res){
            $res = [];
        }
        return $res;
    }

    public function get_news_imgs($uid, $limit){
        $sql = "select * from news_col_img where nid = :nid LIMIT :limit;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->BindParam(':nid', $uid, PDO::PARAM_INT);
        $stmt->BindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $res = $stmt->fetchAll();
        if(!$res){
            $res = [];
        }

        return $res;
    }

    public function get_video_detail($nid){
        $sql = "select * from video_collection a where a.vid = ? limit 1;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array($nid));
        $res = $stmt->fetch();
        if(!$res){
            $res = [];
        }
        return $res;
    }

    public function get_news_comment($nid){
        $sql = "select * from news_comment where nid = ? and status = 1 order by id desc;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array($nid));
        $res = $stmt->fetchAll();
        if(!$res){
            $res = [];        
        }
        return $res;
    }
}

