<?php
apf_require_class('APF_Controller');
class Shop_ShopListController extends APF_Controller
{
  public function handle_request()
      {
	    // TODO: Implement handle_request() method.
		header("Access-Control-Allow-Origin:*");
		header("Access-Control-Allow-Methods:GET, POST, OPTIONS, DELETE");
		header("Access-Control-Allow-Headers:SIG,DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type, Accept-Language, Origin, Accept-Encoding");
		header("Content-type: application/json; charset=utf-8");
		$req = APF::get_instance()->get_request();
		$params = $req->get_parameters();
		$s_re = Util_AdminSecurity::Security($params);
		if(!$s_re){
		   echo Util_Json::json_str(400,'access denied',[]);
	 	   return false;
		}
		$action    = isset($params['action']) && !empty($params['action']) ? $params['action'] :'';
		$shop_type = isset($params['shop_type']) && !empty($params['shop_type']) ? $params['shop_type'] :''    ; 
		if($action != "view"){
			echo Util_Json::json_str(400,'缺少必要参数action或参数不正确',[]);
		    return false;
		}
		$bll_product = new Bll_Product_Info();
		// $product_list = $bll_product->get_shop_list();//获取商品列表
        $operation_shop = [];
		if($shop_type){
				
				$operation_list = $bll_product->get_operation_shop_list($shop_type);//获取口碑、精选、新品商品
				//$shop_list = [];
				//$operation_shop['choiceness'] = [];
				//$operation_shop['public_praise'] = [];
				//$operation_shop['new_recommend'] = [];
				//获取需要的商品列表字段
				//foreach($product_list as $k=>$v){
				//    $shop_list[$k]['name'] = $v['name'];
				//   $shop_list[$k]['id_product'] = $v['id_product'];
				//   $shop_list[$k]['kkid'] = $v['kkid'];
				//}
				//获取口碑、精选、新品商品所需字段
				foreach($operation_list as $k=>$v)
				{
				   $arr = [];
				   $arr['id']              = $v['id'];
				   $arr['name']            = $v['name'];
				   $arr['id_product']      = $v['id_product'];
				   $arr['kkid']            = $v['kkid'];
				   $arr['position']        = $v['position'];
				   $arr['images']     	   = $v['images'][0]['id_product_kkh_url'];
				   $arr['price']           = sprintf("%.1f",$v['price']);
				   $arr['wholesale_price'] = sprintf("%.1f",$v['wholesale_price']);
				   $arr['created_at']       = $v['created_at'];
				   array_push($operation_shop,$arr);

				}
				self::cache_data($operation_shop,$shop_type);
		}
        //整合数据
		//$data['shop_list']  = $shop_list;
		//$data['qpg_choiceness'] = $operation_shop['choiceness'];
		//$data['qpg_public_praise'] = $operation_shop['public_praise'];
		//$data['qpg_new_recommend '] = $operation_shop['new_recommend'];
		
		//if($shop_list)
	    //	{
		  echo Util_Json::json_str(200,'success',$operation_shop);
		  return false;
		//}else{
		//	echo Util_Json::json_str(400,'get shop list fail',[]);
		//	return false;
        //}
       }
       
	   public function cache_data($data,$shop_type)
	   {
	       $redis = Util_Redis::redis_server();
		   //判断是哪种商品
		   if($shop_type == 1)
		   {
		     $name = "choiceness";

		   }
		   elseif($shop_type == 2)
		   {
		     $name = "public_praise";
		   }
		   elseif($shop_type == 3)
		   {
		       $name = "new_recommend";
		   }
		   else
		   {
		      $name = "";
		   }
		   if($name)
		   {
		      $redis->set("product:recommend_".$name,json_encode($data));
		   }


	   }

}
