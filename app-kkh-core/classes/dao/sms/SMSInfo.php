<?php
apf_require_class("APF_DB_Factory");

class Dao_Sms_SMSInfo {

	private $pdo;
	private $go_pdo;

	public function __construct() {
		        $this->pdo = APF_DB_Factory::get_instance()->get_pdo("master");
		        $this->go_pdo = APF_DB_Factory::get_instance()->get_pdo("groupon_master");
	}

	public function dao_send_sms_notify($data) {
                Logger::info(__FILE__, __CLASS__, __LINE__, var_export($data, true));
                $jd = self::dao_send_sms_notify_yunpian($data);
                Logger::info(__FILE__, __CLASS__, __LINE__, $jd );
		        $sql = "insert into t_verifysms (id, mobile, v_code, datei, u_kkid, status, created) values(:id, :mobile, :v_code, :datei, :u_kkid, :status, :created);";
		        $stmt = $this->pdo->prepare($sql);
	            $stmt->execute($data);
		        return $jd;
	}

	public function send_sms_channel($data){
				return self::sms_channel($data);
	}

	public function set_sms_tpl($data){
				Logger::info(__FILE__, __CLASS__, __LINE__, var_export($data, true));
				if(!$data) return false;

				$tpl_content = '【青苹果健康】'.$data['content'];
				$notify_type = 0;
			
				$apikey = APF::get_instance()->get_config("sms_provider_market_apikey");

				Logger::info(__FILE__, __CLASS__, __LINE__, 'apikey :'.$apikey);
                $ch = curl_init();

                /* 设置验证方式 */

                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept:text/plain;charset=utf-8', 'Content-Type:application/x-www-form-urlencoded','charset=utf-8'));

                /* 设置返回结果为流 */
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                /* 设置超时时间*/
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);

