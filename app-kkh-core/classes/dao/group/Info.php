<?php
apf_require_class("APF_DB_Factory");

class Dao_Group_Info {

	    private $pdo;

	    public function __construct() {
		        $this->pdo = APF_DB_Factory::get_instance()->get_pdo("chat_master");
	    }

        public function create_group($data) {
                $sql = "insert into `t_group` (`gid`, `group_id`, `group_name`, `doctor_id`, `group_desc`, `members_only`, `allow_invites`, `invite_need_confirm`, `maxusers`, `affiliations_count`, `owner`, `client_ip`, `created`, `update_date`) values(:gid, :group_id, :group_name, :doctor_id, :group_desc, :members_only, :allow_invites, :invite_need_confirm, :maxusers, :affiliations_count, :owner, :client_ip, :created, now() );";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                $last_id = $this->pdo->lastInsertId();
                return $last_id;
        }

        public function set_group($data) {
                unset($data['created']);
                unset($data['update_date']);
                $sql = "update `t_group` set `group_id` = :group_id, `group_name` = :group_name, `group_desc` = :group_desc, `members_only` = :members_only, `allow_invites` = :allow_invites, `invite_need_confirm` = :invite_need_confirm, `maxusers` = :maxusers, `affiliations_count` = :affiliations_count, `owner` = :owner, `client_ip` = :client_ip  where `gid` = :gid ;";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                return $res;
        }

        public function get_group($id) {
                $row = array();
                $sql = "select `gid`, `group_id`, `group_name`, `group_desc`, `members_only`, `allow_invites`, `invite_need_confirm`, `maxusers`, `affiliations_count`, `owner`, `client_ip`, `created`, `update_date` from `t_group` where `gid` = ? ;";
                Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$id"));
                $row = $stmt->fetch();
                if(empty($row)){
                   $row = array();
                }
                return $row;
        }

        public function get_group_by_groupname($groupname) {
                $row = array();
                $sql = "select `gid`, `group_id`, `group_name`, `group_desc`, `members_only`, `allow_invites`, `invite_need_confirm`, `maxusers`, `affiliations_count`, `owner`, `client_ip`, `created`, `update_date` from `t_group` where `group_name` = ? ;";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$groupname"));
                $row = $stmt->fetch();
                if(empty($row)){
                   $row = array();
                }
                return $row;
        }

        public function get_group_by_groupid($groupid) {
                $row = array();
                $sql = "select `gid`, `group_id`, `group_name`, `group_desc`, `members_only`, `allow_invites`, `invite_need_confirm`, `maxusers`, `affiliations_count`, `owner`, `client_ip`, `created`, `update_date` from `t_group` where `group_id` = ? ;";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$groupid"));
                $row = $stmt->fetch();
                if(empty($row)){
                   $row = array();
                }
                return $row;
        }

        public function add_member($groupid) {
                $row = array();
                $sql = "update t_group set affiliations_count = affiliations_count + 1 where group_id = ?";
                //Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                //Logger::info(__FILE__, __CLASS__, __LINE__, $groupid);
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute(array("$groupid"));
                //Logger::info(__FILE__, __CLASS__, __LINE__, $res);
                return $res;
        }

        public function delete_member($groupid) {
                $row = array();
                $sql = "update t_group set affiliations_count = affiliations_count - 1 where group_id = ?";
                //Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                //Logger::info(__FILE__, __CLASS__, __LINE__, $groupid);
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute(array("$groupid"));
                //Logger::info(__FILE__, __CLASS__, __LINE__, $res);
                return $res;
        }

/*
*/

}
