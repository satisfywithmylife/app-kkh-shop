<?php
apf_require_class("APF_DB_Factory");

class Dao_Order_Info {

	private $pdo;
	private $pdo1;

	public function __construct() {
	    $this->pdo = APF_DB_Factory::get_instance()->get_pdo("shop_master");
		$this->pdo1 = APF_DB_Factory::get_instance()->get_pdo("groupon_master");
	}

        public function create_order($data) {
            //Logger::info(__FILE__, __CLASS__, __LINE__, var_export($data, true));
            $sql = "insert into `s_orders` (`id_order`, `reference`, `kkid`, `id_shop_group`, `id_shop`, `id_carrier`, `id_lang`, `id_customer`, `u_kkid`, `id_cart`, `id_currency`, `id_address_delivery`, `id_address_invoice`, `current_state`, `secure_key`, `payment`, `conversion_rate`, `module`, `recyclable`, `gift`, `gift_message`, `mobile_theme`, `shipping_number`, `pick_code`, `cd_key`, `total_discounts`, `total_discounts_tax_incl`, `total_discounts_tax_excl`, `total_paid`, `total_paid_tax_incl`, `total_paid_tax_excl`, `total_paid_real`, `total_products`, `total_products_wt`, `total_shipping`, `total_shipping_tax_incl`, `total_shipping_tax_excl`, `carrier_tax_rate`, `total_wrapping`, `total_wrapping_tax_incl`, `total_wrapping_tax_excl`, `round_mode`, `round_type`, `invoice_number`, `delivery_number`, `invoice_date`, `delivery_date`, `valid`, `c_kkid`, `c_value`, `ref_kkid`, `ref_doctor_id`, `order_source`, `order_type`, `date_add`, `date_upd`) values(:id_order, :reference, replace(upper(uuid()),'-',''), :id_shop_group, :id_shop, :id_carrier, :id_lang, :id_customer, :u_kkid, :id_cart, :id_currency, :id_address_delivery, :id_address_invoice, :current_state, :secure_key, :payment, :conversion_rate, :module, :recyclable, :gift, :gift_message, :mobile_theme, :shipping_number, :pick_code, :cd_key, :total_discounts, :total_discounts_tax_incl, :total_discounts_tax_excl, :total_paid, :total_paid_tax_incl, :total_paid_tax_excl, :total_paid_real, :total_products, :total_products_wt, :total_shipping, :total_shipping_tax_incl, :total_shipping_tax_excl, :carrier_tax_rate, :total_wrapping, :total_wrapping_tax_incl, :total_wrapping_tax_excl, :round_mode, :round_type, :invoice_number, :delivery_number, :invoice_date, :delivery_date, :valid, :c_kkid, :c_value, :ref_kkid, :ref_doctor_id, :order_source, :order_type, :date_add, :date_upd);";
            $stmt = $this->pdo->prepare($sql);
            $res = $stmt->execute($data);
            $last_id = $this->pdo->lastInsertId();
            return $last_id;
        }

        public function set_order($id_order, $data) {
            $data['id_order'] = $id_order;
            $sql = "update `s_orders` set `reference` = :reference, `id_shop_group` = :id_shop_group, `id_shop` = :id_shop, `id_carrier` = :id_carrier, `id_lang` = :id_lang, `id_customer` = :id_customer, `u_kkid` = :u_kkid, `id_cart` = :id_cart, `id_currency` = :id_currency, `id_address_delivery` = :id_address_delivery, `id_address_invoice` = :id_address_invoice, `current_state` = :current_state, `secure_key` = :secure_key, `payment` = :payment, `conversion_rate` = :conversion_rate, `module` = :module, `recyclable` = :recyclable, `gift` = :gift, `gift_message` = :gift_message, `mobile_theme` = :mobile_theme, `shipping_number` = :shipping_number, `total_discounts` = :total_discounts, `total_discounts_tax_incl` = :total_discounts_tax_incl, `total_discounts_tax_excl` = :total_discounts_tax_excl, `total_paid` = :total_paid, `total_paid_tax_incl` = :total_paid_tax_incl, `total_paid_tax_excl` = :total_paid_tax_excl, `total_paid_real` = :total_paid_real, `total_products` = :total_products, `total_products_wt` = :total_products_wt, `total_shipping` = :total_shipping, `total_shipping_tax_incl` = :total_shipping_tax_incl, `total_shipping_tax_excl` = :total_shipping_tax_excl, `carrier_tax_rate` = :carrier_tax_rate, `total_wrapping` = :total_wrapping, `total_wrapping_tax_incl` = :total_wrapping_tax_incl, `total_wrapping_tax_excl` = :total_wrapping_tax_excl, `round_mode` = :round_mode, `round_type` = :round_type, `invoice_number` = :invoice_number, `delivery_number` = :delivery_number, `invoice_date` = :invoice_date, `delivery_date` = :delivery_date, `valid` = :valid, `c_kkid` = :c_kkid, `c_value` = :c_value, `ref_kkid` = :ref_kkid, `date_add` = :date_add, `date_upd` = :date_upd where `id_order` = :id_order ;";
            $stmt = $this->pdo->prepare($sql);
            $res = $stmt->execute($data);
            return $res;
        }