                /* 设置通信方式 */
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);


                $sms = array(
					'tpl_content' => $tpl_content,
                    'apikey' => $apikey,
					'notify_type' => $notify_type,
                );
                //print_r($sms);
                Logger::info(__FILE__, __CLASS__, __LINE__, var_export($sms, true));

                //print_r ($data);
                $json_data = '';
				$provider = 'https://sms.yunpian.com/v1/tpl/add.json';
            	curl_setopt ($ch, CURLOPT_URL, $provider);
            	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($sms));
            	$json_data = curl_exec($ch);
                /* */
                return $json_data;


	}

    public function sms_channel($data) {

                //echo "sms: ============\n";
                //print_r($data);
                /* */
                if(empty($data)){
                   return false;
                }

                $mobile = $data['mobile']; // 手机号
                $content = $data['content'];
				$tpl_id = $data['tpl_id']; // id
                $apikey = APF::get_instance()->get_config("sms_provider_market_apikey");
				
                $is_moblie = FALSE;
                if(strlen($mobile) == "11")
                {
                  //上面部分判断长度是不是11位 
                  if(preg_match("/1(3|4|5|7|8)[0-9]\d{8}/", $mobile, $matches)){
                    $is_moblie = TRUE;
                  }
                }


                $ch = curl_init();

                /* 设置验证方式 */

                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept:text/plain;charset=utf-8', 'Content-Type:application/x-www-form-urlencoded','charset=utf-8'));

                /* 设置返回结果为流 */
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                /* 设置超时时间*/
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);

                /* 设置通信方式 */
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);


                // 发送模板短信
                // 需要对value进行编码
                $tpl_id = $data['tpl_id'];
                $sms = array('tpl_id'=>$tpl_id,
                            'apikey'=>$apikey,
                            'tpl_value'=>('#content#').'='.urlencode($content),
                            'mobile'=>$mobile
                           );
                //print_r($sms);
                Logger::info(__FILE__, __CLASS__, __LINE__, var_export($sms, true));

                //print_r ($data);
                $json_data = '';
                if($is_moblie){
                   $json_data = self::sms_tpl_send($ch, $sms);
                }   
				return $json_data;	
	}
			

	public function groupon_send_sms_notify($data) {
                $jd = self::dao_send_sms_notify_yunpian_groupon($data);
                unset($data['name']);
                $sql = "insert into `s_sms_notification` (`id_message`, `kkid`, `id_group`, `g_kkid`, `m_kkid`, `id_customer`, `c_kkid`, `status`, `current_state`, `mobile_num`, `content`, `tpl_id`, `created_at`, `updated_at`) values(:id_message, replace(upper(uuid()),'-',''), :id_group, :g_kkid, :m_kkid, :id_customer, :c_kkid, :status, :current_state, :mobile_num, :content, :tpl_id, :created_at, now());";
		        $stmt = $this->go_pdo->prepare($sql);
	            $stmt->execute($data);
		        return $jd;
	}

	public function check_sms_tpl($data){
				if(!$data) return false;

				$tpl_id = $data['tpl_id'];
				
                $apikey = APF::get_instance()->get_config("sms_provider_market_apikey");

                Logger::info(__FILE__, __CLASS__, __LINE__, 'apikey :'.$apikey);
                $ch = curl_init();

                /* 设置验证方式 */

                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept:text/plain;charset=utf-8', 'Content-Type:application/x-www-form-urlencoded','charset=utf-8'));

                /* 设置返回结果为流 */
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                /* 设置超时时间*/
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);

                /* 设置通信方式 */
                curl_setopt($ch, CURLOPT_POST, 1); 
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);


                $sms = array(
                    'tpl_id' => $tpl_id,
                    'apikey' => $apikey,
                );  
                //print_r($sms);
                Logger::info(__FILE__, __CLASS__, __LINE__, var_export($sms, true));

                //print_r ($data);
                $json_data = ''; 
                $provider = 'https://sms.yunpian.com/v1/tpl/get.json';
                curl_setopt ($ch, CURLOPT_URL, $provider);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($sms));
                $json_data = curl_exec($ch);
                /* */
                return $json_data;

	}

	public function dao_send_sms_notify_yunpian_groupon($data) {
                /* */
                if(empty($data)){
                   return false;
                }
                $mobile = $data['mobile_num']; //手机号
                $content = $data['content'];
                $tpl_id = $data['tpl_id'];
                $m_kkid = $data['m_kkid'];
                $m_kkid = substr($m_kkid,0,8);
                $name  = $data['name'];


                $apikey = APF::get_instance()->get_config("sms_provider_market_apikey");
                $is_moblie = FALSE;
                if(strlen($mobile) == "11") 
                { 
                  //上面部分判断长度是不是11位 
                  if(preg_match("/1(3|4|5|7|8)[0-9]\d{8}/", $mobile, $matches)){
                    $is_moblie = TRUE;
                  }
                }
                $ch = curl_init();
                /* 设置验证方式 */
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept:text/plain;charset=utf-8', 'Content-Type:application/x-www-form-urlencoded','charset=utf-8'));
                /* 设置返回结果为流 */
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                /* 设置超时时间*/
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                /* 设置通信方式 */
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                // 发送模板短信
                // 需要对value进行编码
    /*
           array(
                '2129908' => '好友 #name# 加入了您的 #content# 拼团，团已准备就绪，再动动任督二脉发动更多好友就能开团等收货了。',
                '2129914' => '喜奔！您的 #content# 拼团已成团，订单号 #code# 为您奉上，我们将马上为您发货。上辈子一定拯救了银河系。',
                '2129922' => '您的 #content# 拼团已失败，系统将自动退款。曾经有一分感人的价格放在我面前，我没有好好珍惜，如果我再给你一个机会。',
                '2129928' => '5-4-3-2-1，您的 #content# 拼团 还没有成团，您的好友偷偷给你点了赞，却不买单！岂有此理，必须发动你的粉丝团了。',
                '2129904' => '哈喽，您的 #content# 拼团已付款成功，还差一步即可享受超低的拼团价，赶快邀请好友来参加吧，我只帮您预留24小时。',
                );


            $data = array(
                 'g_kkid' => '36CC3CC6EC5F11E7A5CC00163E0EC239',
                 'c_kkid' => '00B61A63B5A011E7B2AF00163E0EB924',
                 'current_state' => 2,
                 'mobile_num' => '18616851610',
                 'content' => '',
                 'tpl_id' => '2129904',
            );
    */
                if($tpl_id == '2129904' || $tpl_id == '2129928' || $tpl_id == '2129922' ){
                    $tpl_value = ('#content#').'='.urlencode($content);
                }
                else if($tpl_id == '2129914'){
                    $tpl_value = ('#content#').'='.urlencode($content).'&'.('#code#').'='.urlencode($m_kkid);
                }
                else if($tpl_id == '2129908'){
                    $tpl_value = ('#name#').'='.urlencode($name)  .'&'. ('#content#').'='.urlencode($content);
                }
                else{
                    $tpl_value = ('#content#').'='.urlencode($content);
                }
                $sms = array('tpl_id' => $tpl_id,
                      'tpl_value' => $tpl_value,
                      'apikey' => $apikey,
                      'mobile' => $mobile
                );
                Logger::info(__FILE__, __CLASS__, __LINE__, var_export($sms, true));
                
                //print_r ($data);
                $json_data = '';
                if($is_moblie){
                   $json_data = self::sms_tpl_send($ch, $sms);
                }
                /* */
                //echo "$json_data \n";
                return $json_data;
	}

	public function dao_send_sms_notify_mission_ticket($data) {

                //echo "sms: ============\n";
                //print_r($data);
                /* */
                if(empty($data)){
                   return false;
                }

                $mobile = $data['mobile']; // 手机号
                $ticket_id = $data['ticket_id']; // id
                $apikey = APF::get_instance()->get_config("sms_provider_market_apikey");

                $is_moblie = FALSE;
                if(strlen($mobile) == "11") 
                { 
                  //上面部分判断长度是不是11位 
                  if(preg_match("/1(3|4|5|7|8)[0-9]\d{8}/", $mobile, $matches)){
                    $is_moblie = TRUE;
                  }
                }
                
                
                $ch = curl_init();
                
                /* 设置验证方式 */
                
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept:text/plain;charset=utf-8', 'Content-Type:application/x-www-form-urlencoded','charset=utf-8'));
                
                /* 设置返回结果为流 */
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                
                /* 设置超时时间*/
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                
                /* 设置通信方式 */
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                
                
                // 发送模板短信
                // 需要对value进行编码
                $tpl_id = APF::get_instance()->get_config("sms_provider_tpl_mission_ticket");
                $sms = array('tpl_id'=>$tpl_id,
                            'apikey'=>$apikey,
                            'tpl_value'=>('#order_number#').'='.urlencode($ticket_id),
                            'mobile'=>$mobile
                           );
                //print_r($sms);
                Logger::info(__FILE__, __CLASS__, __LINE__, var_export($sms, true));
                
                //print_r ($data);
                $json_data = '';
                if($is_moblie){
                   $json_data = self::sms_tpl_send($ch, $sms);
                }
                /* */
                return $json_data;
	}
	public function dao_send_sms_notify_mission_succeed($data) {

                //echo "sms: ============\n";
                //print_r($data);
                /* */
                if(empty($data)){
                   return false;
                }

                $mobile = $data['mobile']; //手机号
                $apikey = APF::get_instance()->get_config("sms_provider_market_apikey");

                $is_moblie = FALSE;
                if(strlen($mobile) == "11") 
                { 
                  //上面部分判断长度是不是11位 
                  if(preg_match("/1(3|4|5|7|8)[0-9]\d{8}/", $mobile, $matches)){
                    $is_moblie = TRUE;
                  }
                }
                
                
                $ch = curl_init();
                
                /* 设置验证方式 */
                
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept:text/plain;charset=utf-8', 'Content-Type:application/x-www-form-urlencoded','charset=utf-8'));
                
                /* 设置返回结果为流 */
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                
                /* 设置超时时间*/
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                
                /* 设置通信方式 */
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                
                
                // 发送模板短信
                // 需要对value进行编码
                $tpl_id = APF::get_instance()->get_config("sms_provider_tpl_mission_succeed");
                $sms = array('tpl_id'=>$tpl_id,
                            'apikey'=>$apikey,
                            'mobile'=>$mobile
                           );
                //print_r($sms);
                Logger::info(__FILE__, __CLASS__, __LINE__, var_export($sms, true));
                
                //print_r ($data);
                $json_data = '';
                if($is_moblie){
                   $json_data = self::sms_tpl_send($ch, $sms);
                }
                /* */
                return $json_data;
	}

	public function dao_send_sms_notify_mission_failed($data) {

                //echo "sms: ============\n";
                //print_r($data);
                /* */
                if(empty($data)){
                   return false;
                }

                $mobile = $data['mobile']; //手机号
                $apikey = APF::get_instance()->get_config("sms_provider_market_apikey");

                $is_moblie = FALSE;
                if(strlen($mobile) == "11") 
                { 
                  //上面部分判断长度是不是11位 
                  if(preg_match("/1(3|4|5|7|8)[0-9]\d{8}/", $mobile, $matches)){
                    $is_moblie = TRUE;
                  }
                }
                
                
                $ch = curl_init();
                
                /* 设置验证方式 */
                
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept:text/plain;charset=utf-8', 'Content-Type:application/x-www-form-urlencoded','charset=utf-8'));
                
                /* 设置返回结果为流 */
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                
                /* 设置超时时间*/
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                
                /* 设置通信方式 */
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                
                
                // 发送模板短信
                // 需要对value进行编码
                $tpl_id = APF::get_instance()->get_config("sms_provider_tpl_mission_failed");
                $sms = array('tpl_id'=>$tpl_id,
                            'apikey'=>$apikey,
                            'mobile'=>$mobile
                           );
                //print_r($sms);
                Logger::info(__FILE__, __CLASS__, __LINE__, var_export($sms, true));
                
                //print_r ($data);
                $json_data = '';
                if($is_moblie){
                   $json_data = self::sms_tpl_send($ch, $sms);
                }
                /* */
                return $json_data;
	}

	public function dao_send_sms_notify_yunpian($data) {

                /* */
				Logger::info(__FILE__, __CLASS__, __LINE__, var_export($data, true));
				if(empty($data)){
                   return false;
                }

                $mobile = $data['mobile']; //手机号
                $captcha = $data['v_code'];
                $apikey = APF::get_instance()->get_config("sms_provider_captcha_apikey");

                $is_moblie = FALSE;
                if(strlen($mobile) == "11") 
                { 
                  //上面部分判断长度是不是11位 
                  if(preg_match("/1(3|4|5|7|8)[0-9]\d{8}/", $mobile, $matches)){
                    $is_moblie = TRUE;
                  }
                }
                
                
                $ch = curl_init();
                
                /* 设置验证方式 */
                
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept:text/plain;charset=utf-8', 'Content-Type:application/x-www-form-urlencoded','charset=utf-8'));
                
                /* 设置返回结果为流 */
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                
                /* 设置超时时间*/
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                
                /* 设置通信方式 */
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                
                
                // 发送模板短信
                // 需要对value进行编码
                $tpl_id = APF::get_instance()->get_config("sms_provider_tpl_id");
                $sms = array('tpl_id'=>$tpl_id,
                            'tpl_value'=>('#code#').'='.urlencode($captcha),
                            'apikey'=>$apikey,
                            'mobile'=>$mobile
                           );
                Logger::info(__FILE__, __CLASS__, __LINE__, var_export($sms, true));
                
                //print_r ($data);
                $json_data = '';
                if($is_moblie){
                   $json_data = self::sms_tpl_send($ch, $sms);
                }
                /* */
                return $json_data;
	}

        private function sms_tpl_send($ch, $data){
            $provider = APF::get_instance()->get_config("sms_provider_tpl_url");
            if(empty($provider)){
              return false;
            }
            Logger::info(__FILE__, __CLASS__, __LINE__, $provider);
            Logger::info(__FILE__, __CLASS__, __LINE__, var_export($data, true));
            curl_setopt ($ch, CURLOPT_URL, $provider);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            return curl_exec($ch);
        }

/*
####################################################################
Variables List
####################################################################
$id = "";
$mobile = "";
$v_code = "";
$datei = "";
$u_kkid = "";
$status = "";
$created = "";
$update_date = "";
####################################################################
Array Statement
####################################################################
$res = array(
    'id' => $id,
    'mobile' => $mobile,
    'v_code' => $v_code,
    'datei' => $datei,
    'u_kkid' => $u_kkid,
    'status' => $status,
    'created' => $created,
    'update_date' => $update_date
);
####################################################################
Insert Statement
####################################################################
insert into t_verifysms (id, mobile, v_code, datei, u_kkid, status, created, update_date) values(:id, :mobile, :v_code, :datei, :u_kkid, :status, :created, :update_date);
####################################################################
Update Statement
####################################################################
update t_verifysms set id = ?, mobile = ?, v_code = ?, datei = ?, u_kkid = ?, status = ?, created = ?, update_date = ? where id = ? ;
####################################################################
Select Statement
####################################################################
select id, mobile, v_code, datei, u_kkid, status, created, update_date from t_verifysms where id = ? ;
*/
}
?>
