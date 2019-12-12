<?php
apf_require_class('APF_Controller');
class Article_HeadLineListController extends APF_Controller
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
	  
	  $action = isset($params['action']) && !empty($params['action']) ? $params['action'] :'';
	  if($action != "view"){
	      	echo Util_Json::json_str(400,'请传入正确的action',[]);
			return false;
	  }
      //获取大标题信息，因为大标题只有一个，查询标题表中最新一条
	  $bll_article           = new Bll_Article_Info();
	  $headline_info         = $bll_article->get_headline();//查询标题信息
	  $article_list          = $bll_article->get_article_list();//查询所有在线文章列表
	  //var_export($article_list);
	  $data["article_list"]  = $article_list;
	  $data["headline_list"] = [];
	  //如果不存在就添加一条记录
	  if(!$headline_info){
	     echo Util_Json::json_str(200,'success',$data);
		 return false;
	  }
      self::cache_data($headline_info,$bll_article);
	  $hid = $headline_info['hid'];
	  $headline_article_list		  = $bll_article->get_headline_article_list($hid);//获取标题下文章列表
	  foreach($headline_article_list as $k=>&$v)
	  {
	     $v['created_at'] = date("Y-m-d H:i:s",$v['created_at']);
		 $v['update_at'] = date("Y-m-d H:i:s",$v['update_at']);
	  }
	  $headline_info['headline_list'] = $headline_article_list;//整合数据
	  $data["headline_list"]          = $headline_info;
	  echo Util_Json::json_str(200,'success',$data);
	  return false;

	}
	//数据缓存
	public function cache_data($headline_info,$bll_article)
	{
	    $hid = $headline_info['hid'];
		$headline_article_list          = $bll_article->get_headline_article_list($hid,10);//获取标题下文章列表
		$headline_info['headline_list'] = $headline_article_list;//整合数据
		$redis = Util_Redis::redis_server();
		$redis->set("product:headline_article_list",json_encode($headline_info,JSON_NUMERIC_CHECK));
	}

}	
