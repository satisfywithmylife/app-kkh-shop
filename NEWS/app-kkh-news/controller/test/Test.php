<?php
apf_require_class("APF_Controller");
class Test_TestController extends APF_Controller{

    public function handle_request(){

        $req = APF::get_instance()->get_request();
        $param_arr = $req->get_parameters();
        Logger::info(__FILE__, __CLASS__, __LINE__, var_export($param_arr, true));

	echo 'success';
    }

}
