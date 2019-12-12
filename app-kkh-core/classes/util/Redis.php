<?php
/**
 *redis connect
 */
class Util_Redis
{   
 
	public static function redis_server($name="default")
	{   
	     
	      $redis_config = APF::get_instance()->get_config('redis_servers','cache');
		  $redis       = new Redis();
		  $redis->connect($redis_config[$name]['host'],$redis_config[$name]['port']);
		  return $redis;
		         
    }

	public static  function del_cache($name)
	{
	    $redis = self::redis_server();
		$redis->del($name);
	}


}
