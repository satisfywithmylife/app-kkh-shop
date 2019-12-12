<?php
apf_require_class("APF_DB_Factory");

class Dao_Doctor_Info {

	private $pdo;

	public function __construct() {
		$this->pdo = APF_DB_Factory::get_instance()->get_pdo("master");
	}

        public function get_practice_data($hd_kkid, $d_kkid, $h_kkid)
        {
            if(empty($hd_kkid)){
                return array();
            }
    
            $sql = "select d_kkid kkid, doctor name, job_title, degree, photo, hospital, department, h_kkid, hd_kkid, created, update_date, r_score, reg_num_int, pat_num_int, loc_code, clinic_type, price from t_practice_points where hd_kkid=? and d_kkid=? and h_kkid=? and status=1 order by r_score desc, reg_num_int desc limit 1;";
    
            #Logger::info(__FILE__, __CLASS__, __LINE__, var_export($sql, true));
            #Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array("$hd_kkid", "$d_kkid", "$h_kkid"));
            $j = $stmt->fetch();
            if(isset($j['photo']) && !empty($j['photo'])){
              $j['photo'] = IMG_CDN_DOCTOR . $j['photo'] . "/" . "headpic.jpg";
            }
            $de = self::get_doctor_data($d_kkid);
            if(isset($de['expertise'])){
              $j['expertise'] = $de['expertise'];
              $j['tags']      = $de['tags'];
              $j['intro']     = $de['intro'];
            }
            return $j;
        }
    
        private function get_doctor_data($kkid)
        {
            $sql = "select expertise, intro, tags from t_doctor where kkid = ?  and status=1 limit 1;";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array($kkid));
            return $stmt->fetch();
        }



}
