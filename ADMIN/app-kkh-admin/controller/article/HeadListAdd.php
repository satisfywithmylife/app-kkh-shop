<?php
apf_require_class('APF_Controller');
class Article_HeadListAddController extends APF_Controller
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
		//获取必要参数
		$action      = isset($params['action']) && !empty($params['action']) ? $params['action'] :'';  //action  用户行为 修改标题 edithead  新增文章 add
		$aid         = isset($params['aid']) && !empty($params['aid']) ? $params['aid'] :'';  //aid  文章id  修改标题时为空
        $position    = isset($params['position']) && !empty($params['position']) ? $params['position'] :'';//文章显示顺序 修改标题时为空
		$hid         = isset($params['hid']) && !empty($params['hid']) ? $params['hid'] :'';//标题id 若不存在标题就不用传
		$headline    = isset($params['headline']) && !empty($params['headline']) ? $params['headline'] :'';//标题内容 新增或修改是传参
		$subhead     = isset($params['subhead']) && !empty($params['subhead']) ? $params['subhead'] :'';//副标题内容 新增或修改是传参
		$bll_article = new Bll_Article_Info();
		//如果action=edithead同时hid为空就走新增标题接口
		if($action == "edithead")
		{
		  
		  if($headline == '' || $subhead == '')
		  {
		     echo Util_Json::json_str(400,'标题、副标题内容不能为空',[]);
			 return false;
		  }
		  $data['headline'] = $headline;
		  $data['subhead']  = $subhead;
		  
		  if(!$hid){//新增标题
		     $res = $bll_article->add_headline($data);
		  }
		  else //修改标题
		  {
		     $data['hid']      = $hid;
			 $res = $bll_article->edit_headline($data);
		  }
		}
		elseif($action == "add")
		{
		   //判断参数是否正确
		   if(!$hid || !$aid || $position<0){
		       echo Util_Json::json_str(400,'缺少必要的参数,显示顺序不能小于0',[]);
			   return false;
		   }
		   //判断hid对应的标题是否存在
		   $info   = $bll_article->get_headline_by_id($hid);
		   if(!$info)
		   {
		       echo Util_Json::json_str(400,'亲，您添加的标题不存在，请核对后重试',[]);
			   return false;
		   }
		   //添加标题下的文章
		   $data['hid']      = $hid;
		   $data['aid']      = $aid;
		   $data['position'] = $position;
		   $res = $bll_article->add_headline_article($data);
		
		}
		else{
		    echo Util_Json::json_str(400,'请传入正确的action参数',[]);
			return false;
		}


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

	private function del_cache()
	{
	    $redis = Util_Redis::redis_server();
		$redis->del("product:headline_article_list");
	}
}
