<?php

class  Bll_Groupon_Info {
	    private $grouponInfoDao;

	    public function __construct() {
		        $this->grouponInfoDao = new Dao_Groupon_Info();
	    }

        public function create_groupon($data) {
                if(empty($data)) return array();
                return $this->grouponInfoDao->create_groupon($data);
        }

        public function set_groupon($data) {
                if(empty($data)) return array();
                return $this->grouponInfoDao->set_groupon($data);
        }

        public function get_groupon($id) {
                if(empty($id)) return array();
                $groupon = $this->grouponInfoDao->get_groupon($id);
                if(isset($groupon['kkid']) && !empty($groupon['kkid'])){
                   $groupon = self::get_groupon_by_kkid($groupon['kkid']);
                } 
                return $groupon;
        }

        public function get_groupon_by_kkid($kkid) {
                if(empty($kkid)) return array();
                $groupon = $this->grouponInfoDao->get_groupon_by_kkid($kkid); 
                if(isset($groupon['p_kkid']) && !empty($groupon['p_kkid'])){
                   $bll_product = new Bll_Product_Info();
                   $groupon['product_info'] = $bll_product->get_product($groupon['p_kkid']);
                }
                if(isset($groupon['created_by']) && !empty($groupon['created_by'])){
                   $bll_user = new Bll_User_UserInfoUC();
                   $base_info = $bll_user->get_user_by_kkid($groupon['created_by']);
                   if(isset($base_info['picture']) && strlen($base_info['picture']) == 32){
                       $base_info['picture_url'] = IMG_CDN_USER . strtolower($base_info['picture']) . "/" . "headpic.jpg";
                   }
                   $groupon['admin_user'] = array(
                                             'kkid'=>$base_info['kkid'],
                                             'name'=>$base_info['name'],
                                             'picture_url'=>$base_info['picture_url'], 
                                             'user_photo'=>$base_info['user_photo'], 
                                          );
                }
                return $groupon;
        }
        public function get_groupon_by_pkkid($kkid) {
                if(empty($kkid)) return array();
                $groupon = $this->grouponInfoDao->get_groupon_by_pkkid($kkid); 
                return $groupon;
        }

		public function get_groupon_by_g_kkid($g_kkid) {
				if(empty($g_kkid)) return array();
				$groupon = $this->grouponInfoDao->get_groupon_by_g_kkid($g_kkid);
				return $groupon;
		}
    /**
     * @param $id 团购id
     * 设置商品为限时抢购商品
     */
        public function set_limit_time_shop($id){
            if(!is_numeric($id)) return array();
            return $this->grouponInfoDao->set_limit_time_shop($id);
        }
        //设置限时团购的团购是否有效
		public function get_groupon_can_limit_time($id)
		{
		  if(!is_numeric($id)) return array();
		  return $this->grouponInfoDao->get_groupon_can_limit_time($id);
		}

		//获取限时拼团的商品
        public function get_product_groupon_limit_time()
        {
            $groupon_limit        = $this->grouponInfoDao->limit_time_shop();
            $bll_product          = new Bll_Product_Info();//实例化商品类
			if($groupon_limit){
            	$product_info         = $bll_product->get_product($groupon_limit['p_kkid']);//获取拼团商品息
			    $bll_groupon_customer = new Bll_Groupon_Customer(); 
			    //$customer             = $bll_groupon_customer->get_customer_by_gkkid($groupon_limit['kkid']);
                $groupon_limit['product'] = $product_info;
			}
			//$groupon_limit['customer']     = $customer;
            return $groupon_limit;
        }

        public function get_product_groupon_list($limit, $offset) {
                if(!is_numeric($limit) || !is_numeric($offset)) return array();
                $groupon_list = $this->grouponInfoDao->get_product_groupon_list($limit, $offset);
                $bll_product = new Bll_Product_Info();
                $bll_user = new Bll_User_UserInfoUC();
                foreach($groupon_list as $k=>$j){
                    /*  */
                    if(isset($j['p_kkid']) && !empty($j['p_kkid'])){
                       $j['product_info'] = $bll_product->get_product($j['p_kkid']);
                    }
                    if(isset($j['created_by']) && !empty($j['created_by'])){
                       $base_info = $bll_user->get_user_by_kkid($j['created_by']);
                       if(isset($base_info['picture']) && strlen($base_info['picture']) == 32){
                           $base_info['picture_url'] = IMG_CDN_USER . strtolower($base_info['picture']) . "/" . "headpic.jpg";
                       }
                       $j['admin_user'] = array(
                                                 'kkid'=>$base_info['kkid'],
                                                 'name'=>$base_info['name'],
                                                 'picture_url'=>$base_info['picture_url'],
                                                 'user_photo'=>$base_info['user_photo'],
                                              );
                    }
                    /*  */
                    $groupon_list[$k] = $j;
                }
                return $groupon_list;
        }
        public function get_product_groupon_adminlist($p_kkid, $limit, $offset) {
                if(!is_numeric($limit) || !is_numeric($offset)) return array();
                $groupon_list = $this->grouponInfoDao->get_product_groupon_adminlist($p_kkid, $limit, $offset);
                $bll_product = new Bll_Product_Info();
                $bll_user = new Bll_User_UserInfoUC();
                foreach($groupon_list as $k=>$j){
                    /*  */
                    if(isset($j['p_kkid']) && !empty($j['p_kkid'])){
                       $j['product_info'] = $bll_product->get_product($j['p_kkid']);
                    }
                    if(isset($j['created_by']) && !empty($j['created_by'])){
                       $base_info = $bll_user->get_user_by_kkid($j['created_by']);
                       if(isset($base_info['picture']) && strlen($base_info['picture']) == 32){
                           $base_info['picture_url'] = IMG_CDN_USER . strtolower($base_info['picture']) . "/" . "headpic.jpg";
                       }
                       $j['admin_user'] = array(
                                                 'kkid'=>$base_info['kkid'],
                                                 'name'=>$base_info['name'],
                                                 'picture_url'=>$base_info['picture_url'],
                                                 'user_photo'=>$base_info['user_photo'],
                                              );
                    }
                    /*  */
                    $groupon_list[$k] = $j;
                }
                return $groupon_list;
        }

        public function get_product_groupon_count() {
                return $this->grouponInfoDao->get_product_groupon_count();
        }
        public function get_product_groupon_adminlist_count($p_kkid) {
                return $this->grouponInfoDao->get_product_groupon_adminlist_count($p_kkid);
        }

		//随机设置一个限时拼团商品
		public function set_random_groupon_info()
		{
		    $groupon_info  = $this->get_product_groupon_list(1,1);
			$id_group      = $groupon_info[0]['id_group'];
			$re =  $this->set_limit_time_shop($id_group);
			return $re;
		}
}