		public function get_expired_product_list($id_customer, $o_kkid, $type) {
			$id_order = self::get_id_order_by_o_kkid($o_kkid);
//			Logger::info(__FILE__, __CLASS__, __LINE__, $type);
			$sql = "select b.kkid, c.attribute_config, c.id_product_attribute, a.product_quantity from s_order_detail a left join s_product b on a.product_id = b.id_product left join s_product_attribute c on a.product_attribute_id = c.id_product_attribute where a.id_order = ?";
			if($type == 1) {
				$sql .= " and b.active = 1;";
			} else {
				$sql .= " and b.active = 0;";
			}

//			Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
			$stmt = $this->pdo->prepare($sql);
			$stmt->execute(array($id_order));
			$res = $stmt->fetchAll();
			//Logger::info(__FILE__, __CLASS__, __LINE__, var_export($res, true));
			return $res;
		}

		public function set_payment_way($o_kkid, $channel){
			if(!$o_kkid || !$channel) return array();
			$sql = "update s_orders set payment = ? where kkid = ?;";
			$stmt = $this->pdo->prepare($sql);
			return $stmt->execute(array($channel, $o_kkid));
		}

		public function get_id_order_by_o_kkid($o_kkid) {
			$sql = 'select id_order from s_orders where kkid = ? limit 1;';
			$stmt = $this->pdo->prepare($sql);
			$stmt->execute(array($o_kkid));
			$res = $stmt->fetchColumn();
			return $res;
		}

        public function add_coupon_to_order($o_kkid, $id_customer, $coupon) {
            $data['kkid'] = $o_kkid;
            $data['id_customer'] = $id_customer;
            $data['c_kkid'] = $coupon['kkid'];
            $data['c_value'] = $coupon['coupon_value'];
			Logger::info(__FILE__, __CLASS__, __LINE__, $data);
            $sql = "update `s_orders` set `c_kkid` = :c_kkid, `c_value` = :c_value where `kkid` = :kkid and `id_customer` = :id_customer";
            $stmt = $this->pdo->prepare($sql);
            $res = $stmt->execute($data);
            return $res;
        }

		public function set_order_status_by_id_order($id_order, $state){
			$sql = "update s_orders set current_state = ? where id_order = ?;";
			$stmt = $this->pdo->prepare($sql);
			return $stmt->execute(array($state, $id_order));
		}

		public function add_remark_to_order($o_kkid, $id_customer, $remark) {
			$data['kkid'] = $o_kkid;
			$data['id_customer'] = $id_customer;
			$data['gift_message'] = $remark;
			Logger::info(__FILE__, __CLASS__, __LINE__, $data);
			$sql = "update `s_orders` set `gift_message` = :gift_message where `kkid` = :kkid and `id_customer` = :id_customer";
			$stmt = $this->pdo->prepare($sql);
			$res = $stmt->execute($data);
			return $res;
		}


        public function get_order($id_order) {
            $row = array();
            //$sql = "select `id_order`, `reference`, `kkid`, `id_shop_group`, `id_shop`, `id_carrier`, `id_lang`, `id_customer`, `u_kkid`, `id_cart`, `id_currency`, `id_address_delivery`, `id_address_invoice`, `current_state`, `secure_key`, `payment`, `conversion_rate`, `module`, `recyclable`, `gift`, `gift_message`, `mobile_theme`, `shipping_number`, `total_discounts`, `total_discounts_tax_incl`, `total_discounts_tax_excl`, `total_paid`, `total_paid_tax_incl`, `total_paid_tax_excl`, `total_paid_real`, `total_products`, `total_products_wt`, `total_shipping`, `total_shipping_tax_incl`, `total_shipping_tax_excl`, `carrier_tax_rate`, `total_wrapping`, `total_wrapping_tax_incl`, `total_wrapping_tax_excl`, `round_mode`, `round_type`, `invoice_number`, `delivery_number`, `invoice_date`, `delivery_date`, `valid`, `c_kkid`, `c_value`, `ref_kkid`, `date_add`, `date_upd` from `s_orders` where `id_order` = ? ;";
           	$sql = "select `id_order`, `kkid`, `id_carrier`, `id_customer`, `u_kkid`, `id_cart`, `id_address_delivery`, `current_state`, `payment`, `total_discounts`, `invoice_date`, `reference`, `total_paid`, `total_paid_real`, `total_products`, `shipping_number`, `delivery_number`, `pick_code`, `cd_key`, `valid`, `c_kkid`, `c_value`, `ref_kkid`, `ref_doctor_id`, `date_add`, `date_upd` from `s_orders` where `id_order` = ? ;";
			Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array("$id_order"));
            
