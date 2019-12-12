<?php
/**
 *@param 快递信息操作类
 *
 */
class Bll_Postage_Info
{
    private $postageInfoDao;
	public function __construct()
	{
	    $this->postageInfoDao = new Dao_Postage_Info();
	}
	//查询快递信息
	public function get_postage_info()
	{
	     return $this->postageInfoDao->get_postage_info();
	}


}
