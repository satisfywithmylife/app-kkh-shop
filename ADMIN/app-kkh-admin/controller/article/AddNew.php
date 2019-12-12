<?php

apf_require_class('APF_Controller');

class Article_AddNewController extends APF_Controller
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
		//$data['content'] = isset($params['content']) && !empty($params['content']) ? $params['content'] : '';
		$thirdurl = isset($params['thirdurl']) && !empty($params['thirdurl']) ? $params['thirdurl'] : '';
		//$data['author'] = isset($params['author']) && !empty($params['author']) ? $params['author'] : 1;
		//$data['show_type'] = isset($params['show_type']) && !empty($params['show_type']) ? $params['show_type'] : 0;
		$data['active'] = 1;  //0-offline,1-online;
		$belong = isset($params['belong']) && !empty($params['belong']) ? $params['belong'] : '';
		$share_image = isset($params['share_image']) && !empty($params['share_image']) ? $params['share_image'] : '';
		
		#check admin role
		$bll_admin_user = new Bll_Admin_Info();
		$bll_article_info = new Bll_Article_Info();
		$bll_article_image = new Bll_Article_Image();
		$bll_article_belong = new Bll_Article_Belong();

		$check = $bll_admin_user->check_user_role($username ,$access_token);		
		if(!$check) {
			echo $this->json_str(400, 'access denied', array());
			return false;
		}
		$data['created_by'] = $check['uid'];
		$data['updated_by'] = $check['uid']; 
	
		$image_arr = $belong_arr = [];
		#nesseary data
		if (empty(trim($data['title'])) || empty(trim($thirdurl)) ||empty($belong)) {
			echo $this->json_str(400, '缺少必要参数，title or content or belong', array());
			return false;
		}

		#get article images
		$path = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/'.CURRENT_RELEASE.'/app-kkh-admin/img/';
		$dispatch = $this->get_boring_things($thirdurl);
		//var_dump($dispatch);die;
		$url = 'https://r.xiumi.us/api/statistics/shows/'. $dispatch['code'] .'/hit';
		$data_ = [
			'user_sid' => $dispatch['sid'],
		];
		$json_str = $this->http_post($url, json_encode($data_), $dispatch['code'])//Util_Curl::http_post($url , $json_encode($data_), 0, 1);
		//$json_str = json_encode($data_);
		var_dump($json_str);die;
		$res = json_decode($json_str, true);
		$data_url = 'http:'.$res['data']['show_data_url'];
		$data_source = $this->http_get($data_url);
		$data['content'] = json_decode($data_source, true)['$appendix']['htmlForPreview'];

		//$data['content'] = $this->http_get($data['thirdurl']);
		//var_dump($data['content']);die();  //js动态加载数据的，无法抓
		//$data['content'] = $this->get_content_body($data['content']);
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
				$preh = $this->get_img_type($v);  
				$mime = $preh[2];  //获取图片类型
				$pre_fix = $preh[1]; //前缀头

				$img_data = base64_decode(str_replace($pre_fix, '', $v));  //trim(str_replace(' ', '+', substr($v, strpos($v, ',')+1)));   //获取图片编码，转码
				$realpath = $path . uniqid(rand(), true) . '.' . $mime;
				if(file_exists($realpath)){
					unlink($realpath);
				}

				$res = file_put_contents($realpath, $img_data);
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

		$aid = $bll_article_info->add_article($data);
	
		#set show image

		if ($aid != 0 && !empty($image_arr)) {
			$bll_article_image->add_image($image_arr, $aid, 0);   //list show image
		}

				
		$bll_article_image->add_image($share_image, $aid, 1);   //share image

		$belong_arr = explode("||", $belong);
		if (!empty($belong_arr)) {
			$bll_article_belong->add_article_to_product($belong_arr, $aid);
		}

		echo $this->json_str(200, 'success', array());	
		return false;
	}

	public function get_boring_things($url){
		$code = substr($url, strrpos($url, '/')+1);
		$new_url = str_replace('/'.$code, '', $url);
		$sid = substr($new_url, strrpos($new_url, '/')+1);
		$data = [
			'code' => $code,
			'sid' => $sid,
		];
		return $data;
	}

    function http_post($url, $data, $code){
        $curl = curl_init();
		$header = array(
			'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.139 Safari/537.36',
			'content-type: application/json;charset=UTF-8',
			':authority: r.xiumi.us',
			':method: POST',
			':path: /api/statistics/shows/'.$code.'/hit',
			':scheme: https',
			'accept: application/json, text/plain, */*',
			'accept-encoding: gzip, deflate, br',
			'accept-language: zh-CN,zh;q=0.9',
			'content-length:'.strlen($data),
			'cookie: _ga=GA1.2.639506716.1525255811; _gid=GA1.2.1494838426.1525408442; _gat=1',
			'origin: https://r.xiumi.us',
			//'',
		);
        curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header)
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($curl, CURLOPT_TIMEOUT, 3);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

	function http_get($url) {
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    	curl_setopt($ch, CURLOPT_ENCODING,'gzip,deflate');
    	curl_setopt($ch, CURLOPT_TIMEOUT, 3); 
    	$output = curl_exec($ch);
    	curl_close($ch);
    	return $output;
	}

	public function get_content_body($content){
		$regx = '';
	}

	#判断依否是一个图片
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
	
	#匹配所有图片，返回array（）
	public function get_image($str)
	{	
    	$regex = "/<img.*?src=\"(.*?)\".*?>/";
   		 preg_match_all($regex, $str, $matches, PREG_PATTERN_ORDER);
    	//var_dump($);die)();
    	return $matches[1];//return $matches[1];
	}
}