            $row = $stmt->fetch();
            if(isset($row['id_order']) && empty($row['id_order'])){
/*
                $id_order = $row['id_order'];
                $product_list = array();
                $total_price = 0;
                foreach($product_list as $k=>$p){
                    if($p['selected']){
                      $total_price += $p['item_total_price'];
                    }
                }
                $row['product_list'] = $product_list;
                $row['total_price'] = $total_price;
*/
            }
            if(empty($row)){
               $row = array();
            }
            return $row;
        }

		public function get_order_product_num($id_order){
			return 1;
		}
		
		public function change_order_state_by_o_kkid($o_kkid) {
	        $time_now = date('Y-m-d H:i:s');
			$sql = "update s_orders set current_state = 2, date_upd = " . $time_now . " where kkid = ?;";
			$stmt = $this->pdo->prepare($sql);
			$res = $stmt->execute(array("$o_kkid"));
			return $res;
		}

		public function change_order_state_by_pid($pid) {
			$sql = "update t_payment_charge set payment_status = 1 where pid = ?;";
			$stmt = $this->pdo->prepare($sql);
			$res = $stmt->execute(array("$pid"));
			return $res;
		}

        public function change_gorder_state_by_pid($pid) {
            $sql = "update t_payment_charge set payment_status = 1 where pid = ?;";
            $stmt = $this->pdo1->prepare($sql);
            $res = $stmt->execute(array("$pid"));
            return $res;
        }

        public function change_gorder_state_by_id_customer_group($id_customer_group) {
            $sql = "update s_customer_group set current_state = 2 where id_customer_group = ?;";
            $stmt = $this->pdo1->prepare($sql);
            $res = $stmt->execute(array($id_customer_group));
            return $res;
        }   
   

		public function get_order_info_by_order_no($order_no) {
			$row = array();
			$sql = "select * from t_payment_charge where order_no = ? limit 1;";
			$stmt = $this->pdo->prepare($sql);
			$stmt->execute(array("$order_no"));
			$row = $stmt->fetch();
			if (empty($row)) {
				return array();
			}
			return $row;
		}

        public function get_gorder_info_by_order_no($order_no) {
            $row = array();
            $sql = "select * from t_payment_charge where order_no = ? limit 1;";
            $stmt = $this->pdo1->prepare($sql);
            $stmt->execute(array("$order_no"));
			$row = $stmt->fetch();
            if (empty($row)) {
                return array();
            }   
            return $row;
        }   

        public function get_order_by_customer($o_kkid, $id_customer) {
            $row = array();
            //$sql = "select `id_order`, `reference`, `kkid`, `id_shop_group`, `id_shop`, `id_carrier`, `id_lang`, `id_customer`, `u_kkid`, `id_cart`, `id_currency`, `id_address_delivery`, `id_address_invoice`, `current_state`, `secure_key`, `payment`, `conversion_rate`, `module`, `recyclable`, `gift`, `gift_message`, `mobile_theme`, `shipping_number`, `total_discounts`, `total_discounts_tax_incl`, `total_discounts_tax_excl`, `total_paid`, `total_paid_tax_incl`, `total_paid_tax_excl`, `total_paid_real`, `total_products`, `total_products_wt`, `total_shipping`, `total_shipping_tax_incl`, `total_shipping_tax_excl`, `carrier_tax_rate`, `total_wrapping`, `total_wrapping_tax_incl`, `total_wrapping_tax_excl`, `round_mode`, `round_type`, `invoice_number`, `delivery_number`, `invoice_date`, `delivery_date`, `valid`, `c_kkid`, `c_value`, `ref_kkid`,`date_add`, `date_upd` from `s_orders` where `kkid` = ? and `id_customer` = ? ;";
            $sql = "select `id_order`, `kkid`, `id_carrier`, `id_customer`, `u_kkid`, `id_cart`, `id_address_delivery`, `current_state`, `payment`,  `total_discounts`, `total_paid`, `invoice_date`, `reference`, `total_paid_real`, `total_products`, `valid`, `c_kkid`, `shipping_number`, `pick_code`, `cd_key`, `c_value`, `ref_doctor_id`, `ref_kkid`,`date_add`, `date_upd` from `s_orders` where `kkid` = ? and `id_customer` = ? ;";
			//Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array("$o_kkid", "$id_customer"));
            
            $row = $stmt->fetch();
            $bll_carrier = new Bll_Carrier_Info();
            if(isset($row['id_customer']) && !empty($row['id_customer'])){
                $id_order = $row['id_order'];
                $product_list = array();
                $total_price = 0;
                $product_list = self::get_order_detail_list($id_order);

                $id_address_delivery = $row['id_address_delivery'];
                $address_delivery = self::get_order_address($id_address_delivery);
            
                $order_reduction_price = 0; //reduction 优惠金额
                $order_wholesale_price = 0; //订单市场价总金额
                $order_price_allowed_coupon = 0; //可以使用优惠券的商品总金额
                foreach($product_list as $k=>$p){

                   if($p['original_wholesale_price'] > $p['original_product_price']){
                       $product_reduction_price = ($p['original_wholesale_price'] - $p['original_product_price']) * $p['product_quantity'] ;
                       $order_reduction_price  += $product_reduction_price;
                   }

                   $order_wholesale_price  += $p['original_wholesale_price'] * $p['product_quantity'];
                   if(isset($p['product_total_price_coupon']) && !empty($p['product_total_price_coupon'])){
                       $order_price_allowed_coupon += $p['product_total_price_coupon'];
                   }


                }
                $row['order_product_list'] = $product_list;
                $row['address_delivery'] = $address_delivery;
                $row['order_reduction_price'] = $order_reduction_price;
                $row['order_wholesale_price'] = $order_wholesale_price;
                $row['order_price_allowed_coupon'] = $order_price_allowed_coupon;
                //Logger::info(__FILE__, __CLASS__, __LINE__, "order_price_allowed_coupon : " . $row['order_price_allowed_coupon']);
				//Logger::info(__FILE__, __CLASS__, __LINE__, "city : ". $address_delivery['city']);
                $fee = $bll_carrier->get_carrier_fee($address_delivery['city'] ? $address_delivery['city'] : explode(" ", $address_delivery['address1'])[1]);
                $row['carrier_fee'] = $fee;

            }
            if(empty($row)){
               $row = array();
            }
            return $row;
        }

        public function get_order_by_customer_list($id_customer, $current_state, $page_size, $page_start) { 
            $row = array();
            //$sql = "select `id_order`, `reference`, `kkid`, `id_shop_group`, `id_shop`, `id_carrier`, `id_lang`, `id_customer`, `u_kkid`, `id_cart`, `id_currency`, `id_address_delivery`, `id_address_invoice`, `current_state`, `secure_key`, `payment`, `conversion_rate`, `module`, `recyclable`, `gift`, `gift_message`, `mobile_theme`, `shipping_number`, `total_discounts`, `total_discounts_tax_incl`, `total_discounts_tax_excl`, `total_paid`, `total_paid_tax_incl`, `total_paid_tax_excl`, `total_paid_real`, `total_products`, `total_products_wt`, `total_shipping`, `total_shipping_tax_incl`, `total_shipping_tax_excl`, `carrier_tax_rate`, `total_wrapping`, `total_wrapping_tax_incl`, `total_wrapping_tax_excl`, `round_mode`, `round_type`, `invoice_number`, `delivery_number`, `invoice_date`, `delivery_date`, `valid`, `c_kkid`, `c_value`, `ref_kkid`, `date_add`, `date_upd` from `s_orders` where `id_customer` = :id_customer and `current_state` = :current_state order by id_order desc LIMIT :limit OFFSET :offset;;";
            $sql = "select `id_order`, `kkid`, `id_carrier`, `id_customer`, `u_kkid`, `id_cart`, `id_address_delivery`, `current_state`, `payment`, `total_discounts`, `total_paid`, `invoice_date`, `reference`, `total_paid_real`, `total_products`, `delivery_number`, `shipping_number`, `pick_code`, `valid`, `c_kkid`, `c_value`, `ref_kkid`, `ref_doctor_id`, `date_add`, `date_upd` from `s_orders` where `id_customer` = :id_customer and `current_state` in (:current_state) order by id_order desc LIMIT :limit OFFSET :offset;";
			Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
            Logger::info(__FILE__, __CLASS__, __LINE__, "id_customer : ".$id_customer);
            Logger::info(__FILE__, __CLASS__, __LINE__, "current_state111 : ".$current_state);
			try { 
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_customer', $id_customer, PDO::PARAM_INT);
            $stmt->bindParam(':current_state', $current_state);
            $stmt->bindParam(':limit', $page_size, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $page_start, PDO::PARAM_INT);
			//$data = array('id_customer'=>$id_customer,'current_state'=>$current_state,'limit'=>$page_size,"offset"=>$page_start);
            $stmt->execute();
            $product_list = array();
            $rows = $stmt->fetchAll();
			}
			catch (Exception $e)
			{
				Logger::info(__FILE__, __CLASS__, __LINE__, $e->getMessage());
			}
			Logger::info(__FILE__, __CLASS__, __LINE__, var_export($rows,true)."$page_size========$page_start");

            $bll_carrier = new Bll_Carrier_Info();
            foreach($rows as $k=>$row){

                $id_order = $row['id_order'];
                $product_list = self::get_order_detail_list($id_order);
                $row['order_product_list'] = $product_list;
                // total_paid
                // c_value
                if($row['c_value'] > 0){
                  $row['total_paid_real'] = $row['total_paid_real'] - $row['c_value'];
                  $row['total_paid'] = $row['total_paid'] - $row['c_value'];
                }
                if($row['total_paid']<=0){
                  $row['total_paid'] = 1;
                }
                if($row['total_paid_real']<=0){
                  $row['total_paid_real'] = 1;
                }
                  //$row['total_paid'] = -1001;
                  //$row['total_paid_tax_incl'] = -1002;
                  //$row['total_paid_real'] = -1003;

                $id_address_delivery = $row['id_address_delivery'];
                $address_delivery = self::get_order_address($id_address_delivery);
                $row['address_delivery'] = $address_delivery;
                $fee = $bll_carrier->get_carrier_fee($address_delivery['city'] ? $address_delivery['city'] : explode(" ", $address_delivery['address1'])[1]);
                $row['carrier_fee'] = $fee;

                $rows[$k] = $row;
            }

            if(empty($rows)){
               $rows = array();
            }
            return $rows;
        }

        public function get_order_by_customer_list_admin($current_state, $page_size, $page_start) { 
            $row = array();
            $sql = "select `id_order`, `reference`, `kkid`, `id_shop_group`, `id_shop`, `id_carrier`, `id_lang`, `id_customer`, `u_kkid`, `id_cart`, `id_currency`, `id_address_delivery`, `id_address_invoice`, `current_state`, `secure_key`, `payment`, `conversion_rate`, `module`, `recyclable`, `gift`, `gift_message`, `mobile_theme`, `shipping_number`, `pick_code`, `cd_key`, `total_discounts`, `total_discounts_tax_incl`, `total_discounts_tax_excl`, `total_paid`, `total_paid_tax_incl`, `total_paid_tax_excl`, `total_paid_real`, `total_products`, `total_products_wt`, `total_shipping`, `total_shipping_tax_incl`, `total_shipping_tax_excl`, `carrier_tax_rate`, `total_wrapping`, `total_wrapping_tax_incl`, `total_wrapping_tax_excl`, `round_mode`, `round_type`, `invoice_number`, `delivery_number`, `invoice_date`, `delivery_date`, `valid`, `c_kkid`, `c_value`, `ref_kkid`, `date_add`, `date_upd` from `s_orders` where `current_state` = :current_state and date_add > DATE_ADD(now(), INTERVAL -1440 minute) order by id_order desc LIMIT :limit OFFSET :offset;;";
            Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':current_state', $current_state, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $page_size, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $page_start, PDO::PARAM_INT);
            $stmt->execute();

            $product_list = array();
            $rows = $stmt->fetchAll();
            $bll_carrier = new Bll_Carrier_Info();
            foreach($rows as $k=>$row){

                $id_order = $row['id_order'];
                $product_list = self::get_order_detail_list($id_order);
                $row['order_product_list'] = $product_list;

                $id_address_delivery = $row['id_address_delivery'];
                $address_delivery = self::get_order_address($id_address_delivery);
                $row['address_delivery'] = $address_delivery;
                $fee = $bll_carrier->get_carrier_fee($address_delivery['city'] ? $address_delivery['city'] : explode(" ", $address_delivery['address1'])[1]);
				
                $row['carrier_fee'] = $fee;

                $rows[$k] = $row;
            }

            if(empty($rows)){
               $rows = array();
            }
            return $rows;
        }

        public function create_order_detail($data) {
            $sql = "insert into `s_order_detail` (`id_order_detail`, `id_order`, `id_order_invoice`, `id_warehouse`, `id_shop`, `product_id`, `product_attribute_id`, `id_customization`, `product_name`, `product_quantity`, `product_quantity_in_stock`, `product_quantity_refunded`, `product_quantity_return`, `product_quantity_reinjected`, `product_price`, `reduction_percent`, `reduction_amount`, `reduction_amount_tax_incl`, `reduction_amount_tax_excl`, `group_reduction`, `product_quantity_discount`, `product_ean13`, `product_isbn`, `product_upc`, `product_reference`, `product_supplier_reference`, `product_weight`, `id_tax_rules_group`, `tax_computation_method`, `tax_name`, `tax_rate`, `ecotax`, `ecotax_tax_rate`, `discount_quantity_applied`, `download_hash`, `download_nb`, `download_deadline`, `total_price_tax_incl`, `total_price_tax_excl`, `unit_price_tax_incl`, `unit_price_tax_excl`, `total_shipping_price_tax_incl`, `total_shipping_price_tax_excl`, `purchase_supplier_price`, `original_product_price`, `original_wholesale_price`) values(:id_order_detail, :id_order, :id_order_invoice, :id_warehouse, :id_shop, :product_id, :product_attribute_id, :id_customization, :product_name, :product_quantity, :product_quantity_in_stock, :product_quantity_refunded, :product_quantity_return, :product_quantity_reinjected, :product_price, :reduction_percent, :reduction_amount, :reduction_amount_tax_incl, :reduction_amount_tax_excl, :group_reduction, :product_quantity_discount, :product_ean13, :product_isbn, :product_upc, :product_reference, :product_supplier_reference, :product_weight, :id_tax_rules_group, :tax_computation_method, :tax_name, :tax_rate, :ecotax, :ecotax_tax_rate, :discount_quantity_applied, :download_hash, :download_nb, :download_deadline, :total_price_tax_incl, :total_price_tax_excl, :unit_price_tax_incl, :unit_price_tax_excl, :total_shipping_price_tax_incl, :total_shipping_price_tax_excl, :purchase_supplier_price, :original_product_price, :original_wholesale_price);";
            $stmt = $this->pdo->prepare($sql);
            $res = $stmt->execute($data);
            $last_id = $this->pdo->lastInsertId();
            return $last_id;
        }

        public function create_order_history($data) {
            $sql = "insert into `s_order_history` (`id_order_history`, `id_employee`, `id_order`, `id_order_state`, `date_add`) values(:id_order_history, :id_employee, :id_order, :id_order_state, :date_add);";
            $stmt = $this->pdo->prepare($sql);
            $res = $stmt->execute($data);
            $last_id = $this->pdo->lastInsertId();
            return $last_id;
        }

        public function get_order_detail($id_order_detail) {
            $row = array();
            //$sql = "select `id_order_detail`, `id_order`, `id_order_invoice`, `id_warehouse`, `id_shop`, `product_id`, `product_attribute_id`, `id_customization`, `product_name`, `product_quantity`, `product_quantity_in_stock`, `product_quantity_refunded`, `product_quantity_return`, `product_quantity_reinjected`, `product_price`, `reduction_percent`, `reduction_amount`, `reduction_amount_tax_incl`, `reduction_amount_tax_excl`, `group_reduction`, `product_quantity_discount`, `product_ean13`, `product_isbn`, `product_upc`, `product_reference`, `product_supplier_reference`, `product_weight`, `id_tax_rules_group`, `tax_computation_method`, `tax_name`, `tax_rate`, `ecotax`, `ecotax_tax_rate`, `discount_quantity_applied`, `download_hash`, `download_nb`, `download_deadline`, `total_price_tax_incl`, `total_price_tax_excl`, `unit_price_tax_incl`, `unit_price_tax_excl`, `total_shipping_price_tax_incl`, `total_shipping_price_tax_excl`, `purchase_supplier_price`, `original_product_price`, `original_wholesale_price`, `commented` from `s_order_detail` where `id_order_detail` = ? ;";
            $sql = "select `id_order_detail`, `id_order`, `reduction_amount`, `product_id`, `product_attribute_id`, `product_name`, `product_quantity`, `product_price`,  `product_quantity_discount`, `purchase_supplier_price`, `original_product_price`, `original_wholesale_price`, `commented` from `s_order_detail` where `id_order_detail` = ? ;";
			//Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array("$id_order_detail"));
            
            $row = $stmt->fetch();
            $bll_product = new Bll_Product_Info();
            if(isset($row['id_order']) && empty($row['id_order'])){
                $id_product = $row['product_id'];
                $product_attribute_id = $row['product_attribute_id'];
                $product = $bll_product->get_product_by_id($id_product); // product object
                $product_attribute_list = self::get_product_attribute_list($id_product, $product_attribute_id);
                $row['p_info'] = $product;
                $row['product_attribute'] = $product_attribute_list;
/*
                $id_order = $row['id_order'];
                $product_list = array();
                $total_price = 0;
                foreach($product_list as $k=>$p){
                    if($p['selected']){
                      $total_price += $p['item_total_price'];
                    }
                }
                $row['product_list'] = $product_list;
                $row['total_price'] = $total_price;
*/
            }
            if(empty($row)){
               $row = array();
            }
            return $row;
        }

        public function set_order_detail($id_order, $data) {
            $data['id_order'] = $id_order;
            $sql = "update `s_order_detail` set `id_order_detail` = :id_order_detail, `id_order` = :id_order, `id_order_invoice` = :id_order_invoice, `id_warehouse` = :id_warehouse, `id_shop` = :id_shop, `product_id` = :product_id, `product_attribute_id` = :product_attribute_id, `id_customization` = :id_customization, `product_name` = :product_name, `product_quantity` = :product_quantity, `product_quantity_in_stock` = :product_quantity_in_stock, `product_quantity_refunded` = :product_quantity_refunded, `product_quantity_return` = :product_quantity_return, `product_quantity_reinjected` = :product_quantity_reinjected, `product_price` = :product_price, `reduction_percent` = :reduction_percent, `reduction_amount` = :reduction_amount, `reduction_amount_tax_incl` = :reduction_amount_tax_incl, `reduction_amount_tax_excl` = :reduction_amount_tax_excl, `group_reduction` = :group_reduction, `product_quantity_discount` = :product_quantity_discount, `product_ean13` = :product_ean13, `product_isbn` = :product_isbn, `product_upc` = :product_upc, `product_reference` = :product_reference, `product_supplier_reference` = :product_supplier_reference, `product_weight` = :product_weight, `id_tax_rules_group` = :id_tax_rules_group, `tax_computation_method` = :tax_computation_method, `tax_name` = :tax_name, `tax_rate` = :tax_rate, `ecotax` = :ecotax, `ecotax_tax_rate` = :ecotax_tax_rate, `discount_quantity_applied` = :discount_quantity_applied, `download_hash` = :download_hash, `download_nb` = :download_nb, `download_deadline` = :download_deadline, `total_price_tax_incl` = :total_price_tax_incl, `total_price_tax_excl` = :total_price_tax_excl, `unit_price_tax_incl` = :unit_price_tax_incl, `unit_price_tax_excl` = :unit_price_tax_excl, `total_shipping_price_tax_incl` = :total_shipping_price_tax_incl, `total_shipping_price_tax_excl` = :total_shipping_price_tax_excl, `purchase_supplier_price` = :purchase_supplier_price, `original_product_price` = :original_product_price, `original_wholesale_price` = :original_wholesale_price where `id_order_detail` = :id_order_detail ;";
            $stmt = $this->pdo->prepare($sql);
            $res = $stmt->execute($data);
            return $res;
        }

        public function create_order_address($data) {
            $sql = "insert into `s_address` (`id_address`, `id_country`, `id_state`, `id_customer`, `id_manufacturer`, `id_supplier`, `id_warehouse`, `alias`, `company`, `lastname`, `firstname`, `address1`, `address2`, `postcode`, `city`, `other`, `phone`, `phone_mobile`, `vat_number`, `dni`, `date_add`, `date_upd`, `active`, `deleted`) values(:id_address, :id_country, :id_state, :id_customer, :id_manufacturer, :id_supplier, :id_warehouse, :alias, :company, :lastname, :firstname, :address1, :address2, :postcode, :city, :other, :phone, :phone_mobile, :vat_number, :dni, :date_add, :date_upd, :active, :deleted);";
            $stmt = $this->pdo->prepare($sql);
            $res = $stmt->execute($data);
            $last_id = $this->pdo->lastInsertId();
            return $last_id;
        }

        public function set_order_address($id_customer, $data) {
            $data['id_order'] = $id_order;
            $sql = "update `s_address` set `id_address` = :id_address, `id_country` = :id_country, `id_state` = :id_state, `id_customer` = :id_customer, `id_manufacturer` = :id_manufacturer, `id_supplier` = :id_supplier, `id_warehouse` = :id_warehouse, `alias` = :alias, `company` = :company, `lastname` = :lastname, `firstname` = :firstname, `address1` = :address1, `address2` = :address2, `postcode` = :postcode, `city` = :city, `other` = :other, `phone` = :phone, `phone_mobile` = :phone_mobile, `vat_number` = :vat_number, `dni` = :dni, `date_add` = :date_add, `date_upd` = :date_upd, `active` = :active, `deleted` = :deleted where `id_address` = :id_address and `id_customer` = :id_customer ;";
            $stmt = $this->pdo->prepare($sql);
            $res = $stmt->execute($data);
            return $res;
        }

        public function get_order_address($id_address) {
            $sql = "select `id_address`, `id_country`, `id_state`, `id_customer`, `id_manufacturer`, `id_supplier`, `id_warehouse`, `alias`, `company`, `lastname`, `firstname`, `address1`, `address2`, `postcode`, `city`, `other`, `phone`, `phone_mobile`, `vat_number`, `dni`, `date_add`, `date_upd`, `active`, `deleted` from `s_address` where `id_address` = ? ;";
	        //$sql = "select `id_address`, `id_state`, `id_customer`, `lastname`, `firstname`, `address1`, `address2`, `postcode`, `city`, `other`, `phone`, `phone_mobile`, `active`, `deleted` from `s_address` where `id_address` = ? ;";    
			$stmt = $this->pdo->prepare($sql);
            $stmt->execute(array("$id_address"));
            
            $row = $stmt->fetch();
            return $row;
        }

        public function get_order_address_by_customer_list($id_customer) {
            //$sql = "select `id_address`, `id_country`, `id_state`, `id_customer`, `id_manufacturer`, `id_supplier`, `id_warehouse`, `alias`, `company`, `lastname`, `firstname`, `address1`, `address2`, `postcode`, `city`, `other`, `phone`, `phone_mobile`, `vat_number`, `dni`, `date_add`, `date_upd`, `active`, `deleted` from `s_address` where `id_customer` = ? and deleted = 0 order by id_address desc limit 10;";
            $sql = "select `id_address`, `id_state`, `id_customer`, `lastname`, `firstname`, `address1`, `address2`, `postcode`, `city`, `other`, `phone`, `phone_mobile`, `date_add`, `date_upd`, `active`, `deleted` from `s_address` where `id_customer` = ? and deleted = 0 order by id_address desc limit 10;";
			$stmt = $this->pdo->prepare($sql);
            $stmt->execute(array("$id_customer"));
            $rows = $stmt->fetchAll();
            foreach($rows as $k=>$j){
                $rows[$k] = $j;
            }
            return $rows;
        }

        public function get_order_detail_list($id_order) {
            Logger::info(__FILE__, __CLASS__, __LINE__, "id_order : ". $id_order);
            $row = array();
            //$sql = "select `id_order_detail`, `id_order`, `id_order_invoice`, `id_warehouse`, `id_shop`, `product_id`, `product_attribute_id`, `id_customization`, `product_name`, `product_quantity`, `product_quantity_in_stock`, `product_quantity_refunded`, `product_quantity_return`, `product_quantity_reinjected`, `product_price`, `reduction_percent`, `reduction_amount`, `reduction_amount_tax_incl`, `reduction_amount_tax_excl`, `group_reduction`, `product_quantity_discount`, `product_ean13`, `product_isbn`, `product_upc`, `product_reference`, `product_supplier_reference`, `product_weight`, `id_tax_rules_group`, `tax_computation_method`, `tax_name`, `tax_rate`, `ecotax`, `ecotax_tax_rate`, `discount_quantity_applied`, `download_hash`, `download_nb`, `download_deadline`, `total_price_tax_incl`, `total_price_tax_excl`, `unit_price_tax_incl`, `unit_price_tax_excl`, `total_shipping_price_tax_incl`, `total_shipping_price_tax_excl`, `purchase_supplier_price`, `original_product_price`, `original_wholesale_price`, `commented` from `s_order_detail` where `id_order` = ? ;";
           	$sql = "select `id_order_detail`, `id_order`, `product_id`, `product_attribute_id`,  `product_name`, `product_quantity`, `product_price`, `reduction_percent`, `reduction_amount`, `purchase_supplier_price`, `original_product_price`, `original_wholesale_price`, `commented` from `s_order_detail` where `id_order` = ? ;"; 
			//Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array("$id_order"));
            $rows = $stmt->fetchAll();
            $bll_product = new Bll_Product_Info();
            $id_product = 0;
            //Logger::info(__FILE__, __CLASS__, __LINE__, "id_product : ". $id_product);
            //Logger::info(__FILE__, __CLASS__, __LINE__, var_export($rows, true));
            foreach($rows as $k=>$j){
                $id_product = $j['product_id'];
                //Logger::info(__FILE__, __CLASS__, __LINE__, "id_product : ". $id_product);
                $product_attribute_id = $j['product_attribute_id'];
                
                $product = $bll_product->get_product_by_id($id_product); // product object
                
                //Logger::info(__FILE__, __CLASS__, __LINE__, var_export($product, true));
                $product_attribute_list = self::get_product_attribute_list($id_product, $product_attribute_id);
                $j['p_info'] = $product;
                $j['product_attribute'] = $product_attribute_list;
                if(isset($product['allowed_coupon']) && $product['allowed_coupon'] == 1){  // allowed coupon
                   $j['product_total_price_coupon'] = $j['product_quantity'] * $product['price'];
                   //Logger::info(__FILE__, __CLASS__, __LINE__, "product_price : ". $product['price']);
                   //Logger::info(__FILE__, __CLASS__, __LINE__, "product_quantity : ". $j['product_quantity']);
                   //Logger::info(__FILE__, __CLASS__, __LINE__, "product_total_price_coupon : ". $j['product_total_price_coupon']);
                }
                $rows[$k] = $j;
            }
            return $rows;
        }

        public function get_product_attribute_list($id_product, $id_product_attribute) {
            if($id_product_attribute == 0) return array();
            $row = array();
            $sql = "SELECT a.`id_attribute`, a.`id_attribute_group`, al.`name`, agl.`name` as `group`, pa.`reference`, pa.`ean13`, pa.`isbn`,pa.`upc` FROM `s_attribute` a LEFT JOIN `s_attribute_lang` al ON (al.`id_attribute` = a.`id_attribute` AND al.`id_lang` = 1) LEFT JOIN `s_product_attribute_combination` pac ON (pac.`id_attribute` = a.`id_attribute`) LEFT JOIN `s_product_attribute` pa ON (pa.`id_product_attribute` = pac.`id_product_attribute`) INNER JOIN s_product_attribute_shop product_attribute_shop ON (product_attribute_shop.id_product_attribute = pa.id_product_attribute AND product_attribute_shop.id_shop = 1) LEFT JOIN `s_attribute_group_lang` agl ON (a.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = 1) WHERE pa.`id_product` = ? AND pac.`id_product_attribute` = ? AND agl.`id_lang` = 1;";
            //Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array("$id_product", "$id_product_attribute"));
            $rows = $stmt->fetchAll();
            $bll_product = new Bll_Product_Info();
            foreach($rows as $k=>$j){
                $rows[$k] = $j;
            }
            //Logger::info(__FILE__, __CLASS__, __LINE__, "=======get_product_attribute_list======");
            //Logger::info(__FILE__, __CLASS__, __LINE__, var_export($rows, true));
            return $rows;
        }

        public function set_order_paystatus_by_kkid($o_kkid, $id_customer, $current_state) {
                //
                $data = array();
                $data['id_customer'] = $id_customer;
                $data['kkid'] = $o_kkid;
                $data['current_state'] = $current_state;
                $data['date_upd'] = date('Y-m-d H:i:s');
                $sql = "update `s_orders` set `current_state` = :current_state, `date_upd` = :date_upd where `kkid` = :kkid and id_customer = :id_customer ;";
                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute($data);
        }

	   public function set_pick_status($data){
	   			if(!$data) return array();
				$sql = "update s_orders set pick_status = :pick_status where id_order = :id_order;";
				$stmt = $this->pdo->prepare($sql);
				return $stmt->execute($data);
	   }
/*
*/
}
