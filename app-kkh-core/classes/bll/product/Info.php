<?php

class  Bll_Product_Info {
	private $productInfoDao;

	public function __construct() {
		$this->productInfoDao = new Dao_Product_Info();
	}	
		public function id_product_kkh2id_product($id_product_kkh){
			if(!$id_product_kkh) return 0;
			return $this->productInfoDao->id_product_kkh2id_product($id_product_kkh);
		}
		
		public function id_product2p_kkid($id_product){
			if(!$id_product) return '';
			return $this->productInfoDao->id_product2p_kkid($id_product);
		}
			
		public function get_id_product_by_p_kkid($p_kkid){
			return $this->productInfoDao->get_id_product_by_p_kkid($p_kkid);
		}

        public function get_category_by_parentid($parentid) {
                if(empty($parentid)) return array();
                return $this->productInfoDao->get_category_by_parentid($parentid);
        }
		
		public function get_price_by_p_kkid($p_kkid) {
				if (empty($p_kkid)) return array();
				return $this->productInfoDao->get_price_by_p_kkid($p_kkid);
		}

        public function set_product_by_kkid($r_kkid, $u_kkid, $data) {
                if(empty($r_kkid) || empty($u_kkid)) return array();
                return $this->productInfoDao->set_product_by_kkid($r_kkid, $u_kkid, $data);
        }

        public function cancel_product_by_kkid($r_kkid, $u_kkid, $data) {
                if(empty($r_kkid) || empty($u_kkid)) return array();
                return $this->productInfoDao->cancel_product_by_kkid($r_kkid, $u_kkid, $data);
        }

        public function set_product_paystatus_by_kkid($r_kkid, $u_kkid, $status) {
                if(empty($r_kkid) || empty($u_kkid)) return array();
                return $this->productInfoDao->set_product_paystatus_by_kkid($r_kkid, $u_kkid, $status);
        }

        public function add_product($u_kkid, $data) {
                if(empty($u_kkid)) return array();
                return $this->productInfoDao->add_product($u_kkid, $data);
        }

        public function add_product_sk($u_kkid, $data) {
                if(empty($u_kkid)) return array();
                return $this->productInfoDao->add_product_sk($u_kkid, $data);
        }
       
	    public function madeinId2countryDetail($id){    
    	    $id = isset($id)&&is_numeric($id) ? $id : 1;
			$country_list = $this->productInfoDao->madeinId2countryDetail();
        	return $country_list[$id];
    	}   

 
        public function get_product($p_kkid, $g_kkid) {
                if(empty($p_kkid)) return array();
                $product = $this->productInfoDao->get_product($p_kkid);
                if(isset($product['kkid']) && !empty($product['kkid'])){
                //if(false){
                    $bll_groupon = new Bll_Groupon_Info();
					if (isset($g_kkid) && !empty($g_kkid)) {
						$groupon = $bll_groupon->get_groupon_by_g_kkid($g_kkid);
					}else{
                    	$groupon = $bll_groupon->get_groupon_by_pkkid($product['kkid']);
                    }
					// discount_amount
                    if(isset($groupon['kkid']) && !empty($groupon['kkid'])){
                        $product['groupon_info'] = array('g_kkid'=> $groupon['kkid'], 'discount_amount'=>$groupon['discount_amount']);
                    }
                }
                /*
                */
                return $product;
        }

        public function get_product_by_id($id_product) {
                if(empty($id_product)) return array();
                $product = $this->productInfoDao->get_product_by_id($id_product);
                return $product;
        }

		public function get_product_by_id_product($id_product) {
                if(empty($id_product)) return array();
                $product = $this->productInfoDao->get_product_by_id_product($id_product);
                return $product;
        }   


        public function get_product_list($limit, $offset)
        {
                if(!is_numeric($limit) || !is_numeric($offset)){
                  return array();
                }
                return $this->productInfoDao->get_product_list($limit, $offset);
        }
        //后台管理系统获取商品列表
        public function get_shop_list(){
            return $this->productInfoDao->get_shop_list();
        }
        //获取口碑、精选、新品商品
        public function get_operation_shop_list($type=''){
            return $this->productInfoDao->get_operation_shop_list($type);
        }
        //新增口碑、精选、新品商品
        public function add_operation_shop($data){
            if(empty($data)){
                return array();
            }else{
                return $this->productInfoDao->add_operation_shop($data);
            }
        }

        //删除口碑、精选、新品商品
        public function del_operation_shop($id){
            if(!is_numeric($id)){
                return array();
            }
            return $this->productInfoDao->del_operation_shop($id);
        }
        public function get_product_count()
        {
                return $this->productInfoDao->get_product_count();
        }

        public function get_product_search_list($p_kw, $limit, $offset)
        {
                if(!is_numeric($limit) || !is_numeric($offset)){
                  return array();
                }
                return $this->productInfoDao->get_product_search_list($p_kw, $limit, $offset);
        }

        public function get_product_search_count($p_kw)
        {
                return $this->productInfoDao->get_product_search_count($p_kw);
        }

        public function get_location($kkid)
        {
                if(empty($kkid)) {
                    return array();
                }
                return $this->productInfoDao->get_location($kkid);
        }

        public function get_productlist_by_idcategory($id_category, $limit, $offset)
        {
                if(!is_numeric($limit) || !is_numeric($offset)){
                    return array();
                }
                return $this->productInfoDao->get_productlist_by_idcategory($id_category, $limit, $offset);
        }

        public function get_cateprodlist_count($id_category) {
                return $this->productInfoDao->get_cateprodlist_count($id_category);        
        }

		/*
		*获取猜你喜欢的商品
		*@param $page_start  "开始查询位置"
		*@param $page_size  "查询条数"
		*/
		public function get_product_guess_you_like($page_start,$page_size)
		{
		    if(!is_numeric($page_start) || !is_numeric($page_size)){
				return array();
			}
			return $this->productInfoDao->get_product_guess_you_like($page_start,$page_size);
		}
        /**
		*获取猜你喜欢数量
		*/
		public function get_guess_you_like_count()
		{
		    return $this->productInfoDao->get_guess_you_like_count();
		}
		
		//获取某个商品所属分类
		public function get_product_category_list($id_product)
		{
		    if(!is_numeric($id_product))
			{
			    return [];
			}
			return $this->productInfoDao->get_product_category_list($id_product);
		}

		//获取相似商品
		public function get_similar_product_list($category_str,$page_start,$page_size)
		{
		     if(!$category_str || !is_numeric($page_size) || !is_numeric($page_start))
			 {
			     return array();
			 }
			 return $this->productInfoDao->get_similar_product_list($category_str,$page_start,$page_size);
		}
		//获取相似商品数量
		public function get_similar_product_list_count($category_str)
		{
		    if(!$category_str)
			{
			    return array();
			}
			return $this->productInfoDao->get_similar_product_list_count($category_str);
		}
}
