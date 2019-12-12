<?php
apf_require_class("APF_DB_Factory");

class Dao_Doctor_KkhInfo {

	private $pdo;

	public function __construct() {
		$this->pdo = APF_DB_Factory::get_instance()->get_pdo("green_master");
	}

        public function create_doctor($data) {
                $sql = "insert into `doctor` (`id`, `user_type`, `sex`, `password`, `phone_num`, `real_name`, `header_pic`, `register_time`, `title_aca`, `title_med`, `specialty_id`, `feature`, `department_id`, `last_update_time`, `pwd_update_time`, `pwd_create_time`, `account_status`, `bio`, `device_token`, `push_user_id`, `push_channel_id`, `tools`, `hospital_id`, `dept_phone`, `referer`, `internal_remark`, `phone_fee`, `phone_fee_unit`, `registered`, `link_to_id`, `last_login_time`, `app_version`, `device_type`, `device_id`, `os`, `os_version`, `channel`, `is_login`, `last_verified_time`, `extra`, `wx_openid`, `auth_pic`, `sales_id`, `auth_pic_status`, `membership_level`, `continuous_login_day_count`, `user_token`, `update_date`, `kkid`, `ease_orgname`, `ease_appname`, `ease_groupid`) values(:id, :user_type, :sex, :password, :phone_num, :real_name, :header_pic, :register_time, :title_aca, :title_med, :specialty_id, :feature, :department_id, :last_update_time, :pwd_update_time, :pwd_create_time, :account_status, :bio, :device_token, :push_user_id, :push_channel_id, :tools, :hospital_id, :dept_phone, :referer, :internal_remark, :phone_fee, :phone_fee_unit, :registered, :link_to_id, :last_login_time, :app_version, :device_type, :device_id, :os, :os_version, :channel, :is_login, :last_verified_time, :extra, :wx_openid, :auth_pic, :sales_id, :auth_pic_status, :membership_level, :continuous_login_day_count, :user_token, :update_date, :kkid, :ease_orgname, :ease_appname, :ease_groupid);";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                $last_id = $this->pdo->lastInsertId();
                return $last_id;
        }

        public function set_doctor($data) {
                $sql = "update `doctor` set `user_type` = :user_type, `sex` = :sex, `password` = :password, `phone_num` = :phone_num, `real_name` = :real_name, `header_pic` = :header_pic, `register_time` = :register_time, `title_aca` = :title_aca, `title_med` = :title_med, `specialty_id` = :specialty_id, `feature` = :feature, `department_id` = :department_id, `last_update_time` = :last_update_time, `pwd_update_time` = :pwd_update_time, `pwd_create_time` = :pwd_create_time, `account_status` = :account_status, `bio` = :bio, `device_token` = :device_token, `push_user_id` = :push_user_id, `push_channel_id` = :push_channel_id, `tools` = :tools, `hospital_id` = :hospital_id, `dept_phone` = :dept_phone, `referer` = :referer, `internal_remark` = :internal_remark, `phone_fee` = :phone_fee, `phone_fee_unit` = :phone_fee_unit, `registered` = :registered, `link_to_id` = :link_to_id, `last_login_time` = :last_login_time, `app_version` = :app_version, `device_type` = :device_type, `device_id` = :device_id, `os` = :os, `os_version` = :os_version, `channel` = :channel, `is_login` = :is_login, `last_verified_time` = :last_verified_time, `extra` = :extra, `wx_openid` = :wx_openid, `auth_pic` = :auth_pic, `sales_id` = :sales_id, `auth_pic_status` = :auth_pic_status, `membership_level` = :membership_level, `continuous_login_day_count` = :continuous_login_day_count, `user_token` = :user_token, `update_date` = :update_date, `kkid` = :kkid, `ease_orgname` = :ease_orgname, `ease_appname` = :ease_appname, `ease_groupid` = :ease_groupid where `id` = :id ;";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                return $res;
        }

        public function set_doctor_ease($data) {
                $sql = "update `doctor` set `user_token` = :user_token, `kkid` = :kkid, `ease_orgname` = :ease_orgname, `ease_appname` = :ease_appname, `ease_groupid` = :ease_groupid where `id` = :id ;";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                return $res;
        }

        public function get_doctor($id) {
                $row = array();
                $sql = "select `id`, `user_type`, `sex`, `password`, `phone_num`, `real_name`, `header_pic`, `register_time`, `title_aca`, `title_med`, `specialty_id`, `feature`, `department_id`, `last_update_time`, `pwd_update_time`, `pwd_create_time`, `account_status`, `bio`, `device_token`, `push_user_id`, `push_channel_id`, `tools`, `hospital_id`, `dept_phone`, `referer`, `internal_remark`, `phone_fee`, `phone_fee_unit`, `registered`, `link_to_id`, `last_login_time`, `app_version`, `device_type`, `device_id`, `os`, `os_version`, `channel`, `is_login`, `last_verified_time`, `extra`, `wx_openid`, `auth_pic`, `sales_id`, `auth_pic_status`, `membership_level`, `continuous_login_day_count`, `user_token`, `update_date`, `kkid`, `ease_orgname`, `ease_appname`, `ease_groupid` from `doctor` where `id` = ? ;";
                Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$id"));
                $row = $stmt->fetch();
                if(empty($row)){
                   $row = array();
                }
                else{
                   $row['doctor_service'] = self::get_doctor_service_by_id($row['id']);
                   $row['doctor_getuiID'] = self::get_doctor_getuiID_by_id($row['id']);
                }
                return $row;
        }

