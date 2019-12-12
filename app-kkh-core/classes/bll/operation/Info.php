<?php

class  Bll_Operation_Info {

	private $operationInfoDao;

	public function __construct() {
		$this->operationInfoDao = new Dao_Operation_Info();
	}

	public function add($data){
		if (!$data) return array();
		return $this->operationInfoDao->add($data);
	}
	
	public function name_edit($data){
		if (!$data) return array();
		return $this->operationInfoDao->name_edit($data);
	}

    public function img_edit($data){
        if (!$data) return array();
        return $this->operationInfoDao->img_edit($data);
    }
	
	public function del($id) {
		if (!$id) return array();
        return $this->operationInfoDao->del($id);
	}
	//修改 operation name and img
	public function edit_info($data)
	{
	   if (!$data) return array();
	   return $this->operationInfoDao->edit_info($data);
	}

	public function opreation_list(){
		return $this->operationInfoDao->operation_list();
	}
}
