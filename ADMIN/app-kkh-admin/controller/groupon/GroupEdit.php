<?php
apf_require_class('APF_Controller');
class Groupon_GroupEditController extends APF_Controller
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
	  $action   = isset($params['action']) && !empty($params['action']) ? $params['action'] :'';
	  $id_group = isset($params['id_group']) && !empty($params['id_group']) ? $params['id_group'] :""; 
      if($action != "edit" || !$id_group)
	  {
		echo Util_Json::json_str(400,'参数错误',[]);
		return false;
	  }
	  $bll_groupon = new Bll_Groupon_Info();
	  //判断该条团购是否有效
      $info = $bll_groupon->get_groupon_can_limit_time($id_group);
      if(!$info)
	  {
	    echo Util_Json::json_str(400,'该团购信息无效',[]);
		return false;
	  }

      //开始修改显示团购商品

	  $res = $bll_groupon->set_limit_time_shop($id_group);

	  if($res){
	     self::del_cache();
	     echo Util_Json::json_str(200,'success',[]);
		 return false;
	  }else{
	     echo Util_Json::json_str(400,'edit fail',[]);
		 return false;
	  }



	}

	public function del_cache()
	{
	    $redis = Util_Redis::redis_server();
		$redis->del("product:groupon_limit_time");
	}
}	
