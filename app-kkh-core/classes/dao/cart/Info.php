<?php
apf_require_class("APF_DB_Factory");

class Dao_Cart_Info {

	private $pdo;

	public function __construct() {
		$this->pdo = APF_DB_Factory::get_instance()->get_pdo("shop_master");
	}

        public function create_cart($data) {
                $sql = "insert into `s_cart` (`id_cart`, `id_shop_group`, `id_shop`, `id_carrier`, `delivery_option`, `id_lang`, `id_address_delivery`, `id_address_invoice`, `id_currency`, `id_customer`, `id_guest`, `secure_key`, `recyclable`, `gift`, `gift_message`, `mobile_theme`, `allow_seperated_package`, `date_add`, `date_upd`, `checkout_session_data`) values(:id_cart, :id_shop_group, :id_shop, :id_carrier, :delivery_option, :id_lang, :id_address_delivery, :id_address_invoice, :id_currency, :id_customer, :id_guest, :secure_key, :recyclable, :gift, :gift_message, :mobile_theme, :allow_seperated_package, :date_add, :date_upd, :checkout_session_data);";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                $last_id = $this->pdo->lastInsertId();
                return $last_id;
        }

        public function set_cart($id_cart, $data) {
                $data['id_cart'] = $id_cart;
                $sql = "update `s_cart` set `id_cart` = :id_cart, `id_shop_group` = :id_shop_group, `id_shop` = :id_shop, `id_carrier` = :id_carrier, `delivery_option` = :delivery_option, `id_lang` = :id_lang, `id_address_delivery` = :id_address_delivery, `id_address_invoice` = :id_address_invoice, `id_currency` = :id_currency, `id_customer` = :id_customer, `id_guest` = :id_guest, `secure_key` = :secure_key, `recyclable` = :recyclable, `gift` = :gift, `gift_message` = :gift_message, `mobile_theme` = :mobile_theme, `allow_seperated_package` = :allow_seperated_package, `date_add` = :date_add, `date_upd` = :date_upd, `checkout_session_data` = :checkout_session_data where `id_cart` = :id_cart ;";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                return $res;
        }

		public function get_attribute_config($id_product) {
				$sql = "select attribute_config from s_product_attribute where id_product_attribute = ? limit 1;";
				$stmt = $this->pdo->prepare($sql);
				$stmt->execute(array($id_product));
				$res = $stmt->fetch();
				return $res;
		}

		public function clear_all_cart_selected_product($id_cart){
				$sql = "delete from s_cart_product where id_cart = ? and selected = 1;";
				$stmt = $this->pdo->prepare($sql);
				$res = $stmt->execute(array($id_cart));
				return $res;
		}

        public function clear_cart($id_cart) {
                $sql = "delete from `s_cart_product` where `id_cart` = ? ;";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute(array("$id_cart"));
                return $res;
        }

        public function get_cart($id_cart) {
                $row = array();
                $sql = "select `id_cart`, `id_shop_group`, `id_shop`, `id_carrier`, `delivery_option`, `id_lang`, `id_address_delivery`, `id_address_invoice`, `id_currency`, `id_customer`, `id_guest`, `secure_key`, `recyclable`, `gift`, `gift_message`, `mobile_theme`, `allow_seperated_package`, `date_add`, `date_upd`, `checkout_session_data` from `s_cart` where `id_cart` = ? ;";
                Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$id_cart"));
                
                $row = $stmt->fetch();
                if(isset($row['id_cart']) && empty($row['id_cart'])){
                    $id_cart = $row['id_cart'];
                    $product_list = self::get_cart_product_list($id_cart);
                    $total_price = 0;
                    foreach($product_list as $k=>$p){
                        if($p['selected']){
                          $total_price += $p['item_total_price'];
                          $total_org_price += $p['item_total_org_price'];
                        }
                    }
                    $row['product_list'] = $product_list;
                    $row['total_price'] = $total_price;
                    $row['total_org_price'] = $total_org_price;
                }
                if(empty($row)){
                   $row = array();
                }
                return $row;
        }

		public function get_customer_cart_by_id_customer($id_customer) {
				$row = array();
				$sql = "select `id_cart`, `id_customer` from `s_cart` where `id_customer` = ? ;";
				$stmt = $this->pdo->prepare($sql);
				$stmt->execute(array("$id_customer"));
				$row = $stmt->fetch();

				if(isset($row['id_customer']) && !empty($row['id_customer'])){
					$id_cart = $row['id_cart'];
					$product_list = self::get_mycart_product_list($id_cart);
					$total_price = 0;	
					foreach($product_list as $k=>$p){
						if($p['selected']){
							$total_price += $p['item_total_price'];
							$total_org_price += $p['item_total_org_price'];
						}	
					}
					$row['product_list'] = $product_list;
					$row['total_price'] = $total_price;
					$row['total_org_price'] = $total_org_price;
				}

				if (empty($row)) {
					$row = array();
				}
				return $row;
		}

