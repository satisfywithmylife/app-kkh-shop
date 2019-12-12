<?php

class Weixin
{

    public function weixin_params($curr_url = '')
    {
        $ticket = $this->get_weixin_ticket();
        $noncestr = DOMAIN;//'touch.shop.kangkanghui.com';
        $timestamp = time();
        if(empty($curr_url)){
            $url = DOMAIN;//'http://touch.shop.kangkanghui.com/';
        }
        else{
            $url = $curr_url;
        }
        $weixin_params = array(
            'jsapi_ticket' => $ticket,
            'noncestr' => $noncestr,
            'timestamp' => $timestamp,
            'url' => $url,
        );
        Logger::info(__FILE__, __CLASS__, __LINE__, var_export($weixin_params, true));
        ksort($weixin_params);
        $build_str = array();
        foreach ($weixin_params as $k => $v) {
            $build_str[] = $k . '=' . $v;
        }
        $signature = sha1(join('&', $build_str));
        return array(
            'noncestr' => $noncestr,
            'timestamp' => $timestamp,
            'signature' => $signature,
            'url' => $url,
            'wx_appid' => WEIXIN_APP_ID,
        );
    }

    private function get_weixin_ticket()
    {
        $appid = WEIXIN_APP_ID;
        $appsecret = WEIXIN_APP_SECRET;

        $key = 'weixin_jsapi_' . $appid . '_' . $appsecret;
        $memcache = APF_Cache_Factory::get_instance()->get_memcache();
        $ticket = $memcache->get($key);
        if (empty($ticket)) {
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $appsecret;
            $return = $this->call($url);
            $arr = json_decode($return, true);
            $access_token = $arr['access_token'];
            $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=' . $access_token . '&type=jsapi';
            $return = $this->call($url);
            $arr = json_decode($return, true);
            $ticket = $arr['ticket'];
            $memcache->add($key, $ticket, 0, 7140);
        }
        return $ticket;
    }

    private function call($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($ch);
        return $resp;
    }
}
