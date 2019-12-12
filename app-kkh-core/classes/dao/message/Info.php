<?php
apf_require_class("APF_DB_Factory");

class Dao_Message_Info {

	    private $pdo;

	    public function __construct() {
		        $this->pdo = APF_DB_Factory::get_instance()->get_pdo("chat_master");
	    }

        public function create_message($data) {
                unset($data['update_time']);
                unset($data['kkid']);
                $sql = "insert into `t_message` (`id`, `kkid`, `msg_id`, `msg_type`, `target_type`, `from_kkid`, `to_kkid`, `content`, `from_status`, `to_status`, `unread`, `sent_time`, `create_time`, `update_time`) values(:id, replace(upper(uuid()),'-',''), :msg_id, :msg_type, :target_type, :from_kkid, :to_kkid, :content, :from_status, :to_status, :unread, :sent_time, :create_time, now());";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                $last_id = $this->pdo->lastInsertId();
                return $last_id;
        }


        public function set_message($data) {
                unset($data['created']);
                unset($data['update_date']);
                unset($data['kkid']);
                $sql = "update `t_message` set `msg_id` = :msg_id, `msg_type` = :msg_type, `target_type` = :target_type, `from_kkid` = :from_kkid, `to_kkid` = :to_kkid, `content` = :content, `from_status` = :from_status, `to_status` = :to_status, `unread` = :unread where `id` = :id ;";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                return $res;
        }

        public function set_message_mediafile($data) {
                $sql = "update `t_message` set `mediafile_url` = :mediafile_url where `id` = :id ;";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                return $res;
        }

        public function get_message($id) {
                $row = array();
                $sql = "select `id`, `kkid`, `msg_id`, `msg_type`, `target_type`, `from_kkid`, `to_kkid`, `content`, `from_status`, `to_status`, `unread`, `sent_time`, `create_time`, `update_time` from `t_message` where `id` = ? ;";
                Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$id"));
                $row = $stmt->fetch();
                if(empty($row)){
                   $row = array();
                }
                return $row;
        }

        public function get_message_list($from_kkid, $to_kkid) {
                $row = array();
                $sql = "select `id`, `kkid`, `msg_id`, `msg_type`, `target_type`, `from_kkid`, `to_kkid`, `content`, `from_status`, `to_status`, `unread`, `sent_time`, `create_time`, `update_time` from `t_message` where `from_kkid` = ? and `to_kkid` = ? ;";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$from_kkid", "$to_kkid"));
                $jobs = $stmt->fetchAll();

                $message2 = self::get_message_list2($to_kkid, $from_kkid);

                $job = array();
                foreach($jobs as $k=>$j){
                    $key = $j['create_time'];
                    $job[$key] = $j;
                }
                foreach($message2 as $k2=>$j2){
                    $key = $j2['create_time'];
                    $job[$key] = $j2;
                }
                return ksort($job);
        }

        public function get_group_message_list($to_kkid, $limit, $offset) {
                $row = array();
                $sql = "select `id`, `kkid`,`msg_id`, `msg_type`, `target_type`, `from_kkid`, `to_kkid`, `content`, `from_status`, `to_status`, `unread`, `sent_time`, `create_time`, `update_time` from `t_message` where `target_type` = 'chatgroups' and `to_kkid` = :to_kkid order by create_time desc LIMIT :limit OFFSET :offset;";
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam(':to_kkid', $to_kkid, PDO::PARAM_STR);
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
                $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
                
                $jobs = $stmt->fetchAll();

                $job = array();
                foreach($jobs as $k=>$j){
                    $key = $j['create_time'];
                    $job[$key] = $j;
                }
                return ksort($job);
        }

        private function get_message_list2($from_kkid, $to_kkid) {
                $row = array();
                $sql = "select `id`, `kkid`, `msg_id`, `msg_type`, `target_type`, `from_kkid`, `to_kkid`, `content`, `from_status`, `to_status`, `unread`, `sent_time`, `create_time`, `update_time` from `t_message` where `from_kkid` = ? and `to_kkid` = ? limit 20;";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$from_kkid", "$to_kkid"));
                $jobs = $stmt->fetchAll();
                return $jobs;
        }

        public function get_message_by_msgid($msg_id) {
                $row = array();
                $sql = "select `id` , `kkid`, `msg_id`, `msg_type`, `target_type`, `from_kkid`, `to_kkid`, `content`, `from_status`, `to_status`, `unread`, `sent_time`, `create_time`, `update_time` from `t_message` where `msg_id` = ? ;";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$msg_id"));
                $row = $stmt->fetch();
                if(empty($row)){
                   $row = array();
                }
                return $row;
        }

        public function create_images($data) {
                unset($data['update_date']);
                unset($data['kkid']);
                $sql = "insert into `t_images` (`id`, `kkid`, `o_kkid`, `info_type`, `img_url`, `domain`, `image_hashkey`, `description`, `datei`, `transfer_success`, `dl_retry`, `width`, `height`, `wh_ratio`, `wh_range`, `file_size`, `status`, `created`, `update_date`) values(:id, replace(upper(uuid()),'-',''), :o_kkid, :info_type, :img_url, :domain, :image_hashkey, :description, :datei, :transfer_success, :dl_retry, :width, :height, :wh_ratio, :wh_range, :file_size, :status, :created, now());";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                $last_id = $this->pdo->lastInsertId();
                return $last_id;
        }

        public function get_images($id) {
                $row = array();
                $sql = "select `id`, `kkid`, `o_kkid`, `info_type`, `img_url`, `domain`, `image_hashkey`, `description`, `datei`, `transfer_success`, `dl_retry`, `width`, `height`, `wh_ratio`, `wh_range`, `file_size`, `status`, `created`, `update_date` from `t_images` where `id` = ? ;";
                Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$id"));
                $row = $stmt->fetch();
                if(empty($row)){
                   $row = array();
                }
                return $row;
        }

        public function get_images_by_kkid($kkid) {
                $row = array();
                $sql = "select `id`, `kkid`, `o_kkid`, `info_type`, `img_url`, `domain`, `image_hashkey`, `description`, `datei`, `transfer_success`, `dl_retry`, `width`, `height`, `wh_ratio`, `wh_range`, `file_size`, `status`, `created`, `update_date` from `t_images` where `kkid` = ? ;";
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
