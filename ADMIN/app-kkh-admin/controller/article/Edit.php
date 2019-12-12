<?php

apf_require_class('APF_Controller');

class Article_EditController extends APF_Controller
{
    public $url = "upimg.kangkanghui.com";
    
    public $qiniucdn_domain = "https://category.kkhcdn.com/";

    public $bucket = "category";    

    public function handle_request()
    {   
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET, POST, OPTIONS, DELETE");
        header("Access-Control-Allow-Headers:SIG,DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type, Accept-Language, Origin, Accept-Encoding");
        header("Content-type: application/json; charset=utf-8");

        $req = APF::get_instance()->get_request();
        $params = $req->get_parameters();

		$username = isset($params['username']) && !empty($params['username']) ? $params['username'] : '';
		$access_token = isset($params['access_token']) && !empty($params['access_token']) ? $params['access_token'] : '';

		$data['title'] = isset($params['title']) && !empty($params['title']) ? $params['title'] : '';
		$data['content'] = isset($params['content']) && !empty($params['content']) ? $params['content'] : '';
		$data['author'] = isset($params['author']) && !empty($params['author']) ? $params['author'] : 1;
		$data['show_type'] = isset($params['show_type']) && !empty($params['show_type']) ? $params['show_type'] : 0;
		$data['active'] = isset($params['active']) && !empty($params['active']) ? $params['active'] : 0;  //0-offline,1-online;
		$share_image = isset($params['share_image']) && !empty($params['share_image']) ? $params['share_image'] : '';
		$data['aid'] = isset($params['aid']) && !empty($params['aid']) ? $params['aid'] : 0;
		$data['belong'] = isset($params['belong']) && !empty($params['belong']) ? $params['belong'] : '';
		if($data['aid'] == 0) {
            $data = [ 
                'status' => 400,
                'msg' => 'need aid，aid can not be empty',
                'data' => array(), 
            ];  
            echo json_encode($data);
            return false;			
		}		

		#check admin role
		$bll_admin_user = new Bll_Admin_Info();
		$bll_article_info = new Bll_Article_Info();
		$bll_article_image = new Bll_Article_Image();

		$check = $bll_admin_user->check_user_role($username ,$access_token);		
		if(!$check) {
			$data = [
				'status' => 400,
				'msg' => 'access denied',
				'data' => array(), 
			];
			echo json_encode($data);
			return false;
		}
		//$data['created_by'] = $check['uid'];
		$data['updated_by'] = $check['uid']; 
	
		#nesseary data
		if (empty(trim($data['title'])) || empty(trim($data['content'])) || empty(trim($data['belong']))) {
			$data = [
				'status' =>400,
				'msg' => '缺少必要参数,title or content or belong',
				'data' => [],
			];
			echo json_encode($data);
			return false;
		}

		#get article images
		$image_arr = [];
        
		#get article images
        $path = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/'.CURRENT_RELEASE.'/app-kkh-admin/img/';
        //Logger::info(__FILE__, __CLASS__, __LINE__, 'path: '.$path);
        $image_arr = self::get_image($data['content']);
    
        $data['content'] = $this->setImgSiteAction($data['content']);   //设置图片替换符
        if($image_arr){
            foreach($image_arr as $k=>$v) {
                #是否是一个资源定位符，是则调用远程上传七牛云方法；
                if($this->is_img_url($v)){
                    $imgurl = self::upload_remote($v);
                    if(!$imgurl) {
                        echo $this->json_str(400, '上传远程图片到七牛云失败', array());
                        return false;
                    }   
                    $data['content'] = $this->exchange_img_once($data['content'], $imgurl);  //替换图片为正确的七牛地址
                    $image_arr[$k] = $imgurl;  //图片存表做准备
                    continue;
                }   

                #base64~
        //      Logger::info(__FILE__, __CLASS__, __LINE__, 'v: '.$v);
                $preh = $this->get_img_type($v);  
                $mime = $preh[2];  //获取图片类型
                $pre_fix = $preh[1]; //前缀头

        //      Logger::info(__FILE__, __CLASS__, __LINE__, 'mime: '.$mime);
                $img_data = base64_decode(str_replace($pre_fix, '', $v));  //trim(str_replace(' ', '+', substr($v, strpos($v, ',')+1)));   //获取图片编码，转码
        //      Logger::info(__FILE__, __CLASS__, __LINE__, 'base64: '.$img_data);
                $realpath = $path . uniqid(rand(), true) . '.' .$mime;
                if(file_exists($realpath)){
                    unlink($realpath);
                }   

                $res = file_put_contents($realpath, $img_data);
        //      Logger::info(__FILE__, __CLASS__, __LINE__, var_export($res, true));
                if(!$res) {
                    echo $this->json_str(400, '图片转存本地失败', array());
                    return false;
                }   

                chmod($realpath, 0777);  //给与权限
    
                $imgurl = self::upload($realpath);  //上传本地图片到七牛云
                if(!$imgurl){
                    echo $this->json_str(400, '上传图片到七牛云失败', array());
                    return false;
                }   
    
                unlink($realpath);  //clear temp img
    
                $data['content'] = $this->exchange_img_once($data['content'], $imgurl);  //替换图片为正确的七牛地址
                $image_arr[$k] = $imgurl;  //图片存表做准备
            }   
        }   
		
		Logger::info(__FILE__, __CLASS__, __LINE__, var_export($image_arr, true));		

		#auto set show_type
		if ($data['show_type'] == 0){
			$data['show_type'] = count($image_arr) >= 3 ? 3 : count($image_arr);
		}

		$res = $bll_article_info->edit_article($data);
		
		if ($res && !empty($image_arr)) {
			$bll_article_image->add_image($image_arr, $data['aid'], 0);
		}

		$bll_article_image->add_image($share_image, $data['aid'], 1);
        //删除科普文缓存
		Util_Redis::del_cache("product:headline_article_list");
		$data = [
			'status' => 200,
			'msg' => 'success',
			'data' => array(),
		];
		echo json_encode($data);	
		return false;
	}


