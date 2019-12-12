<?php

class Util_Upload{
	public $bucket;
	public $url;
	public $qiniucdn_domain;
	
	function __construct(){
		$this->bucket = 'category';
		$this->url = 'upimg.kangkanghui.com';
		$this->qiniucdn_domain = 'https://category.kkhcdn.com/';
	}

	public function upload_local($path){
        $path = '@'.realpath($path);
        $data = [ 
            'my_field' => $path,
            'type' => '', 
            'pickey' => uniqid(rand(), true),
            'bucket' => 'category',//$this->bucket,
        ];

        $url = 'upimg.kangkanghui.com';//$this->url;
        $res = Util_Curl::http_post($url, $data, 0);
 		//return json_encode($data);
 		if (isset($res['status']) && $res['status'] ==1){
            return 'https://category.kkhcdn.com/'. $res['hashkey'];//$this->qiniucdn_domain . $res['hashkey'];
        } else {
            return false;
        }
	}

	public function upload_remote($remote_url){
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
}
