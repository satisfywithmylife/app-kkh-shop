<?php
apf_require_class("APF_DB_Factory");

class Dao_Coupon_FshareInfo {

	private $pdo;

	public function __construct() {
		$this->pdo = APF_DB_Factory::get_instance()->get_pdo("coupon_master");
	}

        public function create_share_coupon($data) {
                unset($data['kkid']);
                unset($data['update_date']);

                Logger::info(__FILE__, __CLASS__, __LINE__, var_export($data, true));

                $sql = "insert into `t_fshare_coupon` (`id`, `kkid`, `sender`, `receiver`, `mobile_num`, `wxopenid`, `coupon_code`, `coupon_value`, `status`, `active_ver`, `created`, `update_date`) values(:id, replace(upper(uuid()),'-',''), :sender, :receiver, :mobile_num, :wxopenid, :coupon_code, :coupon_value, :status, :active_ver, :created, now());";

                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                $last_id = $this->pdo->lastInsertId();
                return $last_id;
        }

        public function set_share_coupon($data) {
                //
                Logger::info(__FILE__, __CLASS__, __LINE__, var_export($data, true));
                unset($data['kkid']);
                unset($data['created']);
                unset($data['update_date']);

                $sql = "update `t_fshare_coupon` set `sender` = :sender, `receiver` = :receiver, `mobile_num` = :mobile_num, `wxopenid` = :wxopenid, `coupon_code` = :coupon_code, `coupon_value` = :coupon_value, `status` = :status where `id` = :id ;";

                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute($data);
        }

        public function get_share_coupon($id) {
            $row = array();
            $sql = "select `id`, `kkid`, `sender`, `receiver`, `mobile_num`, `wxopenid`, `coupon_code`, `coupon_value`, `status`, `created`, `update_date` from `t_fshare_coupon` where `id` = ? ;";
            Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array("$id"));
            $row = $stmt->fetch();
            if(isset($row['id']) && empty($row['id'])){
            }
            if(empty($row)){
               $row = array();
            }
            return $row;
        }

        public function get_share_coupon_total_value($receiver, $ver=1) {
            $v = 0;
            $sql = "select count(*) c from `t_fshare_coupon` where `receiver` = ? and status = 1 and active_ver = ? ;";
            Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array("$receiver", "$ver"));
            $v = $stmt->fetchColumn();
            return $v;
        }

        //检查 coupon 是否已存在
        public function check_coupon_exist($sender, $receiver){
            $c_kkid = '';
            //$sql = "select kkid from `t_fshare_coupon` where `sender` = ? and `receiver` = ? and status = 1 limit 1;";
            $sql = "select kkid from `t_fshare_coupon` where `receiver` = ? and status = 1 limit 1;";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array("$receiver"));
            $c_kkid = $stmt->fetchColumn();
            return $c_kkid;
        }

        //检查 coupon 是否已存在
        public function check_coupon_exist_adv($sender, $receiver, $ver){
            $c_kkid = '';
            $sql = "select kkid from `t_fshare_coupon` where `receiver` = ? and status = 1 and active_ver = ? limit 1;";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array("$receiver", "$ver"));
            $c_kkid = $stmt->fetchColumn();
            return $c_kkid;
        }

/*
*/

}
