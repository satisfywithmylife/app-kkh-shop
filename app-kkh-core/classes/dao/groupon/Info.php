<?php
apf_require_class("APF_DB_Factory");

class Dao_Groupon_Info {

	    private $pdo;

	    public function __construct() {
		        $this->pdo = APF_DB_Factory::get_instance()->get_pdo("groupon_master");
	    }

        public function create_groupon($data) {
                $sql = "insert into `s_product_group` (`part_num`, `id_group`, `kkid`, `p_kkid`, `id_product`, `from_date`, `to_date`, `vouchers`, `share_title`, `share_description`, `share_image`, `is_online`, `is_active`, `discount_amount`, `created_by`, `last_modified`, `created_at`, `updated_at`) values(:part_num, :id_group, replace(upper(uuid()),'-',''), :p_kkid, :id_product, :from_date, :to_date, :vouchers, :share_title, :share_description, :share_image, :is_online, :is_active, :discount_amount, :created_by, :last_modified, :created_at, now());";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                $last_id = $this->pdo->lastInsertId();
                return $last_id;
        }

        public function set_groupon($data) {
                unset($data['created_at']);
                unset($data['updated_at']);
                $sql = "update `s_product_group` set `part_num` = :part_num,`from_date` = :from_date, `to_date` = :to_date, `vouchers` = :vouchers, `share_title` = :share_title, `share_description` = :share_description, `share_image` = :share_image, `is_online` = :is_online, `is_active` = :is_active, `discount_amount` = :discount_amount, `created_by` = :created_by, `last_modified` = :last_modified where `kkid` = :kkid ;";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                return $res;
        }

        public function get_groupon($id) {
                $row = array();
                $sql = "select `id_group`, `kkid`, `p_kkid`, `id_product`, `from_date`, `to_date`, `vouchers`, `share_title`, `share_description`, `share_image`, `is_online`, `is_active`, `discount_amount`, `created_by`, `last_modified`, `created_at`, `updated_at` from `s_product_group` where `id_group` = ? ;";
                //Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$id"));
                $row = $stmt->fetch();
                if(empty($row)){
                   $row = array();
                }
                return $row;
        }

        public function get_groupon_by_kkid($kkid) {
                $row = array();
                $sql = "select `part_num`, `id_group`, `kkid`, `p_kkid`, `id_product`, `from_date`, `to_date`, `vouchers`, `share_title`, `share_description`, `share_image`, `is_online`, `is_active`, `discount_amount`, `created_by`, `last_modified`, `created_at`, `updated_at` from `s_product_group` where `kkid` = ? ;";
                //Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$kkid"));
                $row = $stmt->fetch();
                if(empty($row)){
                   $row = array();
                }
                return $row;
        }

        public function get_groupon_by_pkkid($kkid) {
                $row = array();
                $sql = "select `kkid`, `discount_amount` from `s_product_group` where is_online = 1 and is_active = 1 and from_date <= date(now()) and to_date >= date(now()) and `p_kkid` = ? order by id_group desc limit 1 ;";
                //Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$kkid"));
                $row = $stmt->fetch();
                if(empty($row)){
                   $row = array();
                }
                return $row;
        }

        public function get_groupon_by_g_kkid($g_kkid) {
                $row = array();
                $sql = "select `kkid`, `discount_amount` from `s_product_group` where is_online = 1 and is_active = 1 and from_date <= date(now()) and to_date >= date(now()) and `kkid` = ? order by id_group desc limit 1 ;";
                //Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$g_kkid"));
                $row = $stmt->fetch();
                if(empty($row)){
                   $row = array();
                }   
                return $row;
        }

        //设置限时团购商品
        public function set_limit_time_shop($id)
        {
            $ago_limit_shop = $this->limit_time_shop();
            if($ago_limit_shop){
                $this->cancel_limit_time($ago_limit_shop['id_group']);
            }
            $sql = "UPDATE `s_product_group` SET is_limit_time = 1 WHERE id_group =:id ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $res = $stmt->execute();
            return $res;
        }
		//查看设置的团购是否符合要求 （必须在活动期之内 被激活 上线
	    public function get_groupon_can_limit_time($id)
		{
		    $sql = "SELECT
			   			`id_group`,
						`kkid`,
						`p_kkid`,
						`id_product`,
						`from_date`
					FROM
						`s_product_group`
					WHERE
					    id_group = $id
						AND is_online = 1
						AND is_active = 1
						AND from_date <= date(now())
						AND to_date >= date(now()) LIMIT 1";
			Logger::info(__FILE__, __CLASS__, __LINE__, "sql: $sql");
			$stmt = $this->pdo->prepare($sql);
			$stmt->execute();
			$jobs = $stmt->fetch();
			return $jobs;
		
		}

