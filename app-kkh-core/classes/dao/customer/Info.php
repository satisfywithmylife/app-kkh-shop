<?php
apf_require_class("APF_DB_Factory");

class Dao_Customer_Info {

	private $pdo;

	public function __construct() {
		$this->pdo = APF_DB_Factory::get_instance()->get_pdo("shop_master");
	}


        public function create_customer($data) {
                #Logger::info(__FILE__, __CLASS__, __LINE__, $u_kkid);
                unset($data['kkid']);
                $sql = "insert into `s_customer` (`id_customer`, `kkid`, `u_kkid`, `client_ip`, `id_shop_group`, `id_shop`, `id_gender`, `id_default_group`, `id_lang`, `id_risk`, `company`, `siret`, `ape`, `firstname`, `lastname`, `email`, `passwd`, `last_passwd_gen`, `birthday`, `newsletter`, `ip_registration_newsletter`, `newsletter_date_add`, `optin`, `website`, `outstanding_allow_amount`, `show_public_prices`, `max_payment_days`, `secure_key`, `note`, `active`, `is_guest`, `deleted`, `date_add`, `date_upd`, `reset_password_token`, `reset_password_validity`, `created`) values(:id_customer, replace(upper(uuid()),'-',''), :u_kkid, :client_ip, :id_shop_group, :id_shop, :id_gender, :id_default_group, :id_lang, :id_risk, :company, :siret, :ape, :firstname, :lastname, :email, :passwd, :last_passwd_gen, :birthday, :newsletter, :ip_registration_newsletter, :newsletter_date_add, :optin, :website, :outstanding_allow_amount, :show_public_prices, :max_payment_days, :secure_key, :note, :active, :is_guest, :deleted, :date_add, :date_upd, :reset_password_token, :reset_password_validity, :created);";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                $last_id = $this->pdo->lastInsertId();
                return $last_id;
        }

        public function set_customer($data) {
                unset($data['kkid']);
                $sql = "update `s_customer` set `id_customer` = :id_customer, `kkid` = :kkid, `u_kkid` = :u_kkid, `client_ip` = :client_ip, `id_shop_group` = :id_shop_group, `id_shop` = :id_shop, `id_gender` = :id_gender, `id_default_group` = :id_default_group, `id_lang` = :id_lang, `id_risk` = :id_risk, `company` = :company, `siret` = :siret, `ape` = :ape, `firstname` = :firstname, `lastname` = :lastname, `email` = :email, `passwd` = :passwd, `last_passwd_gen` = :last_passwd_gen, `birthday` = :birthday, `newsletter` = :newsletter, `ip_registration_newsletter` = :ip_registration_newsletter, `newsletter_date_add` = :newsletter_date_add, `optin` = :optin, `website` = :website, `outstanding_allow_amount` = :outstanding_allow_amount, `show_public_prices` = :show_public_prices, `max_payment_days` = :max_payment_days, `secure_key` = :secure_key, `note` = :note, `active` = :active, `is_guest` = :is_guest, `deleted` = :deleted, `date_add` = :date_add, `date_upd` = :date_upd, `reset_password_token` = :reset_password_token, `reset_password_validity` = :reset_password_validity, `created` = :created where `id_customer` = :id_customer ;";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                $last_id = $this->pdo->lastInsertId();
                return $last_id;
        }

        public function get_customer($id_customer) {
                $row = array();
                $sql = "select `id_customer`, `kkid`, `u_kkid`, `client_ip`, `id_shop_group`, `id_shop`, `id_gender`, `id_default_group`, `id_lang`, `id_risk`, `company`, `siret`, `ape`, `firstname`, `lastname`, `email`, `passwd`, `last_passwd_gen`, `birthday`, `newsletter`, `ip_registration_newsletter`, `newsletter_date_add`, `optin`, `website`, `outstanding_allow_amount`, `show_public_prices`, `max_payment_days`, `secure_key`, `note`, `active`, `is_guest`, `deleted`, `date_add`, `date_upd`, `reset_password_token`, `reset_password_validity`, `created` from `s_customer` where `id_customer` = ? ;";
                Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                Logger::info(__FILE__, __CLASS__, __LINE__, $id_customer);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$id_customer"));
                
                $row = $stmt->fetch();

                if(empty($row)){
                   $row = array();
                }
                return $row;
        }

        // from user center kkid
        public function get_customer_by_u_kkid($u_kkid) {
                $row = array();
                $sql = "select `id_customer`, `kkid`, `u_kkid`, `client_ip`, `id_shop_group`, `id_shop`, `id_gender`, `id_default_group`, `id_lang`, `id_risk`, `company`, `siret`, `ape`, `firstname`, `lastname`, `email`, `passwd`, `last_passwd_gen`, `birthday`, `newsletter`, `ip_registration_newsletter`, `newsletter_date_add`, `optin`, `website`, `outstanding_allow_amount`, `show_public_prices`, `max_payment_days`, `secure_key`, `note`, `active`, `is_guest`, `deleted`, `date_add`, `date_upd`, `reset_password_token`, `reset_password_validity`, `created` from `s_customer` where `u_kkid` = ? ;";
                //Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$u_kkid"));
                
                $row = $stmt->fetch();

                if(empty($row)){
                   $row = array();
                }
                return $row;
        }

        public function get_customer_by_kkid($kkid) {
                $row = array();
                $sql = "select `id_customer`, `kkid`, `u_kkid`, `client_ip`, `id_shop_group`, `id_shop`, `id_gender`, `id_default_group`, `id_lang`, `id_risk`, `company`, `siret`, `ape`, `firstname`, `lastname`, `email`, `passwd`, `last_passwd_gen`, `birthday`, `newsletter`, `ip_registration_newsletter`, `newsletter_date_add`, `optin`, `website`, `outstanding_allow_amount`, `show_public_prices`, `max_payment_days`, `secure_key`, `note`, `active`, `is_guest`, `deleted`, `date_add`, `date_upd`, `reset_password_token`, `reset_password_validity`, `created` from `s_customer` where `u_kkid` = ? ;";
                Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$kkid"));
                
                $row = $stmt->fetch();

                if(empty($row)){
                   $row = array();
                }
                return $row;
        }

        public function get_customer_by_id_address($id_address) {
                $row = array();
                $sql = "select `id_address`, `id_country`, `id_state`, `id_customer`, `id_manufacturer`, `id_supplier`, `id_warehouse`, `alias`, `company`, `lastname`, `firstname`, `address1`, `address2`, `postcode`, `city`, `other`, `phone`, `phone_mobile`, `vat_number`, `dni`, `date_add`, `date_upd`, `active`, `deleted` from `s_address` where `id_address` = ? ;";
                Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                Logger::info(__FILE__, __CLASS__, __LINE__, $id_address);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$id_address"));
                
                $row = $stmt->fetch();

                if(empty($row)){
                   $row = array();
                }
                return $row;
        }

/*
*/

}
