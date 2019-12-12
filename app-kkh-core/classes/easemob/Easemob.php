<?php
class Easemob_Easemob {
	private $client_id;
	private $client_secret;
	private $org_name;
	private $app_name;
	private $url;
	private $easemob_chat_host;
	private $token;
    const TOKEN_KEY = "easemob_token";

    //------------------------------------------------------用户体系	
		/**
	 * 初始化参数
	 *
	 * @param array $options   
	 * @param $options['client_id']    	
	 * @param $options['client_secret'] 
	 * @param $options['org_name']    	
	 * @param $options['app_name']   		
	 */
	public function __construct() { }	

    public static function create()
    {
      $obj = new Easemob_Easemob();
      if ($obj->init()) return $obj;

      return NULL;
    }

	/**
	*获取token 
	*/
	public function getToken()
	{
		return "Authorization:Bearer ".$this->token;
	}
	/**
	  授权注册
	*/

    public function registerUser($userName, $nickName = "") {
      if (empty($userName)) {
        return array('status' => 'error', 'msg' => 'invalid parameter');
      }
  
	  $url = self::url().'/users';
      $data = array(
          'username' => $userName,
          'password' => 'WISx5IxlgUVN',
      );
      if (!empty($nickName)) {
        $data['nickname'] = $nickName;
      }
      $result = Util_Curl::post($url, $data, array(
                              'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
                              'Authorization' => 'Bearer '.$this->token));
      if ($result['code'] = 200) {
        $result = json_decode($result['content']);
        if (empty($result->error)) {
          return array('status' => 'ok', 'msg' => '', 'result' => $result);
        } else if (preg_match("/duplicate_unique/", $result->error)) {
          return array('status' => 'ok', 'msg' => 'user exist', 'result' => $result);
        }
        return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
      } else {
        $this->refreshToken();
      }
  
      return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
    }

	
	/*
		获取单个用户
	*/
  public function getUser($userName) {
    if (empty($userName)) {
      return array('status' => 'error', 'msg' => 'invalid parameter');
    }

    $url = self::url()."/users/{$userName}";
    $result = Util_Curl::get($url, array(), array(
        'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
        'Authorization' => 'Bearer '.$this->token));
    if ($result['code'] == 200) {
      $result = json_decode($result['content']);
      if (empty($result->error)) {
        return array('status' => 'ok', 'msg' => '', 'result' => $result);
      }
      return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
    } else {
      $this->refreshToken();
    }

    return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
  }
	
