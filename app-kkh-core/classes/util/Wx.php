<?php

class Util_Wx
{
	private $appid;
	private $appsecret;
	private $redis;

	public function get_access_token(){
		$access_token = '';
		$this->appid = MP_APP_ID;
		$this->appsecret = MP_APP_SECRET;
		$this->redis = new Redis();
		$this->redis->connect('127.0.0.1', 6379);
		$access_token = $this->redis->get('xapp_access_token');
		if ($access_token){
			return $access_token;
		}
		
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->appsecret;
		$data = Util_Curl::http_post($url, array(), 0);
		$this->redis->set('xapp_access_token', $data['access_token'], 3600);
		return $data['access_token'];
	}

	public function get_qr($access_token, $path = "pages/shop/index"){
//		header('content-type:image/jpg');
		if(!$access_token) return '';
		//return $access_token;
		$url = "https://api.weixin.qq.com/wxa/getwxacode?access_token=$access_token";
		//$url = "https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode?access_token=$access_token";
		//$url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=$access_token";
		#这里有个坑，请求必须以post方式，数据以json格式，且post的trans。。。不能为1
		$data = [
			'path' => $path,
			'width' => 460,
			'auto_color' => true,
		];
		$data = json_encode($data);
		$res = Util_Curl::http_post($url, $data, 0, 1);
		$filename = IMG_PATH . uniqid(rand(), true) . '.jpg';
		//echo $filename;die;
		$re = file_put_contents($filename, $res);
		if(!$re){
			return '';
		}
		$img_url = Util_Upload::upload_local($filename);
		unlink($filename);
		return $img_url;
	}
}