        public function get_doctor_by_kkid($kkid) {
                $row = array();
                $sql = "select `id`, `user_type`, `sex`, `password`, `phone_num`, `real_name`, `header_pic`, `register_time`, `title_aca`, `title_med`, `specialty_id`, `feature`, `department_id`, `last_update_time`, `pwd_update_time`, `pwd_create_time`, `account_status`, `bio`, `device_token`, `push_user_id`, `push_channel_id`, `tools`, `hospital_id`, `dept_phone`, `referer`, `internal_remark`, `phone_fee`, `phone_fee_unit`, `registered`, `link_to_id`, `last_login_time`, `app_version`, `device_type`, `device_id`, `os`, `os_version`, `channel`, `is_login`, `last_verified_time`, `extra`, `wx_openid`, `auth_pic`, `sales_id`, `auth_pic_status`, `membership_level`, `continuous_login_day_count`, `user_token`, `update_date`, `kkid`, `ease_orgname`, `ease_appname`, `ease_groupid` from `doctor` where `kkid` = ? ;";
                Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$kkid"));
                $row = $stmt->fetch();
                if(empty($row)){
                   $row = array();
                }
                else{
                   $row['doctor_service'] = self::get_doctor_service_by_id($row['id']);
                   $row['doctor_getuiID'] = self::get_doctor_getuiID_by_id($row['id']);
                }
                Logger::info(__FILE__, __CLASS__, __LINE__, var_export($row, true));
                return $row;
        }

        public function get_doctor_by_wx_info($wx_openid, $wx_unionid) {
                $row = array();
                $sql = "select `id`, `user_type`, `sex`, `password`, `phone_num`, `real_name`, `header_pic`, `register_time`, `title_aca`, `title_med`, `specialty_id`, `feature`, `department_id`, `last_update_time`, `pwd_update_time`, `pwd_create_time`, `account_status`, `bio`, `device_token`, `push_user_id`, `push_channel_id`, `tools`, `hospital_id`, `dept_phone`, `referer`, `internal_remark`, `phone_fee`, `phone_fee_unit`, `registered`, `link_to_id`, `last_login_time`, `app_version`, `device_type`, `device_id`, `os`, `os_version`, `channel`, `is_login`, `last_verified_time`, `extra`, `wx_openid`, `auth_pic`, `sales_id`, `auth_pic_status`, `membership_level`, `continuous_login_day_count`, `user_token`, `update_date`, `kkid`, `ease_orgname`, `ease_appname`, `ease_groupid` from `doctor` where `wx_openid` = ? ;";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array($wx_openid));
                if(empty($row)){
                    $row = array();
                } else {
                    $row['doctor_service'] = self::get_doctor_service_by_id($row['id']);
                    $row['doctor_getuiID'] = self::get_doctor_getuiID_by_id($row['id']);
                }

                return $row;
        }


        public function get_doctor_service_by_id($id) {
                $row = array();
                $sql = "select a.*,b.code service_code ,b.name service_name from doctor_service a left join service b on a.service_id=b.id where a.doctor_id = ?;";
                Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$id"));
                $row = $stmt->fetch();
                if(empty($row)){
                   $row = array();
                }
                return $row;
        }
        public function get_doctor_getuiID_by_id($id) {
                $row = array();
                $sql = "select `id`, `user_type`, `user_id`, `device_token`, `push_method`, `getui_client_id`, `tag_list` from `push_user` where user_type='DOC' and user_id = ? and push_method='getui';";
                Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$id"));
                $row = $stmt->fetch();
                if(empty($row)){
                   $row = array();
                }
                return $row;
        }

        public function get_doctor_favorite_product($doctor_id) {
                $pid = 0;
                $sql = "select commodity_id from qpg_commodity_favorite where doctor_id = ? order by id desc limit 1;";
                Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$doctor_id"));
                $pid = $stmt->fetchColumn();
                return $pid;
        }

        public function get_doctor_specialty_product($doctor_id) {
                $pid = 0;
                $sql = "select a.qpgcommodity_id from qpg_commodity_specialty a left join specialty b on a.specialty_id=b.id left join doctor c on a.specialty_id=c.specialty_id where c.id= ? order by a.id desc limit 1;";
                Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$doctor_id"));
                $pid = $stmt->fetchColumn();
                return $pid;
        }

/*
*/

}
