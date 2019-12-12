<?php
apf_require_class("APF_Controller");
class Video_DetailController extends APF_Controller{

    public function __construct(){
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET, POST, OPTIONS, DELETE");
        header("Access-Control-Allow-Headers:SIG,DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type, Accept-Language, Origin, Accept-Encoding");
        header("Content-type: application/json; charset=utf-8");
    }   

    public function handle_request(){

        $req = APF::get_instance()->get_request();
        $params = $req->get_parameters();
        $vid = $params['vid'] ? $params['vid'] : 1;
        //$page_start = $params['page_start'] ? (int)$params['page_start'] : 0;
        //$page_size = $params['page_size'] ? (int)$params['page_size'] : 20;
        Logger::info(__FILE__, __CLASS__, __LINE__, var_export($params, true));
        
        $bll_info = new Bll_Video_Info();

        $video_detail = $bll_info->get_video_detail($vid);
        //foreach($news_list as &$v){
        //    $v['imgs'] = $bll_info->get_news_imgs($v['nid'], $v['img_num'] >= 3 ? 3 : 1);
        //}
        $video_detail = [];
        $data = [
            'video_detail' => $video_detail,
        ];
        echo Util_Json::json_str(200, 'success', $data);
        return false;
    }

}

