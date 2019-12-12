<?php

class  Bll_Article_Test {

	private $articleTestDao;

	public function __construct() {
		$this->articleTestDao = new Dao_Article_Test();
	}

	public function test($a){
		return $this->articleTestDao->test($a);
	}

	public function set_user_action_log($data){
		$bll_user = new Bll_User_UserInfoUC(); //1-viewed, 2-shared
		$check  = $bll_user->verify_user_access_token($data['kkid'], $data['token']);
		if (!$check) {
			$data['kkid'] = $data['token'] = '';
		}
		$this->articleInfoDao->add_log($data);
	}
		
	public function get_article_by_p_kkid($p_kkid){
		if(!$p_kkid) return array();
		if (strlen($p_kkid) == 32) {    //p_kkid or id_product
			$bll_product_info = new Bll_Product_Info();
			$p_kkid = $bll_product_info->get_id_product_by_p_kkid($p_kkid);
		}
		//Logger::info(__FILE__, __CLASS__, __LINE__, $p_kkid);
		$res = $this->articleInfoDao->get_article_by_p_kkid($p_kkid);
		$list = [];
		//Logger::info(__FILE__, __CLASS__, __LINE__, var_export($res, true));
		foreach($res as $k=>$v) {
			$article_info = self::get_article_by_aid($v['aid']);
			//Logger::info(__FILE__, __CLASS__, __LINE__, var_export($v, true));
			if(!$article_info){
				continue;
			}
			$article_info['show_imgs'] = self::get_show_image($article_info['aid'], $article_info['show_type']);
			$list[$k] = $article_info;				
		}
		return $list;
	}

	public function get_show_image($aid, $show_type){
		$bll_article_image = new Bll_Article_Image();
		return $bll_article_image->get_show_image($aid, $show_type);
	}

	public function get_article_by_aid($aid){
		return $this->articleInfoDao->get_article_by_aid($aid);
	}

	public function get_article_by_keyword_admin($kwd, $data){
		if(empty($data)) return array();
		$res =  $this->articleInfoDao->get_article_by_keyword_admin($kwd, $data);
		foreach ($res as $k=>$v){
			$id_product_arr = self::get_extend_info_by_aid($v['aid']);
			if(empty($id_product_arr)) {
				$v['product_list'] = [];
			}else{
				//Logger::info(__FILE__, __CLASS__, __LINE__, $v['aid']);
				//Logger::info(__FILE__, __CLASS__, __LINE__, var_export($id_product_arr, true));
				$v['product_list'] = self::get_product_info_by_id_product($id_product_arr);
			}
			$v['created_by'] = self::get_admin_username_by_uid($v['created_by']);
			$v['updated_by'] = self::get_admin_username_by_uid($v['updated_by']);
			$v['created_at'] = date("Y-m-d H:i:s", $v['created_at']);
			$v['updated_at'] = date("Y-m-d H:i:s", $v['updated_at']);
			$res[$k] = $v;
		}
		return $res;
	}

	public function get_article_by_keyword_admin_count($kwd, $data){
		$res =  count($this->articleInfoDao->get_article_by_keyword_admin($kwd, $data));
		return $res;
	}
	
	public function get_admin_username_by_uid($uid){
		$bll_admin_info = new Bll_Admin_Info();
		$res = $bll_admin_info->get_admin_info_by_uid($uid);
		if(empty($res)) {
			return;
		}
		return $res['username'];
	}

	public function get_product_info_by_id_product($id_product_arr){
		Logger::info(__FILE__, __CLASS__, __LINE__, var_export($id_product_arr, true));
		$bll_product_info = new Bll_Product_Info();
		$product_list = [];
		foreach($id_product_arr as $k=>$v) {
			Logger::info(__FILE__, __CLASS__, __LINE__, var_export($v, true));
			if(empty($v)) {
				continue;
			}
			$p_info = $bll_product_info->get_product_by_id_product($v['id_product']);
			$product_list[] = $p_info; 
		}
		return $product_list;
	}

	public function get_extend_info_by_aid($aid) {
		if(empty($aid)) return array();
		$bll_article_belong = new Bll_Article_Belong();
		$res = $bll_article_belong->get_belong_admin($aid);
		Logger::info(__FILE__, __CLASS__, __LINE__, var_export($res, true));
		return $res;
	}

	public function add_article($data){
		if (empty($data)) return array();
		return $this->articleInfoDao->add_article($data);
	}
		
	public function edit_article($data){
		if(empty($data)) return array();
		$bll_article_belong = new Bll_Article_Belong();
		$belong_arr = explode("||", $data['belong']);
		$bll_article_belong->add_article_to_product($belong_arr, $data['aid']);

		return $this->articleInfoDao->edit_article($data);
	}
		
	public function del_article($data){
		if(empty($data)) return array();
		#删除文章(ruan)，连同所有该文章的关联商品记录一起做软删除
		$bll_article_belong = new Bll_Article_Belong();
		$bll_article_belong->update($data['aid']);
		return $this->articleInfoDao->del_article($data);
	}

	public function view_article($data){
		if(empty($data)) return array();
		return $this->articleInfoDao->view_article($data);
	}

	public function get_article_admin($aid){
		if(empty($aid)) return array();
		$res = $this->articleInfoDao->get_article_admin($aid);
		if (!$res) {
			return array();
		}
		
		$product_id_arr = [];
		$bll_article_belong = new Bll_Article_Belong();
		$product_id_arr = $bll_article_belong->get_belong_admin($aid);
		foreach ($product_id_arr as $k=>$v) {
			$m[] = $v['id_product'];
		}
		Logger::info(__FILE__, __CLASS__, __LINE__, var_export($product_id_arr, true));
		$res['belong'] = join('||', $m);
		return $res;
	}

	public function count_article($data){
		if(empty($data)) return 0;
		return $this->articleInfoDao->count_article($data);
	}
}
