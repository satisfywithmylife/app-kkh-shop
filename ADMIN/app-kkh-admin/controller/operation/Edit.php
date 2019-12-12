<?php
/**
 * Created by PhpStorm.
 * User: HP
 * Date: 2018/4/8
 * Time: 16:53
 * 运营图片修改类  精选  口碑   新品推荐
 */
apf_require_class('APF_Controller');
class Operation_EditController extends APF_Controller
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
        $id   = isset($params['id']) && !empty($params['id']) ? $params['id'] :'';  //id  运营图片id
        $type = isset($params['type']) && !empty($params['type']) ? $params['type'] :3;  //type  运营图片类型1  精选  2  口碑爆款  3 新品推荐
        //必要参数判定
        if(!$id || !$type){
            echo Util_Json::json_str(400,'非法请求',[]);
            return false;
        }
        //修改的两个选项不能都为空
        $name = isset($params['name']) && !empty($params['name']) ? $params['name'] :'';  //name  运营图片name
        $img_url = isset($params['img_url']) && !empty($params['img_url']) ? $params['img_url'] :'';  //img_url  运营图片地址
        if(!$name && !$img_url){
            echo Util_Json::json_str(400,'缺少参数',[]);
            return false;
        }
        $is_show = isset($params['is_show']) && !empty($params['is_show']) ? $params['is_show'] :1;  //is_show  运营图片饰扣显示
        $action = isset($params['action']) && !empty($params['action']) ? $params['action'] :'';  //action  行为
        //用户行为判断
        if($action != "edit"){
            echo Util_Json::json_str(400,'请传入正确的action',[]);
            return false;
        }
        //实例化
        $bll_operation_info = new Bll_Operation_Info();
        //修改对应的数据
		if($name && $img_url)
		{
		   $data['name'] = $name;
		   $data['img_url'] = $img_url;
		   $data['id']   = $id;
		   $res = $bll_operation_info->edit_info($data);

		}
        elseif($name){
		    $data['name'] = $name;
			$data['id']   = $id;
            $res = $bll_operation_info->name_edit($data);
        }elseif($img_url){
		    $data['img_url'] = $img_url;
			$data['id']   = $id;
            $res = $bll_operation_info->img_edit($data);
        }else{
            $res = '';
        }

        if($res){
            echo Util_Json::json_str(200,'success',[]);
            return false;
        }else{
            echo Util_Json::json_str(400,$res,[]);
            return false;
        }

    }
}
