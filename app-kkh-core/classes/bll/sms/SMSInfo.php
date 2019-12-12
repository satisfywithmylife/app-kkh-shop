<?php

class Bll_Sms_SMSInfo
{
    private $smsInfoDao;
	
    public function __construct()
    {
        $this->smsInfoDao = new Dao_Sms_SMSInfo();
    }

	public function send_sms_channel($data){
		return $this->smsInfoDao->send_sms_channel($data);
	}

    public function bll_send_sms_notify($info)
    {
        return $this->smsInfoDao->dao_send_sms_notify($info);
    }

    public function groupon_send_sms_notify($info)
    {
        return $this->smsInfoDao->groupon_send_sms_notify($info);
    }
	public function set_sms_tpl($data){
		if(!$data) return array();
		return $this->smsInfoDao->set_sms_tpl($data);
	}
	
	public function check_sms_tpl($data){
		if(!$data) return array();
		return $this->smsInfoDao->check_sms_tpl($data);
	}
}