        public function get_cart_by_customer($id_customer) {
                $row = array();
                $sql = "select `id_cart`, `id_shop_group`, `id_shop`, `id_carrier`, `delivery_option`, `id_lang`, `id_address_delivery`, `id_address_invoice`, `id_currency`, `id_customer`, `id_guest`, `secure_key`, `recyclable`, `gift`, `gift_message`, `mobile_theme`, `allow_seperated_package`, `date_add`, `date_upd`, `checkout_session_data` from `s_cart` where `id_customer` = ? ;";
                //Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$id_customer"));
                
                $row = $stmt->fetch();
                if(isset($row['id_customer']) && !empty($row['id_customer'])){
                    $id_cart = $row['id_cart'];
                    $product_list = self::get_cart_product_list($id_cart);
                    $total_price = 0;
					$total_org_price = 0;
                    foreach($product_list as $k=>$p){
                        if($p['selected']){
                          $total_price += $p['item_total_price'];
                          $total_org_price += $p['item_total_org_price'];
                        }
                    }
                    $row['product_list'] = $product_list;
                    $row['total_price'] = $total_price;
                    $row['total_org_price'] = $total_org_price;
                }
                if(empty($row)){
                   $row = array();
                }
                return $row;
        }

        public function get_cart_by_guest($id_guest) {
                $row = array();
                $sql = "select `id_cart`, `id_shop_group`, `id_shop`, `id_carrier`, `delivery_option`, `id_lang`, `id_address_delivery`, `id_address_invoice`, `id_currency`, `id_customer`, `id_guest`, `secure_key`, `recyclable`, `gift`, `gift_message`, `mobile_theme`, `allow_seperated_package`, `date_add`, `date_upd`, `checkout_session_data` from `s_cart` where `id_guest` = ? ;";
                Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$id_guest"));
                
                $row = $stmt->fetch();
                if(isset($row['id_guest']) && !empty($row['id_guest'])){
                    $id_cart = $row['id_cart'];
                    $product_list = self::get_cart_product_list($id_cart);
                    $total_price = 0;
					$total_org_price = 0;
                    foreach($product_list as $k=>$p){
                        if($p['selected'] == 1){
                          $total_price += $p['item_total_price'];
                          $total_org_price += $p['item_total_org_price'];
                        }
                    }
                    $row['product_list'] = $product_list;
                    $row['total_price'] = $total_price;
                    $row['total_org_price'] = $total_org_price;
                }
                if(empty($row)){
                   $row = array();
                }
                return $row;
        }

		private function get_mycart_product_list($id_cart) {
			$sql = "select `id_cart`, `id_product`, `id_address_delivery`, `id_product_attribute`, `quantity`, `date_add` from `s_cart_product` where `id_cart` = :id_cart order by date_add desc;";
			$stmt = $this->pdo->prepare($sql);
			$stmt->bindParam(':id_cart', $id_cart, PDO::PARAM_INT);
			$stmt->execute();
			$jobs = $stmt->fetchAll();
			$job = array();
			$bll_product = new Bll_Product_Info();
			$bll_order = new Bll_Order_Info();
			$price = ['price', 'wholesale_price', 'jd_price', 'tb_price'];
			foreach($jobs as $k=>$j){
                $id_product_attribute = $j['id_product_attribute'];
                $id_product = $j['id_product'];
                $product = $bll_product->get_product_by_id($id_product); // product object  //allowed_coupon
                $product_attribute_list = $bll_order->get_product_attribute_list($id_product, $id_product_attribute);
                //Logger::info(__FILE__, __CLASS__, __LINE__, var_export($product_attribute_list, true));
                if (!empty($product)) {
				//	foreach($product as $kk=>$vv) {
						$product['madein'] = $bll_product->madeinId2countryDetail($product['madein']);
						$product['kkhtags'] = empty($product['kkhtags']) ? array() : explode('||', $product['kkhtags']);
						foreach($price as $m=>$t) {
							$product[$t] = (float)$product[$t]; 
						}
				//	}
				}
				$j['p_info'] = $product;
                $j['product_attribute'] = $product_attribute_list;
                $j['item_total_price'] = $j['quantity'] * $product['price'];
                $j['item_total_org_price'] = $j['quantity'] * $product['wholesale_price'];
                if(isset($product['allowed_coupon']) && $product['allowed_coupon'] == 1){  // allowed coupon
                   $j['item_total_price_coupon'] = $j['quantity'] * $product['price'];
                   $j['item_total_org_price_coupon'] = $j['quantity'] * $product['wholesale_price'];
                }
                if($product['active'] == 0){
                   $j['selected'] = 0;
				   continue;
                }
                $job[$k] = $j;
			}
			return $job;			
		}

