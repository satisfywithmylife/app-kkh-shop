<?php
require_once dirname(__FILE__) . '/../phpqrcode/phpqrcode.php';

class Util_Qr
{
	public $qr;

	public function __construct(){
		$this->qr = new QRcode(); 
	}
		
	public function make_code($content, $filename = ""){
		if(!$filename){
			$filename = IMG_PATH . uniqid(rand(), true) . '.png';//;$_SERVER['DOCUMENT_ROOT'];
		}
		$errorCorrectionLevel = 'L';    //容错级别 
		$matrixPointSize = 5;
		QRcode::png($content,$filename , $errorCorrectionLevel, $matrixPointSize, 2);
		//$QR = imagecreatefromstring(file_get_contents($QR));
		return $filename;
	}

}
?>
