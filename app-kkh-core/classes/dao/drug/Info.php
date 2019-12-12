<?php
apf_require_class("APF_DB_Factory");

class Dao_Drug_Info {

	private $pdo;

	public function __construct() {
		$this->pdo = APF_DB_Factory::get_instance()->get_pdo("master");
	}

        public function set_drug_by_kkid($u_kkid, $d_kkid, $data) {
                if(isset($data['id'])) unset($data['id']);
                if(isset($data['kkid'])) unset($data['kkid']);
                if(isset($data['d_kkid'])) unset($data['d_kkid']);
                if(isset($data['created'])) unset($data['created']);
                if(isset($data['update_date'])) unset($data['update_date']);
                $data['u_kkid'] = $u_kkid;
                $data['kkid'] = $d_kkid;

                $sql = "update `t_drug` set `u_kkid` = :u_kkid, `name` = :name, `c_name` = :c_name, `e_name` = :e_name, `py_name` = :py_name, `type` = :type, `approval_id` = :approval_id, `specs` = :specs, `dosage_form` = :dosage_form, `indication` = :indication, `ingredients` = :ingredients, `shape` = :shape, `usage` = :usage, `adverse_reaction` = :adverse_reaction, `taboo` = :taboo, `attentions` = :attentions, `attentions_pw` = :attentions_pw, `attentions_ch` = :attentions_ch, `attentions_oa` = :attentions_oa, `overdose` = :overdose, `chinical_trial` = :chinical_trial, `toxicology` = :toxicology, `pharmacokinetics` = :pharmacokinetics, `storage_conditions` = :storage_conditions, `package` = :package, `period_validity` = :period_validity, `performance_standards` = :performance_standards, `price` = :price, `preparation` = :preparation, `adaptation_department` = :adaptation_department, `therapeutic_field` = :therapeutic_field, `product_advantage` = :product_advantage, `channel` = :channel, `business_type` = :business_type, `medical_insurance` = :medical_insurance, `competitive_products` = :competitive_products, `manufacturer` = :manufacturer, `l_info` = :l_info, `v_info` = :v_info, `imgs_num` = :imgs_num, `status` = :status where `kkid` = :kkid ;";
                //Logger::info(__FILE__, __CLASS__, __LINE__, var_export($data, true));
                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute($data);
        }

        public function add_drug($u_kkid, $data) {
                if(isset($data['id'])) unset($data['id']);
                if(isset($data['kkid'])) unset($data['kkid']);
                if(isset($data['d_kkid'])) unset($data['d_kkid']);
                if(isset($data['update_date'])) unset($data['update_date']);
                $data['u_kkid'] = $u_kkid;
                $sql = "insert into `t_drug` (`id`, `kkid`, `u_kkid`, `name`, `c_name`, `e_name`, `py_name`, `type`, `approval_id`, `specs`, `dosage_form`, `indication`, `ingredients`, `shape`, `usage`, `adverse_reaction`, `taboo`, `attentions`, `attentions_pw`, `attentions_ch`, `attentions_oa`, `overdose`, `chinical_trial`, `toxicology`, `pharmacokinetics`, `storage_conditions`, `package`, `period_validity`, `performance_standards`, `price`, `preparation`, `adaptation_department`, `therapeutic_field`, `product_advantage`, `channel`, `business_type`, `medical_insurance`, `competitive_products`, `manufacturer`, `l_info`, `v_info`, `imgs_num`, `status`, `created`, `update_date`) values(0, replace(upper(uuid()),'-',''), :u_kkid, :name, :c_name, :e_name, :py_name, :type, :approval_id, :specs, :dosage_form, :indication, :ingredients, :shape, :usage, :adverse_reaction, :taboo, :attentions, :attentions_pw, :attentions_ch, :attentions_oa, :overdose, :chinical_trial, :toxicology, :pharmacokinetics, :storage_conditions, :package, :period_validity, :performance_standards, :price, :preparation, :adaptation_department, :therapeutic_field, :product_advantage, :channel, :business_type, :medical_insurance, :competitive_products, :manufacturer, :l_info, :v_info, :imgs_num, :status, :created, now());";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                /* */
                $last_id = $this->pdo->lastInsertId();
                //Logger::info(__FILE__, __CLASS__, __LINE__, "last_id: $last_id");
                $d_kkid = self::get_drug_kkid_by_id($last_id);
                return $d_kkid;
        }

        private function get_drug_kkid_by_id($id) {
                $sql = "select `kkid` from `t_drug` where `id` = ? ;";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$id"));
                $kkid = $stmt->fetchColumn();
                if(!empty($kkid) && strlen($kkid) == 32){
                   //$kkid = '';
                }
                else{
                   $kkid = '';
                }
                return $kkid;
        }


        public function get_drug($kkid, $u_kkid) {
                $cond1 = "";
                if(!empty($u_kkid)){
                   $cond1 = " and kkid in (select d_kkid from t_serve_scope where status = 1 and u_kkid = ?) ";
                }
                $sql = "select `kkid`, `u_kkid`, `name`, `c_name`, `e_name`, `py_name`, `type`, `approval_id`, `specs`, `dosage_form`, `indication`, `ingredients`, `shape`, `usage`, `adverse_reaction`, `taboo`, `attentions`, `attentions_pw`, `attentions_ch`, `attentions_oa`, `overdose`, `chinical_trial`, `toxicology`, `pharmacokinetics`, `storage_conditions`, `package`, `period_validity`, `performance_standards`, `price`, `preparation`, `adaptation_department`, `therapeutic_field`, `product_advantage`, `channel`, `business_type`, `medical_insurance`, `competitive_products`, `manufacturer`, `l_info`, `v_info`, `imgs_num`, `status`, `created`, `update_date` from `t_drug` where `kkid` = ?  and status = 1 $cond1 limit 1;";
                $stmt = $this->pdo->prepare($sql);
                if(!empty($u_kkid)){
                   $stmt->execute(array("$kkid", "$u_kkid"));
                }
                else{
                   $stmt->execute(array("$kkid"));
                }
                $row = $stmt->fetch();
                if(isset($row['created'])) $row['created'] = date('y-m-d H:i:s', $row['created']);
                Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                return $row;
        }

        public function get_drug_list($limit, $offset, $u_kkid = '')
        {
            $cond1 = "";
            if(!empty($u_kkid)){
               $cond1 = " and kkid in (select d_kkid from t_serve_scope where status = 1 and u_kkid = :u_kkid) ";
            }
            $sql = "select `kkid`, `name`, `c_name`, `e_name`, `py_name`, `type`, `approval_id`, `specs`, `dosage_form`, `indication`, `ingredients`, `shape`, `usage`, `adverse_reaction`, `taboo`, `manufacturer`, `imgs_num`, `status`, `created`, `update_date` from t_drug where status = 1 $cond1 order by id desc LIMIT :limit OFFSET :offset ;";
            Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            if(!empty($u_kkid)){
              $stmt->bindParam(':u_kkid', $u_kkid, PDO::PARAM_STR);
            }
            $stmt->execute();
            $jobs = $stmt->fetchAll();
            $job = array();
            foreach($jobs as $k=>$j){
              $j['created'] = isset($j['created']) ? date('Y-m-d H:i:s', $j['created']) : '';
              $job[$k] = $j;
            }
    
            return $job;
        }
    
        public function get_drug_count()
        {
            $c = 0;
            $get_count_sql = "select count(*) c from t_drug where status=1;";
            $stmt = $this->pdo->prepare($get_count_sql);
            $stmt->execute();
            $c = $stmt->fetchColumn();
            return $c;
        }

}