	/*
		修改用户昵称
	*/
	public function editNickname($username,$nickname){
        $url = self::url()."/users/$username";
        //return array('url' => $url, 'nickname'=>$nickname);
		$data=array("nickname"=>$nickname);
        $result = Util_Curl::put1($url, $data, array(
            'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
            'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] == 200) {
            $result = json_decode($result['content']);
            if (empty($result->error)) {
                return array('status' => 'ok', 'msg' => '', 'result' => $result);
            }
            return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
            $this->refreshToken();
        }
    
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	/*
		添加好友-
	*/
	public function addFriend($username,$friend_name){
        $url = self::url()."/users/".$username.'/contacts/users/'.$friend_name;
		$data=array();
        $result = Util_Curl::post($url, array(), array(
            'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
            'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] == 200) {
            $result = json_decode($result['content']);
            if (empty($result->error)) {
                return array('status' => 'ok', 'msg' => '', 'result' => $result);
            }
            return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
            $this->refreshToken();
        }
    
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
			
	}
	
	
	/*
		删除好友
	*/
	public function deleteFriend($username,$friend_name){
        $url = self::url()."/users/".$username.'/contacts/users/'.$friend_name;
		$data=array();
        $result = Util_Curl::delete1($url, array(), array(
            'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
            'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] == 200) {
            $result = json_decode($result['content']);
            if (empty($result->error)) {
                return array('status' => 'ok', 'msg' => '', 'result' => $result);
            }
            return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
            $this->refreshToken();
        }
    
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
		
	}
	/*
		查看好友
	*/
	public function showFriends($username){
        $url = self::url()."/users/".$username.'/contacts/users';
		$data=array();
        $result = Util_Curl::get($url, array(), array(
            'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
            'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] == 200) {
            $result = json_decode($result['content']);
            if (empty($result->error)) {
                return array('status' => 'ok', 'msg' => '', 'result' => $result);
            }
            return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
            $this->refreshToken();
        }
    
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
		
	}
	/*
		查看用户黑名单
	*/
	public function getBlacklist($username){
        $url = self::url()."/users/".$username.'/blocks/users';
		$data=array();
        $result = Util_Curl::get($url, array(), array(
            'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
            'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] == 200) {
            $result = json_decode($result['content']);
            if (empty($result->error)) {
                return array('status' => 'ok', 'msg' => '', 'result' => $result);
            }
            return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
            $this->refreshToken();
        }
    
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	/*
		往黑名单中加人
	*/
	public function addUserForBlacklist($username,$data){
        $url = self::url()."/users/".$username.'/blocks/users';
        $result = Util_Curl::post($url, array(), array(
            'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
            'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] == 200) {
            $result = json_decode($result['content']);
            if (empty($result->error)) {
                return array('status' => 'ok', 'msg' => '', 'result' => $result);
            }
            return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
            $this->refreshToken();
        }
    
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
		
	}
	/*
		从黑名单中减人
	*/
	public function deleteUserFromBlacklist($username,$blocked_name){
        $url = self::url()."/users/".$username.'/blocks/users/'.$blocked_name;
        $data=array();
        $result = Util_Curl::delete1($url, array(), array(
            'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
            'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] == 200) {
            $result = json_decode($result['content']);
            if (empty($result->error)) {
                return array('status' => 'ok', 'msg' => '', 'result' => $result);
            }
            return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
            $this->refreshToken();
        }
    
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	 /*
		查看用户是否在线
	 */
	public function isOnline($username){
        $url = self::url()."/users/".$username.'/status';
        $data=array();
        $result = Util_Curl::get($url, array(), array(
            'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
            'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] == 200) {
            $result = json_decode($result['content']);
            if (empty($result->error)) {
                return array('status' => 'ok', 'msg' => '', 'result' => $result);
            }
            return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
            $this->refreshToken();
        }
    
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	/*
		查看用户离线消息数
	*/
	public function getOfflineMessages($username){
        $url = self::url()."/users/".$username.'/offline_msg_count';
        $data=array();
        $result = Util_Curl::get($url, array(), array(
            'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
            'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] == 200) {
            $result = json_decode($result['content']);
            if (empty($result->error)) {
                return array('status' => 'ok', 'msg' => '', 'result' => $result);
            }
            return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
            $this->refreshToken();
        }
    
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	/*
		查看某条消息的离线状态
		----deliverd 表示此用户的该条离线消息已经收到
	*/
	public function getOfflineMessageStatus($username,$msg_id){
        $url = self::url()."/users/".$username.'/offline_msg_status/'.$msg_id;
        $data=array();
        $result = Util_Curl::get($url, array(), array(
            'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
            'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] == 200) {
            $result = json_decode($result['content']);
            if (empty($result->error)) {
                return array('status' => 'ok', 'msg' => '', 'result' => $result);
            }
            return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
            $this->refreshToken();
        }
    
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	/*
		解禁用户账号
	*/ 
  public function activateUser($username) {
    $url = self::url()."/users/$username/activate";
    $result = Util_Curl::post($url, $data, array(
        'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
        'Authorization' => 'Bearer '.$this->token
    ));
    if ($result['code'] == 200) {
        return array('status' => 'ok', 'msg' => '', 'result' => $result);
    } else {
        $this->refreshToken();
        return array('status' => 'false', 'msg' => '', 'result' => $result);
    }
  }

	/*
		禁用用户账号
	*/ 
  public function deactivateUser($username) {
    $url = self::url()."/users/$username/deactivate";
    $result = Util_Curl::post($url, $data, array(
        'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
        'Authorization' => 'Bearer '.$this->token
    ));
    if ($result['code'] == 200) {
        return array('status' => 'ok', 'msg' => '', 'result' => $result);
    } else {
        $this->refreshToken();
        return array('status' => 'false', 'msg' => '', 'result' => $result);
    }
  }

	
	/*
		发送一条文本消息
	*/ 
  public function sendTxtMsg($from, $to, $msg, $ext = array()) {
    if (empty($to)) {
      return array('status' => 'error', 'msg' => 'invalid parameter');
    }

    $url = self::url()."/messages";
    $data = array(
      'target_type' => 'users',
      'target' => array($to),
      'msg' => array('type' => 'txt', 'msg' => $msg),
    );
    if (!empty($from)) {
      $data['from'] = $from;
    }
    if (!empty($ext)) {
      $data['ext'] = $ext;
    }

    $result = Util_Curl::post($url, $data, array(
        'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
        'Authorization' => 'Bearer '.$this->token));
    if ($result['code'] = 200) {
      $result = json_decode($result['content']);
      if (empty($result->error)) {
        return array('status' => 'ok', 'msg' => '', 'result' => $result);
      }
      return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
    } else {
      $this->refreshToken();
    }

    return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
  }
	/*
		强制用户下线
	*/ 
	public function disconnectUser($username){
        $url = self::url()."/users/".$username."/disconnect";
        $data=array();
        $result = Util_Curl::get($url, array(), array(
            'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
            'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] == 200) {
            $result = json_decode($result['content']);
            if (empty($result->error)) {
                return array('status' => 'ok', 'msg' => '', 'result' => $result);
            }
            return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
            $this->refreshToken();
        }
    
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	//--------------------------------------------------------上传下载
	/*
		上传图片或文件
	*/
	public function uploadFile($filePath){
        $filePathurl = self::url()."/chatfiles";
        //return array('url' => $url, 'filePath'=>$filePath);
        Logger::info(__FILE__, __CLASS__, __LINE__, $filePathurl);
        Logger::info(__FILE__, __CLASS__, __LINE__, $filePath);
		//$file=file_get_contents($filePath);
        $data=array("file"=> '@' . $filePath);
        $result = Util_Curl::upload_curl_mediafile($filePathurl, $data, array(
            //'Content-Type' => 'multipart/form-data',
            'restrict-access' => 'true',
            'Authorization' => 'Bearer '.$this->token));

        return $result;
	}
	/*
		下载文件或图片
	*/
	public function downloadFile($uuid,$shareSecret,$ext)
	{
		$url = $this->url . 'chatfiles/' . $uuid;
		$header = array("share-secret:" . $shareSecret, "Accept:application/octet-stream", $this->getToken(),);

		if ($ext=="png") {
			$result=$this->postCurl($url,'',$header,'GET');
		}else {
			$result = $this->getFile($url);
		}
		$filename = md5(time().mt_rand(10, 99)).".".$ext; //新图片名称
		if(!file_exists("resource/down")){
			mkdir("resource/down/");
		}

		$file = @fopen("resource/down/".$filename,"w+");//打开文件准备写入
		@fwrite($file,$result);//写入
		fclose($file);//关闭
		return $filename;
		
	}

	public function getFile($url){
		set_time_limit(0); // unlimited max execution time

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_TIMEOUT, 600); //max 10 minutes
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
	/*
		下载图片缩略图
	*/
	public function downloadThumbnail($uuid,$shareSecret){
		$url=$this->url.'chatfiles/'.$uuid;
		$header = array("share-secret:".$shareSecret,"Accept:application/octet-stream",$this->getToken(),"thumbnail:true");
		$result=$this->postCurl($url,'',$header,'GET');
		$filename = md5(time().mt_rand(10, 99))."th.png"; //新图片名称
		if(!file_exists("resource/down")){
			//mkdir("../image/down");
			mkdirs("resource/down/");
		}
		
		$file = @fopen("resource/down/".$filename,"w+");//打开文件准备写入
		@fwrite($file,$result);//写入
		fclose($file);//关闭
		return $filename;
	}
	 
	
	
	//--------------------------------------------------------发送消息
	/*
		发送文本消息
	*/
	public function sendText($from="admin",$target_type,$target,$content,$ext){
        $to = $target;
        if (empty($to)) {
          return array('status' => 'error', 'msg' => 'invalid parameter');
        }
    
        $url = self::url()."/messages";
        /* target_type : users , chatgroups , chatrooms */
        $data = array(
          'target_type' => $target_type,
          'target' => array($to),
          'msg' => array('type' => 'txt', 'msg' => $content),
        );
        if (!empty($from)) {
          $data['from'] = $from;
        }
        if (!empty($ext)) {
          $data['ext'] = $ext;
        }
    
        $result = Util_Curl::post($url, $data, array(
            'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
            'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] = 200) {
          $result = json_decode($result['content']);
          if (empty($result->error)) {
            return array('status' => 'ok', 'msg' => '', 'result' => $result);
          }
          return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
          $this->refreshToken();
        }
    
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	/*
		发送透传消息
	*/
	public function sendCmd($from="admin",$target_type,$target,$action,$ext){
        $url = self::url()."/messages";

        /* target_type : users , chatgroups , chatrooms */
        $data = array(
          'target_type' => $target_type,
          'target' => array($target),
          'msg' => array('type' => 'cmd', 'action' => $action),
        );
        if (!empty($from)) {
          $data['from'] = $from;
        }
        if (!empty($ext)) {
          $data['ext'] = $ext;
        }
    
        $result = Util_Curl::post($url, $data, array(
            'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
            'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] = 200) {
          $result = json_decode($result['content']);
          if (empty($result->error)) {
            return array('status' => 'ok', 'msg' => '', 'result' => $result);
          }
          return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
          $this->refreshToken();
        }
    
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	/*
		发图片消息
	*/ 
	public function sendImage($filePath, $from="admin" , $target_type, $target, $filename, $ext){
		$result = self::uploadFile($filePath);
        //Logger::info(__FILE__, __CLASS__, __LINE__, var_export($result, true));
		$uri = $result['uri'];
		$uuid = $result['entities'][0]['uuid'];
		$shareSecret = $result['entities'][0]['share-secret'];

        $url = self::url()."/messages";

		$options['type'] = "img";
		$options['url'] = $uri .'/' . $uuid;
		$options['filename'] = $filename;
		$options['secret'] = $shareSecret;
		$options['size']=array(
			"width" => 480,
			"height" => 720
		);
        //Logger::info(__FILE__, __CLASS__, __LINE__, var_export($options, true));
        /* target_type : users , chatgroups , chatrooms */
        $data = array(
          'target_type' => $target_type,
          'target' => array($target),
          'msg' => $options,
        );
        if (!empty($from)) {
          $data['from'] = $from;
        }
        if (!empty($ext)) {
          $data['ext'] = $ext;
        }
    
        $result = Util_Curl::post($url, $data, array(
            'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
            'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] = 200) {
          $result = json_decode($result['content']);
          if (empty($result->error)) {
            return array('status' => 'ok', 'msg' => '', 'result' => $result);
          }
          return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
          $this->refreshToken();
        }
    
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	/*
		发语音消息
	*/
	public function sendAudio($filePath, $from="admin", $target_type, $target, $filename, $length, $ext){
		$result = self::uploadFile($filePath);
		$uri = $result['uri'];
		$uuid = $result['entities'][0]['uuid'];
		$shareSecret = $result['entities'][0]['share-secret'];
		
        $url = self::url()."/messages";

		$options['type']="audio";
		$options['url']=$uri.'/'.$uuid;
		$options['filename']=$filename;
		$options['length']=$length;
		$options['secret']=$shareSecret;


        /* target_type : users , chatgroups , chatrooms */
        $data = array(
          'target_type' => $target_type,
          'target' => array($target),
          'msg' => $options,
        );
        if (!empty($from)) {
          $data['from'] = $from;
        }
        if (!empty($ext)) {
          $data['ext'] = $ext;
        }
    
        $result = Util_Curl::post($url, $data, array(
            'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
            'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] = 200) {
          $result = json_decode($result['content']);
          if (empty($result->error)) {
            return array('status' => 'ok', 'msg' => '', 'result' => $result);
          }
          return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
          $this->refreshToken();
        }
    
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
    }

	/*
		发视频消息
	*/
	public function sendVedio($filePath, $from = "admin", $target_type, $target, $filename, $length, $thumb, $thumb_secret, $ext){
		$result = self::uploadFile($filePath);
		$uri=$result['uri'];
		$uuid=$result['entities'][0]['uuid'];
		$shareSecret=$result['entities'][0]['share-secret'];
		
        $url = self::url()."/messages";
		
		$options['type']="video";
		$options['url']=$uri.'/'.$uuid;
		$options['filename']=$filename;
		$options['thumb']=$thumb;
		$options['length']=$length;
		$options['secret']=$shareSecret;
		$options['thumb_secret']=$thumb_secret;
		
        /* target_type : users , chatgroups , chatrooms */
        $data = array(
          'target_type' => $target_type,
          'target' => array($target),
          'msg' => $options,
        );
        if (!empty($from)) {
          $data['from'] = $from;
        }
        if (!empty($ext)) {
          $data['ext'] = $ext;
        }
    
        $result = Util_Curl::post($url, $data, array(
            'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
            'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] = 200) {
          $result = json_decode($result['content']);
          if (empty($result->error)) {
            return array('status' => 'ok', 'msg' => '', 'result' => $result);
          }
          return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
          $this->refreshToken();
        }
    
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	/*
	发文件消息
	*/
	public function sendFile($filePath , $from="admin", $target_type, $target, $filename, $length, $ext){
		$result = self::uploadFile($filePath);
		$uri=$result['uri'];
		$uuid=$result['entities'][0]['uuid'];
		$shareSecret=$result['entities'][0]['share-secret'];
		
        $url = self::url()."/messages";

		$options['type']="file";
		$options['url']=$uri.'/'.$uuid;
		$options['filename']=$filename;
		$options['length']=$length;
		$options['secret']=$shareSecret;

        /* target_type : users , chatgroups , chatrooms */
        $data = array(
          'target_type' => $target_type,
          'target' => array($target),
          'msg' => $options,
        );
        if (!empty($from)) {
          $data['from'] = $from;
        }
        if (!empty($ext)) {
          $data['ext'] = $ext;
        }
    
        $result = Util_Curl::post($url, $data, array(
            'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
            'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] = 200) {
          $result = json_decode($result['content']);
          if (empty($result->error)) {
            return array('status' => 'ok', 'msg' => '', 'result' => $result);
          }
          return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
          $this->refreshToken();
        }
    
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	//-------------------------------------------------------------群组操作
	
	/*
		获取app中的所有群组----不分页
	*/
	public function getGroups($limit=0){
		if(!empty($limit)){
            $url = self::url().'/chatgroups?limit='.$limit;
		}else{
            $url = self::url().'/chatgroups';
		}
        //return array('url'=>$url);
	    $data = array();	
        $result = Util_Curl::get($url, $data, array(
                              'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
                              'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] = 200) {
          $result = json_decode($result['content']);
          if (empty($result->error)) {
            return array('status' => 'ok', 'msg' => '', 'result' => $result);
          }
          return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
          $this->refreshToken();
        }
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	/*
		获取app中的所有群组---分页
	*/
	public function getGroupsForPage($limit=0,$cursor=''){
        $url = self::url().'/chatgroups?limit='.$limit.'&cursor='.$cursor;
	    $data = array();	
        $result = Util_Curl::post($url, $data, array(
                              'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
                              'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] = 200) {
          $result = json_decode($result['content']);
          if (empty($result->error)) {
            return array('status' => 'ok', 'msg' => '', 'result' => $result);
          } 
          return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
          $this->refreshToken();
        }
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	/*
		获取一个或多个群组的详情
	*/
	public function getGroupDetail($group_ids){
		$g_ids=implode(',',$group_ids);
        $url = self::url().'/chatgroups/'.$g_ids;
        //return array('url'=>$url);
	    $data = array();	
        $result = Util_Curl::get($url, $data, array(
                              'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
                              'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] = 200) {
          $result = json_decode($result['content']);
          if (empty($result->error)) {
            return array('status' => 'ok', 'msg' => '', 'result' => $result);
          }
          return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
          $this->refreshToken();
        }
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	/*
		创建一个群组
	*/
	public function createGroup($data){
        $url = self::url().'/chatgroups';
/*
	    $data = array();	
        $data['groupname'] = "group001";
        $data['desc'] = "this is a love group";
        $data['public'] = true;
        $data['owner'] = "zhangsan";
        $data['members']=Array("tony");
*/
        $result = Util_Curl::post($url, $data, array(
                              'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
                              'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] = 200) {
          $result = json_decode($result['content']);
          if (empty($result->error)) {
            return array('status' => 'ok', 'msg' => '', 'result' => $result);
          }
          return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
          $this->refreshToken();
        }
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	/*
		修改群组信息
	*/
	public function modifyGroupInfo($group_id,$data){
        $url = self::url().'/chatgroups/'.$group_id;
        //return array('url'=>$url);
/*
        $group_id="124113058216804760";
        $data['groupname']="group002";
        $data['description']="修改群描述";
        $data['maxusers']=300;
*/
        $result = Util_Curl::put1($url, $data, array(
                              'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
                              'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] = 200) {
          $result = json_decode($result['content']);
          if (empty($result->error)) {
            return array('status' => 'ok', 'msg' => '', 'result' => $result);
          }
          return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
          $this->refreshToken();
        }
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	/*
		删除群组
	*/
	public function deleteGroup($group_id){
        $url = self::url().'/chatgroups/'.$group_id;
        $data = array();
        $result = Util_Curl::delete1($url, $data, array(
                              'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
                              'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] = 200) {
          $result = json_decode($result['content']);
          if (empty($result->error)) {
            return array('status' => 'ok', 'msg' => '', 'result' => $result);
          }
          return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
          $this->refreshToken();
        }
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	/*
		获取群组中的成员
	*/
	public function getGroupUsers($group_id){
        $url = self::url().'/chatgroups/'.$group_id.'/users';	
        $data = array();
        $result = Util_Curl::get($url, $data, array(
                              'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
                              'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] = 200) {
          $result = json_decode($result['content']);
          if (empty($result->error)) {
            return array('status' => 'ok', 'msg' => '', 'result' => $result);
          }
          return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
          $this->refreshToken();
        }
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	/*
		群组单个加人
	*/
	public function addGroupMember($group_id,$username){
        $url = self::url().'/chatgroups/'.$group_id.'/users/'.$username;
        $data = array();
        $result = Util_Curl::post($url, $data, array(
                              'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
                              'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] = 200) {
          $result = json_decode($result['content']);
          if (empty($result->error)) {
            return array('status' => 'ok', 'msg' => '', 'result' => $result);
          }
          return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
          $this->refreshToken();
        }
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	/*
		群组批量加人
	*/
	public function addGroupMembers($group_id,$data){
        $url = self::url().'/chatgroups/'.$group_id.'/users';
/*
        $data = array();
        $data['usernames']=array("lisi","wangwu");
*/
        $result = Util_Curl::post($url, $data, array(
                              'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
                              'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] = 200) {
          $result = json_decode($result['content']);
          if (empty($result->error)) {
            return array('status' => 'ok', 'msg' => '', 'result' => $result);
          }
          return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
          $this->refreshToken();
        }
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	/*
		群组单个减人
	*/
	public function deleteGroupMember($group_id,$username){
        $url = self::url().'/chatgroups/'.$group_id.'/users/'.$username;
        $result = Util_Curl::delete1($url, $data, array(
                              'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
                              'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] = 200) {
          $result = json_decode($result['content']);
          if (empty($result->error)) {
            return array('status' => 'ok', 'msg' => '', 'result' => $result);
          }
          return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
          $this->refreshToken();
        }
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	/*
		群组批量减人
	*/
	public function deleteGroupMembers($group_id,$usernames){
        $url = self::url().'/chatgroups/'.$group_id.'/users/'.$usernames;
        $result = Util_Curl::delete1($url, $data, array(
                              'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
                              'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] = 200) {
          $result = json_decode($result['content']);
          if (empty($result->error)) {
            return array('status' => 'ok', 'msg' => '', 'result' => $result);
          }
          return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
          $this->refreshToken();
        }
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	/*
		获取一个用户参与的所有群组
	*/
	public function getGroupsForUser($username){
        $url = self::url().'/users/'.$username.'/joined_chatgroups';
        $data = array();
        $result = Util_Curl::get($url, $data, array(
                              'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
                              'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] = 200) {
          $result = json_decode($result['content']);
          if (empty($result->error)) {
            return array('status' => 'ok', 'msg' => '', 'result' => $result);
          }
          return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
          $this->refreshToken();
        }
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	/*
		群组转让
	*/
	public function changeGroupOwner($group_id,$data){
        $url = self::url().'/chatgroups/'.$group_id;
        $result = Util_Curl::post($url, $data, array(
                              'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
                              'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] = 200) {
          $result = json_decode($result['content']);
          if (empty($result->error)) {
            return array('status' => 'ok', 'msg' => '', 'result' => $result);
          }
          return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
          $this->refreshToken();
        }
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	/*
		查询一个群组黑名单用户名列表
	*/
	public function getGroupBlackList($group_id){
        $url = self::url().'/chatgroups/'.$group_id.'/blocks/users';
        $result = Util_Curl::get($url, $data, array(
                              'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
                              'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] = 200) {
          $result = json_decode($result['content']);
          if (empty($result->error)) {
            return array('status' => 'ok', 'msg' => '', 'result' => $result);
          }
          return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
          $this->refreshToken();
        }
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	/*
		群组黑名单单个加人
	*/
	public function addGroupBlackMember($group_id,$username){
        $url = self::url().'/chatgroups/'.$group_id.'/blocks/users/'.$username;
        $data = array();
        $result = Util_Curl::get($url, $data, array(
                              'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
                              'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] = 200) {
          $result = json_decode($result['content']);
          if (empty($result->error)) {
            return array('status' => 'ok', 'msg' => '', 'result' => $result);
          }
          return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
          $this->refreshToken();
        }
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	/*
		群组黑名单批量加人
	*/
	public function addGroupBlackMembers($group_id,$data){
        $url = self::url().'/chatgroups/'.$group_id.'/blocks/users';
        $result = Util_Curl::post($url, $data, array(
                              'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
                              'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] = 200) {
          $result = json_decode($result['content']);
          if (empty($result->error)) {
            return array('status' => 'ok', 'msg' => '', 'result' => $result);
          }
          return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
          $this->refreshToken();
        }
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	/*
		群组黑名单单个减人
	*/
	public function deleteGroupBlackMember($group_id,$username){
        $url = self::url().'/chatgroups/'.$group_id.'/blocks/users/'.$username;
        $data = array();
        $result = Util_Curl::delete1($url, $data, array(
                              'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
                              'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] = 200) {
          $result = json_decode($result['content']);
          if (empty($result->error)) {
            return array('status' => 'ok', 'msg' => '', 'result' => $result);
          }
          return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
          $this->refreshToken();
        }
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	/*
		群组黑名单批量减人
	*/
	public function deleteGroupBlackMembers($group_id,$data){
        $url = self::url().'/chatgroups/'.$group_id.'/blocks/users';
        $result = Util_Curl::delete1($url, $data, array(
                              'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
                              'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] = 200) {
          $result = json_decode($result['content']);
          if (empty($result->error)) {
            return array('status' => 'ok', 'msg' => '', 'result' => $result);
          }
          return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
          $this->refreshToken();
        }
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	//-------------------------------------------------------------聊天室操作
	/*
		创建聊天室
	*/
	public function createChatRoom($data){
        $url = self::url().'/chatrooms';
        $result = Util_Curl::post($url, $data, array(
                              'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
                              'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] = 200) {
          $result = json_decode($result['content']);
          if (empty($result->error)) {
            return array('status' => 'ok', 'msg' => '', 'result' => $result);
          }
          return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
          $this->refreshToken();
        }
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	/*
		修改聊天室信息
	*/
	public function modifyChatRoom($chatroom_id,$data){
        $url = self::url().'/chatrooms/'.$chatroom_id;
        $result = Util_Curl::post($url, $data, array(
                              'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
                              'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] = 200) {
          $result = json_decode($result['content']);
          if (empty($result->error)) {
            return array('status' => 'ok', 'msg' => '', 'result' => $result);
          }
          return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
          $this->refreshToken();
        }
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	/*
		删除聊天室
	*/
	public function deleteChatRoom($chatroom_id){
        $url = self::url().'/chatrooms/'.$chatroom_id;
        $data = array();
        $result = Util_Curl::delete1($url, $data, array(
                              'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
                              'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] = 200) {
          $result = json_decode($result['content']);
          if (empty($result->error)) {
            return array('status' => 'ok', 'msg' => '', 'result' => $result);
          }
          return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
          $this->refreshToken();
        }
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	/*
		获取app中所有的聊天室
	*/
	public function getChatRooms(){
        $url = self::url().'/chatrooms';
        $data = array();
        $result = Util_Curl::get($url, $data, array(
                              'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
                              'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] = 200) {
          $result = json_decode($result['content']);
          if (empty($result->error)) {
            return array('status' => 'ok', 'msg' => '', 'result' => $result);
          }
          return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
          $this->refreshToken();
        }
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	
	/*
		获取一个聊天室的详情
	*/
	public function getChatRoomDetail($chatroom_id){
        $url = self::url().'/chatrooms/'.$chatroom_id;
        $data = array();
        $result = Util_Curl::get($url, $data, array(
                              'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
                              'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] = 200) {
          $result = json_decode($result['content']);
          if (empty($result->error)) {
            return array('status' => 'ok', 'msg' => '', 'result' => $result);
          }
          return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
          $this->refreshToken();
        }
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	/*
		获取一个用户加入的所有聊天室
	*/
	public function getChatRoomJoined($username){
        $url = self::url().'/users/'.$username.'/joined_chatrooms';
        $data = array();
        $result = Util_Curl::get($url, $data, array(
                              'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
                              'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] = 200) {
          $result = json_decode($result['content']);
          if (empty($result->error)) {
            return array('status' => 'ok', 'msg' => '', 'result' => $result);
          }
          return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
          $this->refreshToken();
        }
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	/*
		聊天室单个成员添加
	*/
	public function addChatRoomMember($chatroom_id,$username){
        $url = self::url().'/chatrooms/'.$chatroom_id.'/users/'.$username;
        $data = array();
        $result = Util_Curl::post($url, $data, array(
                              'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
                              'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] = 200) {
          $result = json_decode($result['content']);
          if (empty($result->error)) {
            return array('status' => 'ok', 'msg' => '', 'result' => $result);
          }
          return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
          $this->refreshToken();
        }
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	/*
		聊天室批量成员添加
	*/
	public function addChatRoomMembers($chatroom_id,$data){
        $url = self::url().'/chatrooms/'.$chatroom_id.'/users';
        $result = Util_Curl::post($url, $data, array(
                              'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
                              'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] = 200) {
          $result = json_decode($result['content']);
          if (empty($result->error)) {
            return array('status' => 'ok', 'msg' => '', 'result' => $result);
          }
          return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
          $this->refreshToken();
        }
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	/*
		聊天室单个成员删除
	*/
	public function deleteChatRoomMember($chatroom_id,$username){
        $url = self::url().'/chatrooms/'.$chatroom_id.'/users/'.$username;
        $data = array();
        $result = Util_Curl::delete1($url, $data, array(
                              'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
                              'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] = 200) {
          $result = json_decode($result['content']);
          if (empty($result->error)) {
            return array('status' => 'ok', 'msg' => '', 'result' => $result);
          }
          return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
          $this->refreshToken();
        }
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	/*
		聊天室批量成员删除
	*/
	public function deleteChatRoomMembers($chatroom_id,$usernames){
        $url = self::url().'/chatrooms/'.$chatroom_id.'/users/'.$usernames;
        $data = array();
        $result = Util_Curl::delete1($url, $data, array(
                              'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
                              'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] = 200) {
          $result = json_decode($result['content']);
          if (empty($result->error)) {
            return array('status' => 'ok', 'msg' => '', 'result' => $result);
          }
          return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
          $this->refreshToken();
        }
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	//-------------------------------------------------------------聊天记录
	
	/*
		导出聊天记录----不分页
	*/
	public function getChatRecord($ql){
		if(!empty($ql)){
            $url = self::url().'/chatmessages?ql='.$ql;
		}else{
            $url = self::url().'/chatmessages';
		}
        $data = array();
        $result = Util_Curl::get($url, $data, array(
                              'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
                              'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] = 200) {
          $result = json_decode($result['content']);
          if (empty($result->error)) {
            return array('status' => 'ok', 'msg' => '', 'result' => $result);
          }
          return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
          $this->refreshToken();
        }
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
	}
	/*
		导出聊天记录---分页
	*/
	public function getChatRecordForPage($ql,$limit=0,$cursor){
		if(!empty($ql)){
            $url = self::url().'/chatmessages?ql='.$ql.'&limit='.$limit.'&cursor='.$cursor;
		}
        $data = array();
        $result = Util_Curl::get($url, $data, array(
                              'Content-Type' => Util_Curl::JSON_CONTENT_TYPE,
                              'Authorization' => 'Bearer '.$this->token));
        if ($result['code'] = 200) {
          $result = json_decode($result['content']);
          if (empty($result->error)) {
            return array('status' => 'ok', 'msg' => '', 'result' => $result);
          }
          return array('status' => 'failed', 'msg' => $result->error_description, 'result' => $result);
        } else {
          $this->refreshToken();
        }
        return array('status' => 'failed', 'msg' => 'curl error code:'.$result['code'], 'result' => $result['content']);
/*
		$cursor=isset ( $result["cursor"] ) ? $result["cursor"] : '-1';
		$this->writeCursor("chatfile.txt",$cursor);
		//var_dump($GLOBALS['cursor'].'00000000000000');
		return $result;
*/
	}
	

    public static function url()
    {
      return APF::get_instance()->get_config('easemob_chat_host').APF::get_instance()->get_config('easemob_org_name')."/".APF::get_instance()->get_config('easemob_app_name');
    }


    public static function fetchTokenFromEasemob() {
        $url = self::url()."/token";

        $data = array(
        'grant_type' => 'client_credentials',
        'client_id' => APF::get_instance()->get_config('easemob_client_id'),
        'client_secret' => APF::get_instance()->get_config('easemob_client_secret'),
        );

        $result = Util_Curl::post($url, $data, array('Content-Type' => Util_Curl::JSON_CONTENT_TYPE));
        if ($result['code'] = 200) {
          return json_decode($result['content']);
        }
        return false;
    }

    public static function getTokenFromDB()
    {
      $kvStore = new Dao_Keyvalue();
      $v = $kvStore->popValue(self::TOKEN_KEY);
      if ($v) {
        return $v['value'];
      }
  
      return false;
    }
  
    public static function saveTokenToDB($token, $expireIn)
    {
      $kvStore = new Dao_Keyvalue();
      if ($expireIn > 7*24*60*60) {
        $expireIn = 120*60;
      }
      $kvStore->pushValue(self::TOKEN_KEY, $token, $expireIn);
    }
  
    public function refreshToken() {
      $result = self::fetchTokenFromEasemob();
      if ($result) {
        $this->token = $result->access_token;
        self::saveTokenToDB($result->access_token, $result->expires_in);
      }
    }
  
    private function init() {
      // init token
      $token = $this->getTokenFromDB();
      if ($token) {
        $this->token = $token;
      } else {
        $this->refreshToken();
      }
      if (empty($this->token)) return false;
  
      return true;
    }

}
