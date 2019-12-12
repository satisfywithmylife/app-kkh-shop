<?php

class  Bll_Message_Info {
	private $messageInfoDao;

	public function __construct() {
		$this->messageInfoDao = new Dao_Message_Info();
	}

        public function create_message($data) {
                if(empty($data)) return array();
                return $this->messageInfoDao->create_message($data);
        }

        public function set_message($data) {
                if(empty($data)) return array();
                return $this->messageInfoDao->set_message($data);
        }

        public function set_message_mediafile($data) {
                if(empty($data)) return array();
                return $this->messageInfoDao->set_message_mediafile($data);
        }

        public function get_message($id) {
                if(empty($id)) return array();
                return $this->messageInfoDao->get_message($id);
        }

        public function get_message_list($from_kkid, $to_kkid) {
                if(empty($from_kkid) || empty($to_kkid)) return array();
                return $this->messageInfoDao->get_message_list($from_kkid, $to_kkid);
        }

        public function get_group_message_list($to_kkid, $limit, $offset) {
                if(empty($from_kkid) || empty($limit) || empty($offset)) return array();
                return $this->messageInfoDao->get_group_message_list($to_kkid, $limit, $offset);
        }

        public function get_message_by_msgid($msg_id) {
                if(empty($msg_id)) return array();
                return $this->messageInfoDao->get_message_by_msgid($msg_id);
        }

        public function create_images($data) {
                if(empty($data)) return array();
                return $this->messageInfoDao->create_images($data);
        }

        public function get_images($id) {
                if(empty($id)) return array();
                return $this->messageInfoDao->get_images($id);
        }

        public function get_images_by_kkid($kkid) {
                if(empty($kkid)) return array();
                return $this->messageInfoDao->get_images_by_kkid($kkid);
        }

}
