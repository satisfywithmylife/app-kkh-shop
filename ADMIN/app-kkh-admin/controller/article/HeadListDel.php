<?php
apf_require_class('APF_Controller');
class Article_HeadListDelController extends APF_Controller
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
		$action = isset($params['action']) && !empty($params['action']) ? $params['action'] :'';  //action  行为
		$aid    = isset($params['aid']) && !empty($params['aid']) ? $params['aid'] :'';//aid 文章id
		$hid    = isset($params['hid']) && !empty($params['hid']) ? $params['hid'] :'';//标题id

		if($action != 'del')
		{
		    echo Util_Json::json_str(400,'请传入正确的action信息',[]);
			return false;
		}
		if(!$aid || !$hid)
		{
		    echo Util_Json::json_str(400,'参数错误',[]);
			return false;
		}
		//实例化文章类
		$bll_article = new Bll_Article_Info();
		$data['aid'] = $aid;
		$data['hid'] = $hid;
		$res = $bll_article->del_headline_article($data);
		if($res)
		{
		    self::del_cache();
		    echo Util_Json::json_str(200,'success',[]);
			return false;
		}
		else
		{
		    echo Util_Json::json_str(400,'fail',[]);
			return false;
		}


	}
     //数据改变删除缓存
	private function del_cache()
	{
	    $redis = Util_Redis::redis_server();
		$redis->del("product:headline_article_list");
	}
}