        private function get_cart_product_list($id_cart) {
            $sql = "select `id_cart`, `id_product`, `id_address_delivery`, `id_shop`, `id_product_attribute`, `id_customization`, `quantity`, `selected`, `date_add` from `s_cart_product` where `id_cart` = :id_cart order by date_add desc;";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_cart', $id_cart, PDO::PARAM_INT);
            $stmt->execute();
            $jobs = $stmt->fetchAll();
            $job = array();
            $bll_product = new Bll_Product_Info();
            $bll_order = new Bll_Order_Info();
            foreach($jobs as $k=>$j){
                $id_product_attribute = $j['id_product_attribute'];
                $id_product = $j['id_product'];
                $product = $bll_product->get_product_by_id($id_product); // product object  //allowed_coupon
                $product_attribute_list = $bll_order->get_product_attribute_list($id_product, $id_product_attribute);
                //Logger::info(__FILE__, __CLASS__, __LINE__, var_export($product_attribute_list, true));
                $j['p_info'] = $product;
                $j['product_attribute'] = $product_attribute_list;
                $j['item_total_price'] = $j['quantity'] * $product['price'];
                $j['item_total_org_price'] = $j['quantity'] * $product['wholesale_price'];
                if(isset($product['allowed_coupon']) && $product['allowed_coupon'] == 1){  // allowed coupon
                   $j['item_total_price_coupon'] = $j['quantity'] * $product['price'];
                   $j['item_total_org_price_coupon'] = $j['quantity'] * $product['wholesale_price'];
                }
                if($product['active'] == 0){
                   $j['selected'] = 0;
                }
                $job[$k] = $j;
            }
            return $job;
        }

        public function get_cart_product($id_cart, $id_product, $id_product_attribute = 0) {
            $wh1 = "";
            if($id_product_attribute){
                $wh1 = "and `id_product_attribute` = :id_product_attribute ";
            }
            $sql = "select `id_cart`, `id_product`, `id_address_delivery`, `id_shop`, `id_product_attribute`, `id_customization`, `quantity`, `date_add` from `s_cart_product` where `id_cart` = :id_cart and `id_product` = :id_product $wh1 limit 1;";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_cart', $id_cart, PDO::PARAM_INT);
            $stmt->bindParam(':id_product', $id_product, PDO::PARAM_INT);
            if($id_product_attribute){
                $stmt->bindParam(':id_product_attribute', $id_product_attribute, PDO::PARAM_INT);
            }
            $stmt->execute();
            $row = $stmt->fetch();
            if(empty($row)){
               $row = array();
            }
            return $row;
        }

        public function add_cart_product($data) {
                $sql = "insert into `s_cart_product` (`id_cart`, `id_product`, `id_address_delivery`, `id_shop`, `id_product_attribute`, `id_customization`, `quantity`, `selected`, `date_add`) values(:id_cart, :id_product, :id_address_delivery, :id_shop, :id_product_attribute, :id_customization, :quantity, :selected, :date_add);";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                $last_id = $this->pdo->lastInsertId();
                return $last_id;
        }

        public function set_cart_product($id_cart, $id_product, $data) {
                $data['id_cart'] = $id_cart;
                $data['id_product'] = $id_product;
                $sql = "update `s_cart_product` set `id_cart` = :id_cart, `id_product` = :id_product, `id_address_delivery` = :id_address_delivery, `id_shop` = :id_shop, `id_product_attribute` = :id_product_attribute, `id_customization` = :id_customization, `quantity` = :quantity, `selected` = :selected, `date_add` = :date_add where `id_cart` = :id_cart and `id_product` = :id_product and `id_product_attribute` = :id_product_attribute;";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                return $res;
        }

        public function del_cart_product($id_cart, $id_product, $id_product_attribute = 0) {
                $data['id_cart'] = $id_cart;
                $data['id_product'] = $id_product;
                $sql = "delete from `s_cart_product` where `id_cart` = ? and `id_product` = ? and `id_product_attribute` = ?;";
                Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute(array("$id_cart", "$id_product", "$id_product_attribute"));
                return $res;
        }

        public function del_cart_all_product($id_cart) {
                $sql = "delete from `s_cart_product` where `id_cart` = ? ;";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute(array("$id_cart"));
                return $res;
        }

        public function merge_cart_product($id_cart1, $id_cart2) {
                $product_list2 = self::get_cart_product_list($id_cart2);
                foreach($product_list2 as $k=>$p){
                    $product = self::get_cart_product($id_cart1, $p['id_product'], $p['id_product_attribute']);
                    if(empty($product)){
                       $p['id_cart'] = $id_cart1;
                       unset($p['p_info']);
                       unset($p['product_attribute']);
                       unset($p['item_total_price']);
                       unset($p['item_total_org_price']);
                       //Logger::info(__FILE__, __CLASS__, __LINE__, var_export($p, true));
                       self::add_cart_product($p);
                    }
                } 
                return ;
        }

/*
*/
		public function get_cart_info_by_u_kkid(){
				$product_list = array();
				$sql = "select * from s_cart a left join s_cart_product b on (a.id_cart = b.id_cart) where u_kkid = ?;";
				$stmt->$this->pdo->prepare($sql);
				$product_list = $stmt->execute(array("$u_kkid"))->fetchall();
				if (empty($res)) {
					$product_list = array();
				}
				return $product_list;
		}

}
