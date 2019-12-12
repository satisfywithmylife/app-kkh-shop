<?php
USE Intervention\Image\ImageManagerStatic as Image;

apf_require_class("APF_Controller");
class Image_MakeController extends APF_Controller{

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

        $type = isset($params["type"]) ? $params["type"] : ''; 
	$avatar = isset($params["avatar"]) ? $params["avatar"] : '';

	Image::configure(array('driver' => 'imagick'));
	$avatarImg = Image::make($avatar)->resize(131, 131);

	$backImg = Image::canvas(200, 200, '#fff');
	$flag = Image::make(dirname(__FILE__).'/../../image/cnflag.png')->resize(180, 180);
	$backImg->insert($flag, 'top-left', 10, 10);
	$backImg  = $backImg->getCore();
	$backImg->roundCorners(100, 100);
	$backImg = Image::make($backImg)->resize(31, 31);
	$avatarImg->insert($backImg, 'top-left', 100, 100);
	$avatarImg = $avatarImg->getCore();
	$avatarImg->roundCorners(15, 15);
	$avatarImg = Image::make($avatarImg);
	echo $avatarImg->response();
	
    }

}

