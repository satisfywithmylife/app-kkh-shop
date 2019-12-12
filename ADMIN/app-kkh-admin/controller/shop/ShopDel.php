<?php
apf_require_class('APF_Controller');
class Shop_ShopDelController extends APF_Controller
{
    public function handle_request()
	{
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
		//获取参数
		$id        = isset($params['id']) && !empty($params['id'])?$params['id'] :'';//获取要删除的id
		$action    = isset($params['action']) && !empty($params['action']) ? $params['action'] :'';  //action  行为
		$shop_type = isset($params['shop_type']) && !empty($params['shop_type']) ? $params['shop_type'] :'';  //shop_type
       //判断类型是否正确
	   if(!$id || !$shop_type)
		{
		  echo Util_Json::json_str(400,'参数错误',[]);
		  return false;
		}
		if($action != "del")
		{
		   echo Util_Json::json_str(400,'请传入正确的action',[]);
		   return false;
		}

		//进行删除操作
		$bll_product = new Bll_Product_Info();
		$res = $bll_product->del_operation_shop($id);
		if($res)
		{
		  self::del_cache($shop_type);
          echo Util_Json::json_str(200,'运营商品删除成功',[]);
		  return false;
		}else{
		  echo Util_Json::json_str(200,'运营商品删除失败',[]);
		  return false;
		}


	}

	//删除缓存
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
