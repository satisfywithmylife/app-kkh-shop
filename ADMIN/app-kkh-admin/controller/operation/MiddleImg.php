<?php
/**
 * Created by PhpStorm.
 * User: HP
 * Date: 2018/4/8
 * Time: 16:01
 * 运营图片加载类  精选  口碑   新品推荐
 */

apf_require_class('APF_Controller');
class Operation_MiddleImgController extends APF_Controller
{   
    public function handle_request()
    {
        // TODO: Implement handle_request() method.
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET, POST, OPTIONS, DELETE");
        header("Access-Control-Allow-Headers:SIG,DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type, Accept-Language, Origin, Accept-Encoding");
        header("Content-type: application/json; charset=utf-8");
        $redis = Util_Redis::redis_server();
		$req = APF::get_instance()->get_request();
        $params = $req->get_parameters();
        $s_re = Util_AdminSecurity::Security($params);
        if(!$s_re){
           echo Util_Json::json_str(400,'access denied',[]);
            return false;
        }

        $action = isset($params['action']) && !empty($params['action']) ? $params['action'] :'';
        if($action == ""){
            echo Util_Json::json_str(400,'缺少必要参数action',[]);
            return false;
        }

        $bll_operation_info = new Bll_Operation_Info();
        $data = $bll_operation_info->opreation_list();
		if(!$data)
		{
			//没有数据新增
		    $info['type']    = 1;
			$info['name']    = "模块";
			$info['is_show'] = 1;
			$info['img_url'] = "";
			$bll_operation_info->add($info);
			$info['type']    = 2;
			$bll_operation_info->add($info);
			$info['type']    = 3;
			$bll_operation_info->add($info);
			
			$data = $bll_operation_info->opreation_list();

		}
		self::cache_data($data);

        echo Util_Json::json_str(200,'success',$data);

    }

	private function cache_data($data)
	{   
        //实例化redis
	    $redis = Util_Redis::redis_server();
		//处理数据
		foreach($data as $k=>$v)
		{
		   if($v['type'] == 1)
		   {
			   $operation_shop['qpg_choiceness_img'] = $v['img_url'];
		   }
		   elseif($v['type'] == 2)
		   { 
			   $operation_shop['qpg_public_praise_img'] = $v['img_url'];
		   }
		   elseif($v['type'] == 3)
		   {
               $operation_shop['qpg_new_recommend_img'] = $v['img_url'];
		   }
		}
	    $redis->set('product:recommend_middleimg',json_encode($operation_shop));
	}



}
