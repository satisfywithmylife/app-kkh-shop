<?php
apf_require_class("APF_Controller");
class Check_ListController extends APF_Controller{

    public function __construct(){
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET, POST, OPTIONS, DELETE");
        header("Access-Control-Allow-Headers:SIG,DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type, Accept-Language, Origin, Accept-Encoding");
        header("Content-type: application/json; charset=utf-8");
    }   

    public function handle_request(){

        $req = APF::get_instance()->get_request();
        $params = $req->get_parameters();
        Logger::info(__FILE__, __CLASS__, __LINE__, var_export($params, true));
        
        $bll_info = new Bll_News_Info();

        /*$beauty_list[] = [
            'imgurl' => 'https://ss0.bdstatic.com/94oJfD_bAAcT8t7mm9GUKT-xh_/timg?image&quality=100&size=b4000_4000&sec=1555564192&di=9d68c5de46a0aafc0cfba70d3a0896d9&src=http://pic-image.yesky.com/uploadImages/2015/283/31/KZ271N85445V.jpg',
        ];*/
        $beauty_list[] = [
            'imgurl' => 'http://5b0988e595225.cdn.sohucs.com/images/20190418/09bb7ac4b00c4a41ba081206eb2fc111.jpeg',
            'summary' => '2018-10-13 摄于郊区公园'
        ];
        $beauty_list[] = [
            'imgurl' => 'http://5b0988e595225.cdn.sohucs.com/images/20190418/88c7acf2278048d394de8a2eea215b8d.jpeg',
            'summary' => '2019-03-13 摄于山西-出差时期'
        ];
        $beauty_list[] = [
            'imgurl' => 'https://5b0988e595225.cdn.sohucs.com/images/20190417/3b06df8370b54ea6a016e584d00de9b6.jpeg',
            'summary' => '2018-04-04 剑门关'
        ];
        $beauty_list[] = [
            'imgurl' => 'https://05imgmini.eastday.com/mobile/20190418/20190418165340_0ff074839cf13244227d37074c1e1557_1.jpeg',
            'summary' => '2018-07-28 于北京;'
        ];
        $beauty_list[] = [
            'imgurl' => 'https://02imgmini.eastday.com/mobile/20190418/20190418165416_950d77295b1ed0e8e06ca29d52e0033e_1.jpeg',
            'summary' => '2019-04-13 摄于云南 玉龙雪山'
        ];
        $beauty_list[] = [
            'imgurl' => 'http://5b0988e595225.cdn.sohucs.com/images/20190418/d3b8466a7be64867999825732214e5b1.jpg',
            'summary' => '2019-01-05 摄于顾村公园，木棉花'
        ];
        //$beauty_list = $bll_info->get_travel_list(20);
        //foreach($beauty_list as &$v){
        //    $v['summary'] = $v['title'];//$summary[mt_rand(0,5)];
            //$v['imgs'] = $bll_info->get_news_imgs($v['nid'], $v['img_num'] >= 3 ? 3 : 1);                                                      
        //}
        $data = [
            'beauty_list' => $beauty_list,
        ];
        echo Util_Json::json_str(200, 'success', $data);
        return false;
    }

}

