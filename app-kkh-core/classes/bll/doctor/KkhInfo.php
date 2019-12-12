<?php

class  Bll_Doctor_KkhInfo {
	private $doctorInfoDao;

	public function __construct() {
		$this->doctorInfoDao = new Dao_Doctor_KkhInfo();
	}

        public function create_doctor($data) {
                if(empty($data)) return array();
                return $this->doctorInfoDao->create_doctor($data);
        }

        public function set_doctor($data) {
                if(empty($data)) return array();
                return $this->doctorInfoDao->set_doctor($data);
        }

        public function set_doctor_ease($data) {
                if(empty($data)) return array();
                return $this->doctorInfoDao->set_doctor_ease($data);
        }

        public function get_doctor($id) {
                if(empty($id)) return array();
                $res =  $this->doctorInfoDao->get_doctor($id);
                $res = self::get_doctor_job_title($res);
                return $res;
        }

        public function get_doctor_by_kkid($kkid) {
                if(empty($kkid)) return array();
                $res = $this->doctorInfoDao->get_doctor_by_kkid($kkid);
                $res = self::get_doctor_job_title($res);
                return $res;
        }

        public function get_doctor_by_wx_info($wx_openid, $wx_uniond) {
                if (empty($wx_openid) || empty($wx_unionid)) {
                    return array();
                }
                
                $res = $this->doctorInfoDao->get_doctor_by_wx_info($wx_openid, $wx_unionid);
                $res = self::get_doctor_job_title($res);
                return $res;
        }

        private function get_doctor_job_title($data) {
/*
*/
                if(empty($data)) return array();
                $title_medical = array('8CHF'=>'主任医师',
        '7ACH'=>'副主任医师',
        '6ATT'=>'主治医师',
        '5RES'=>'住院医师',
        '4QUA'=>'医师',
        '4TCP'=>'中医师',
        '1NUR'=>'护士',
        '2DIC'=>'主管营养师',
        '1DIE'=>'营养师',
        '2DIE'=>'营养师',
        '1PHT'=>'理疗师',
        '2PHR'=>'药剂师',
        '2TEC'=>'技师',
        '0OTH'=>'其他');
                if(isset($data['title_med']) && !empty($data['title_med'])){
                   $k = $data['title_med'];
                   $data['job_title'] = $title_medical[$k];
                }
                if(empty($data['job_title'])){
                   $data['job_title'] = '其他';
                }
                return $data;
        }

        public function get_doctor_favorite_product($doctor_id) {
                if(empty($doctor_id)) return array();
                $commodity_id = $this->doctorInfoDao->get_doctor_favorite_product($doctor_id);
                $bll_product = new Bll_Product_Info();
                $product = $bll_product->get_product($commodity_id);
                return $product;
        }

        public function get_doctor_specialty_product($doctor_id) {
                if(empty($doctor_id)) return array();
                $commodity_id = $this->doctorInfoDao->get_doctor_specialty_product($doctor_id);
                $bll_product = new Bll_Product_Info();
                $product = $bll_product->get_product($commodity_id);
                return $product;
        }

        public function get_doctor_rand_product() {
                $commodity_list = array(
                                       0 => '206',
                                       1 => '204',
                                       2 => '204',
                                       3 => '203',
                                       4 => '202',
                                       5 => '201',
                                       6 => '340',
                                       7 => '197',
                                       8 => '196',
                                       9 => '195',
                                       10 => '194',
                                     );
                $rand_num = mt_rand(1, 10);
                $commodity_id = $commodity_list[$rand_num];
                $bll_product = new Bll_Product_Info();
                $product = $bll_product->get_product($commodity_id);
                return $product;
        }

}
