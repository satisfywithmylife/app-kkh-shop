<?php
apf_require_class("APF_Controller");

class Comment_ImportExternalController extends APF_Controller {
    private static $error_ret = array(
        "status" => 400,
        "data" => array(),
        "msg" => ""
    );

    private static $success_ret = array(
        "status" => 200,
        "data" => array(),
        "msg" => ""
    );

    public function handle_request()
    {
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET, POST, OPTIONS, DELETE");
        header("Access-Control-Allow-Headers:SIG,DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type, Accept-Language, Origin, Accept-Encoding");
        header("Content-type: application/json; charset=utf-8");
		
        $req = APF::get_instance()->get_request();
        $param = $req->get_parameters();
		
		if(empty($param) || !isset($param['id_product']) || empty($param['url_jd'])) {
			self::$error_ret['msg'] = 'invalid param';
			Logger::info(__METHOD__ . ' invalid param, line = ' . __LINE__);
			echo json_encode(self::$error_ret);
			return false;
		}

		$param_check_set = Comment_ProductListController::check_param_set($param, array(
			'id_product',
			'url_jd', //要爬的京东商品url
			'operator'
		));
		$param_check_empty = Comment_ProductListController::check_param_empty($param, array(
			'id_product',
			'url_jd',
			'operator'
		));
		if ($param_check_set === false || $param_check_empty === false) {
			self::$error_ret['status'] = 300;
			self::$error_ret['msg'] = 'invalid param';
			echo json_encode(self::$error_ret);
			Logger::info(__METHOD__ . ' invalid param, line = ' . __LINE__);
			return false;
		}

		//import jd
		$spider_data = array();
		$id_product = intval($param['id_product']);
		$url_jd = $param['url_jd'];
		$size = 5; //要爬的评论数量,因要上传图片到七牛云，为保证接口效率，请小于10 todo
		$spider_data = $this->getJdComment($url_jd, $size, $id_product);

		//import tm todo
		
		if(empty($spider_data)) {
			self::$error_ret['msg'] = 'this url have no comment';
			echo json_encode(self::$error_ret);
			Logger::info(__METHOD__ . ' spider_data empty, line = ' . __LINE__);
			return false;
		}
		
        $bll_comment = new Bll_Comment_Info();
        $ret = $bll_comment->importExternal($spider_data);
		if($ret !== true) {
			echo json_encode(self::$error_ret);
			Logger::info(__METHOD__ . ' db fail, line');
			return false;
		}
		
		$operator = rawurldecode($param['operator']); //导入外部评论的操作人
		$ret = $bll_comment->saveExtInfo($id_product, $url_jd, '', $operator); //todo tianmao buzuo
		self::$success_ret['data'] = $bll_comment->externalInfoSingle($id_product);
		echo json_encode(self::$success_ret);
        return false;
    }

	/**
	 * @return string
	 */
	public function getJdComment($url, $size, $id_product) {
		$ret = array();

		$postUrl = 'http://spider.prod.kangkanghui.com/jd/product/spider/comment';
        $curlPost = array(
			'url' => $url, 
			'size' => $size
		); 

        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);
		
		Logger::info('data = ' . json_encode($data));
		if(empty($data)) {
			Logger::info(__METHOD__ . ' spider return empty');
			return $ret;
        }
		$data = json_decode($data, true);
        if(!is_array($data)) {
            Logger::info(__METHOD__ . ' spider return invalid, cannot transform to array');
			return $ret;
        }   
        if(empty($data) || !isset($data['status']) || empty($data['data']) || empty($data['data']['comments'])) {
            Logger::info(__METHOD__ . ' spider content is empty array, url = ' . $url);
            return $ret;
        } 

		$status = intval($data['status']);
		if($status !== 200) {
			Logger::info(__METHOD__ . ' spider fail, status = ' . $status);
			return $ret;
		}
		
		$data = $data['data']['comments'];
		$now_ts = time();
		$now = date('Y-m-d H:i:s', $now_ts);
		$tmpArrs = array();
		$tmpArr = array(
			'id_product' => $id_product,
			'kkid' => 0, //todo 为0表示外部用户, 貌似不行
			'quality_score' => 5, //todo 默认最高评分
			'service_score' => 5,
			'logistics_score' => 5,
			'content' => '',
			'picture' => '',
			'display' => 0, //todo 默认不显示
			'id_source' => 1,
			'comment_nature' => 1, //todo 默认好评
			'comment_ts' => $now //todo 用评论中的还是当前的
		);

		foreach($data as $k => $v) {
			if(empty($v) || (empty($v['picture']) && empty($v['content']))) {
				Logger::info(__METHOD__ . ' spider data invalid, data = ' . json_encode($v));
				continue;
			}

			$kkid = $now_ts . ($k + 1);

			$tmpArr['kkid'] = $kkid;
			$tmpArr['name'] = !empty($v['nickname']) ? $v['nickname'] : '';
			$tmpArr['user_photo'] = !empty($v['userImageUrl']) ? $v['userImageUrl'] : '';
			$tmpArr['picture'] = !empty($v['images']) ? json_encode($v['images']) : ''; //todo
			$tmpArr['content'] = !empty($v['content']) ? $v['content'] : ''; 
			$tmpArr['comment_ts'] = !empty($v['creationTime']) ? $v['creationTime'] : $now;
			$tmpArr['content'] = !empty($v['content']) ? $v['content'] : '';
            $tmpArr['comment_ts'] = !empty($v['creationTime']) ? $v['creationTime'] : $now;

			//test
			Logger::info('picture = ' . json_encode($v['images']));

			$tmpArrs[] = $tmpArr;
		}
		
		$ret = $tmpArrs;
//		Logger::info('ret = ' . json_encode($ret));
		
		return $ret;
	}
}
