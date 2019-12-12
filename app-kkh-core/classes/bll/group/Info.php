<?php

class  Bll_Group_Info {
	private $groupInfoDao;

	public function __construct() {
		$this->groupInfoDao = new Dao_Group_Info();
	}

        public function create_group($data) {
                if(empty($data)) return array();
                return $this->groupInfoDao->create_group($data);
        }

        public function set_group($data) {
                if(empty($data)) return array();
                return $this->groupInfoDao->set_group($data);
        }

        public function get_group($id) {
                if(empty($id)) return array();
                return $this->groupInfoDao->get_group($id);
        }

        public function get_group_by_groupname($groupname) {
                if(empty($groupname)) return array();
                return $this->groupInfoDao->get_group_by_groupname($groupname);
        }

        public function get_group_by_groupid($groupid) {
                if(empty($groupid)) return array();
                return $this->groupInfoDao->get_group_by_groupid($groupid);
        }
        public function add_member($groupid) {
                if(empty($groupid)) return array();
                return $this->groupInfoDao->add_member($groupid);
        }
        public function delete_member($groupid) {
                if(empty($groupid)) return array();
                return $this->groupInfoDao->delete_member($groupid);
        }

}
