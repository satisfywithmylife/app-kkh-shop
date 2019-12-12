<?php

apf_require_class('APF_Controller');

class Upload_UploadFileController extends APF_Controller
{
    public $url = "upimg.kangkanghui.com";

    public $qiniucdn_domain = "https://category.kkhcdn.com/";        //换成自己的

    public $bucket = "category";    

    public function handle_request()
    {   
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET, POST, OPTIONS, DELETE");
        header("Access-Control-Allow-Headers:SIG,DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type, Accept-Language, Origin, Accept-Encoding");
        header("Content-type: application/json; charset=utf-8");
        $req = APF::get_instance()->get_request();
		$param = $req->get_parameters();
		$path = $_FILES['file']['tmp_name'];
		//$temp_name = $_FIlES['file']['name'];
		//Logger::info(__FILE__, __CLASS__, __LINE__, var_export($_FILES, true));	
		//move_uploaded_file($temp_file, 'img/'.$temp_name);

        //$path = $_SERVER['DOCUMENT_ROOT'].'/2017_08_18/app-kkh-admin/img/'.$temp_name;
		Logger::info(__FILE__, __CLASS__, __LINE__, $path);
        $fp = '@'.realpath($path);

		$post_data = [
		    'my_field' => $fp,
            'type' => '',
            'bucket' => $this->bucket,
            'pickey' => uniqid(rand(), true),	
		 ];
		 
        $res = Util_Curl::post($this->url, $post_data);
		$res = json_decode($res['content'], true);
		Logger::info(__FILE__, __CLASS__, __LINE__, var_export($res, true));			
        if (isset($res['status']) && $res['status'] == 1) {
             $data = [
				'status' => 200,
				'data' => [
					'image_url' => $this->qiniucdn_domain . $res['hashkey'],
					],
				'msg' => 'success',
			 ];
         } else {
             $data = [
				'status' => 400,
				'msg' => '上传文件失败，请检查文件是否正确',
				'data' => array(),
			 ];
         }
		
		echo json_encode($data, JSON_NUMERIC_CHECK);
		return false;

	}
}