    public function is_img_url($str) {
        if(strlen($str) < 255){
            return true;
        }   
        return false;
    }   

    #单次替换图片
    public function exchange_img_once($str, $imgurl){
        $regx = '/%ImgPostion%/';
        $left = '<img src="';
        $right = '">';
        $img_str = $left . $imgurl . $right;
    
        $content = preg_replace($regx, $img_str, $str, 1); 
        return $content;
    }   

    #json
    public function json_str($code, $msg, $data){
        $j_data = [ 
            'status' => $code,
            'msg' => $msg,
            'data' => $data,
        ];  
        $res = json_encode($j_data, JSON_NUMERIC_CHECK);
        return $res;
    }   
    
    #远程图片上传
    public function upload_remote($imgurl) {
        $data = [ 
            'remote' => 1,
            'bucket' => $this->bucket,
            'url' => $imgurl,
            'pickey' => uniqid(rand(), true),
        ];  
    
        $url = $this->url;
        $res = Util_Curl::http_post($url, $data, 0); 
        if(isset($res['status']) && $res['status'] == 200){
            return $this->qiniucdn_domain . $res['pickey'];
        } else {
            return false;
        }   
    }   

    #上传七牛云
    public function upload($path){
        $path = '@'.realpath($path);
        $data = [ 
            'my_field' => $path,
            'type' => '', 
            'pickey' => uniqid(rand(), true),
            'bucket' => $this->bucket,
        ];  

        $url = $this->url;
        $res = Util_Curl::http_post($url, $data, 0); 
        if (isset($res['status']) && $res['status'] ==1){
            return $this->qiniucdn_domain . $res['hashkey'];
        } else {
            return false;
        }   
    }   
    
    #获取图片类型，前缀
    public function get_img_type($str){
        $regx = '/^(data:\s*image\/(\w+);base64,)/';
        preg_match($regx, $str, $result);
        return $result;
    }

    #替换img标签,用自定义占位符(ImgPostion)替换
    public function setImgSiteAction($str){
        $regex = "/<img.*?src=\"(.*?)\".*?>/is";
        $newcontent = preg_replace(
            $regex,
            '%ImgPostion%',
            $str
        );  
        return $newcontent;
    }   

	public function get_image($str)
	{	
    	$regex = '/<img.*?src=\"(.*?)\".*?>/';
   		 preg_match_all($regex, $str, $matches, PREG_PATTERN_ORDER);
    	//var_dump($);die)();
    	return $matches[1];//return $matches[1];
	}
    //删除缓存数据
	private function del_cache()
	{
         $redis = Util_Redis::redis_server();
		 $redis->del("product:headline_article_list");
	}
}
