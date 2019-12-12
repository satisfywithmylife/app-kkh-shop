<?php
apf_require_class('APF_Controller');
/**
 * Class Groupon_GetLimitTimeController
 */
class Groupon_GetLimitTimeController extends APF_Controller
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
		//查询限时拼团的数据
		$bll_groupon    = new Bll_Groupon_Info();
		$bll_customer   = new Bll_Groupon_Customer();
		$groupon_limit  = $bll_groupon->get_product_groupon_limit_time();//获取限时拼团的商品
		//若不存在限时拼团，随机设置一个
		if(!$groupon_limit)
		{
		    $bll_groupon->set_random_groupon_info();
			$groupon_limit  = $bll_groupon->get_product_groupon_limit_time();
		}
		if($groupon_limit)
		{  
		   $data['id_group']   = $groupon_limit['id_group'];
		   $data['id_product'] = $groupon_limit['id_product'];
		   $data['name']       = $groupon_limit['product']['name'];
		   $customer_count     = $bll_customer->get_limit_time_customer_count($groupon_limit['kkid']);
		   $data['cus_num']    = $customer_count['num'];
		   self::cache_data($groupon_limit);
		   echo Util_Json::json_str(200,'success',$data);
		   return false;
		}
		else
		{
		   echo Util_Json::json_str(200,'Data is not exists',[]);
		}


	}
    //缓存数据
	public function cache_data($data)
	{
	   $redis = Util_Redis::redis_server();
	   $redis->set("product:groupon_limit_time",json_encode($data,JSON_NUMERIC_CHECK));
	}
}
