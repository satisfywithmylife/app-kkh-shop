<?php
apf_require_class("APF_Controller");
class News_DetailController extends APF_Controller{

    public function __construct(){
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET, POST, OPTIONS, DELETE");
        header("Access-Control-Allow-Headers:SIG,DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type, Accept-Language, Origin, Accept-Encoding");
        header("Content-type: application/json; charset=utf-8");
    }   

    public function handle_request(){

        $req = APF::get_instance()->get_request();
        $params = $req->get_parameters();
        $nid = $params['nid'] ? $params['nid'] : 1;
        //$page_start = $params['page_start'] ? (int)$params['page_start'] : 0;
        //$page_size = $params['page_size'] ? (int)$params['page_size'] : 20;
        Logger::info(__FILE__, __CLASS__, __LINE__, var_export($params, true));
        
        $bll_info = new Bll_News_Info();

        $news_detail = $bll_info->get_news_detail($nid);

        //foreach($news_list as &$v){
        //    $v['imgs'] = $bll_info->get_news_imgs($v['nid'], $v['img_num'] >= 3 ? 3 : 1);
        //}
        $imgs = $bll_info->get_news_imgs($nid, 150);
        $news_detail['content'] = $this->deal_data($news_detail['content'], $imgs);
        $news_detail = $imgs = [];
        $data = [
            'news_detail' => $news_detail,
            'imgs' => $imgs,
        ];
        echo Util_Json::json_str(200, 'success', $data);
        return false;
    }

    public function deal_data($content, $imgs){
        $result = $this->setImgSiteAction($content);
        $left = '<div style="text-align:center;"><img src="';
        $right = '" style="max-width:100%;"></div>';
        $regx = '/%ImgPostion%/';
        foreach($imgs as $kval){
            $str = $left . $kval['imgurl'] . $right;
            $result = preg_replace($regx, $str, $result, 1);
        }
        //$result = str_replace('<br>', "</view><view class='text-body'>", $result);
        //$result = "<view class='text-body'>".$result."</view>";
        return $result;

    }

    #替换img标签,用自定义占位符(ImgPostion)替换
    function setImgSiteAction($str){
        $regex = "/<img.*?src=\"(.*?)\".*?>/is";
        $newcontent = preg_replace(
            $regex,
            '%ImgPostion%',
            $str
        );
        return $newcontent;
    }
}

