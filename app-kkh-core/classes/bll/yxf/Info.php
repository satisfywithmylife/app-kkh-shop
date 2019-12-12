<?php
/**
 * Created by PhpStorm.
 * User: HP
 * Date: 2018/3/20
 * Time: 20:00
 */

class Bll_Yxf_Info{
    private $yxfInfo;

    public function __construct()
    {
         $this->yxfInfo = new Dao_Yxf_Info();

    }

    public function get_yxf_info($id){
        if(empty($id)) return array();
        return $this->yxfInfo->get_info($id);
    }
    
    //添加一条测试数据
    public function yxf_insert($data){
       if(empty($data)) return array();
       
       return $this->yxfInfo->yxf_insert($data);
       
    }
    
    public function yxf_update($id,$data){
       if(empty($data)) return array();
       
	   return $this->yxfInfo->yxf_update($id,$data); 


   }



}
