<?php
apf_require_class("APF_Controller");
class News_ListController extends APF_Controller{

    public function __construct(){
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET, POST, OPTIONS, DELETE");
        header("Access-Control-Allow-Headers:SIG,DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type, Accept-Language, Origin, Accept-Encoding");
        header("Content-type: application/json; charset=utf-8");
    }   

    public function handle_request(){

        $req = APF::get_instance()->get_request();
        $params = $req->get_parameters();
        $chaid = $params['chaid'] ? $params['chaid'] : 1;
        $page_start = $params['page_start'] ? (int)$params['page_start'] : 0;
        $page_size = $params['page_size'] ? (int)$params['page_size'] : 20;
        Logger::info(__FILE__, __CLASS__, __LINE__, var_export($params, true));
        
        $bll_info = new Bll_News_Info();

        $news_list = $bll_info->get_news_list($chaid, $page_start, $page_size);
        foreach($news_list as &$v){
            $v['imgs'] = $bll_info->get_news_imgs($v['nid'], $v['img_num'] >= 3 ? 3 : 1);
        }
        $news_list = [];
        $data = [
            'news_list' => $news_list,
        ];
        echo Util_Json::json_str(200, 'success', $data);
        return false;
    }

}