        //取消显示拼团
        public function cancel_limit_time($id){
            if(!$id){
                return false;
            }
            $sql = "UPDATE `s_product_group` SET is_limit_time = 0 WHERE id_group =:id ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $res = $stmt->execute();
            return $res;
        }

    /**
     * @param string $gid
     * @return mixed 查询限时抢购的拼团
     */
        public function limit_time_shop($gid=''){
            $sql = "SELECT
                        `id_group`,
                        `kkid`,
                        `p_kkid`,
                        `id_product`,
                        `from_date`,
                        `to_date`,
                        `vouchers`,
                        `share_image`,
                        `is_online`,
                        `is_active`,
					    `part_num` as cus_num,
						`created_at`,
						`updated_at`,
						`discount_amount`
                    FROM
                        `s_product_group`
                    WHERE
                        is_online = 1
                    AND is_active = 1
                    AND is_limit_time = 1
                    AND from_date <= date(now())
                    AND to_date >= date(now())";
            if($gid){
                $sql .= " AND id_group = $gid LIMIT 1";
            }else{
                $sql .= " LIMIT 1";
            }
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $jobs = $stmt->fetch();
            return $jobs;
        }

        public function get_product_groupon_list($limit, $offset)
        {
            if(!is_numeric($limit) || !is_numeric($offset)){
                return array();
            }
            $sql = "select `id_group`, `kkid`, `p_kkid`, `id_product`, `from_date`, `to_date`, `vouchers`, `share_title`, `share_description`, `share_image`, `is_online`, `is_active`, `discount_amount`, `created_by`, `last_modified`, `created_at`, `updated_at` from `s_product_group` where is_online = 1 and is_active = 1 and from_date <= date(now()) and to_date >= date(now())  order by id_group desc LIMIT :limit OFFSET :offset;";
            //Logger::info(__FILE__, __CLASS__, __LINE__, "sql: $sql");
            //Logger::info(__FILE__, __CLASS__, __LINE__, "limit: $limit");
            //Logger::info(__FILE__, __CLASS__, __LINE__, "offset: $offset");
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $jobs = $stmt->fetchAll();
            return $jobs;
        }
        public function get_product_groupon_adminlist($p_kkid, $limit, $offset)
        {
            if(!is_numeric($limit) || !is_numeric($offset)){
                return array();
            }
            $sql = "select `id_group`, `kkid`, `p_kkid`, `id_product`, `from_date`, `to_date`, `vouchers`, `share_title`, `share_description`, `share_image`, `is_online`, `is_active`, `discount_amount`, `created_by`, `last_modified`, `created_at`, `updated_at` from `s_product_group` where is_active = 1 and p_kkid = :p_kkid  order by id_group desc LIMIT :limit OFFSET :offset;";
            //Logger::info(__FILE__, __CLASS__, __LINE__, "sql: $sql");
            //Logger::info(__FILE__, __CLASS__, __LINE__, "limit: $limit");
            //Logger::info(__FILE__, __CLASS__, __LINE__, "offset: $offset");
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':p_kkid', $p_kkid, PDO::PARAM_STR);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $jobs = $stmt->fetchAll();
            return $jobs;
        }

        public function get_product_groupon_count()
        {
            $c = 0;
            $get_count_sql = "select count(*) c from `s_product_group` where is_online = 1 and is_active = 1 and from_date <= date(now()) and to_date >= date(now()) ;";
            //Logger::info(__FILE__, __CLASS__, __LINE__, "sql: $get_count_sql");
            $stmt = $this->pdo->prepare($get_count_sql);
            $stmt->execute();
            $c = $stmt->fetchColumn();
            return $c;

        }

        public function get_product_groupon_adminlist_count($p_kkid)
        {
            $c = 0;
            $get_count_sql = "select count(*) c from `s_product_group` where is_active = 1 and p_kkid = :p_kkid ;";
            //Logger::info(__FILE__, __CLASS__, __LINE__, "sql: $get_count_sql");
            $stmt = $this->pdo->prepare($get_count_sql);
            $stmt->bindParam(':p_kkid', $p_kkid, PDO::PARAM_STR);
            $stmt->execute();
            $c = $stmt->fetchColumn();
            return $c;

        }

/*
*/

}
