<?php
//运营商品精选  口碑   新品推荐 添加
apf_require_class('APF_Controller');

class Shop_ShopAddController extends APF_Controller
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
       $id_product = isset($params['id_product']) && !empty($params['id_product']) ? $params['id_product'] :'';  //id  运营商品id
	   $position   = isset($params['position']) && !empty($params['position']) ? $params['position'] :'';  //position  运营商品位置
	   $shop_type  = isset($params['shop_type']) && !empty($params['shop_type']) ? $params['shop_type'] :'';  //shop_type  商品类型 三个参数  1=>精选，2=>口碑， 3=> 新品推荐
	   $action     = isset($params['action']) && !empty($params['action']) ? $params['action'] :'';  //action  运营商品位置
	   //判断参数是否正确
	   if(!$id_product || !$position || !$shop_type)
	   {       
	      echo Util_Json::json_str(400,'缺少必要参数',[]);
		  return false;
	   }       

	   if($action != "add")
	   {       
		 echo Util_Json::json_str(400,'请传入正确的action',[]);
	     return false;
	   }
	   //判断添加商品类型  
	   if($shop_type == 1)
	   { 
		    $is_choiceness    = 1;
			$is_public_praise = 0;
			$is_new_recommend = 0;
		}
		elseif($shop_type == 2)
		{ 
			$is_choiceness    = 0;
			$is_public_praise = 1;
			$is_new_recommend = 0;
		}
		elseif($shop_type == 3)
		{ 
			$is_choiceness    = 0;
			$is_public_praise = 0;
			$is_new_recommend = 1;
		}
		else
		{
			echo Util_Json::json_str(400,'商品类型错误',[]);
			return false;
		}
		//执行添加操作
		$data['id_product']       = $id_product;
		$data['position']         = $position;
		$data['is_choiceness']    = $is_choiceness;
		$data['is_public_praise'] = $is_public_praise;
		$data['is_new_recommend'] = $is_new_recommend;
		$bll_product = new Bll_Product_Info();
		$res = $bll_product->add_operation_shop($data);
		if($res){
		    self::del_cache($shop_type);
		  	echo Util_Json::json_str(200,'运营商品添加成功',[]);
		  	return false;
		}else{
			echo Util_Json::json_str(200,'运营商品添加失败',[]);
			return false;
		}

	}
	private function del_cache($shop_type)
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
			$redis->del("product:recommend_".$name);
		}
	}
}
