<?php
apf_require_class("APF_DB_Factory");

class Dao_Guest_Info {

	private $pdo;

	public function __construct() {
		$this->pdo = APF_DB_Factory::get_instance()->get_pdo("shop_master");
	}


        public function create_guest($data) {
                #Logger::info(__FILE__, __CLASS__, __LINE__, $u_kkid);
                unset($data['kkid']);
                unset($data['update_date']);
                $sql = "insert into `s_guest` (`id_guest`, `kkid`, `client_ip`, `id_operating_system`, `id_web_browser`, `id_customer`, `javascript`, `screen_resolution_x`, `screen_resolution_y`, `screen_color`, `sun_java`, `adobe_flash`, `adobe_director`, `apple_quicktime`, `real_player`, `windows_media`, `accept_language`, `mobile_theme`, `created`, `update_date`) values(:id_guest, replace(upper(uuid()),'-',''), :client_ip, :id_operating_system, :id_web_browser, :id_customer, :javascript, :screen_resolution_x, :screen_resolution_y, :screen_color, :sun_java, :adobe_flash, :adobe_director, :apple_quicktime, :real_player, :windows_media, :accept_language, :mobile_theme, :created, now());";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                $last_id = $this->pdo->lastInsertId();
                return $last_id;
        }


        public function get_guest($id_guest) {
                $row = array();
                $sql = "select `kkid`, `client_ip`, `id_operating_system`, `id_web_browser`, `id_customer`, `javascript`, `screen_resolution_x`, `screen_resolution_y`, `screen_color`, `sun_java`, `adobe_flash`, `adobe_director`, `apple_quicktime`, `real_player`, `windows_media`, `accept_language`, `mobile_theme`, `created`, `update_date` from `s_guest` where `id_guest` = ? ;";
                Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$id_guest"));
                
                $row = $stmt->fetch();

                if(empty($row)){
                   $row = array();
                }
                return $row;
        }

        public function get_guest_by_kkid($kkid) {
                $row = array();
                $sql = "select `id_guest`, `kkid`, `client_ip`, `id_operating_system`, `id_web_browser`, `id_customer`, `javascript`, `screen_resolution_x`, `screen_resolution_y`, `screen_color`, `sun_java`, `adobe_flash`, `adobe_director`, `apple_quicktime`, `real_player`, `windows_media`, `accept_language`, `mobile_theme`, `created`, `update_date` from `s_guest` where `kkid` = ? ;";
                Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$kkid"));
                
                $row = $stmt->fetch();

                if(empty($row)){
                   $row = array();
                }
                return $row;
        }

/*
*/

}
