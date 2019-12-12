<?php
apf_require_class("APF_DB_Factory");

class Dao_Product_Info {

	private $pdo;

	public function __construct() {
		$this->pdo = APF_DB_Factory::get_instance()->get_pdo("shop_master");
	}
		public function id_product_kkh2id_product($id_product_kkh){
			$row = [];
			$sql = "select id_product from s_product where id_product_kkh = ?;";
			$stmt = $this->pdo->prepare($sql);
			$stmt->execute(array($id_product_kkh));
			return $stmt->fetchColumn();
		}
		
		public function get_id_product_by_p_kkid($p_kkid) {
			$row = [];
			$sql = "select id_product from s_product where kkid = ?;";
			$stmt = $this->pdo->prepare($sql);
			$stmt->execute(array($p_kkid));
			$res = $stmt->fetchColumn();
			return $res;
		}

        public function set_product_by_kkid($c_kkid, $u_kkid, $data) {
                //
                $data['u_kkid'] = $u_kkid;
                $data['kkid'] = $c_kkid;

                Logger::info(__FILE__, __CLASS__, __LINE__, var_export($data, true));

                $sql = "update `t_product` set `last_used` = :last_used, `submitted_by` = :submitted_by, `success` = :success, `fail` = :fail, `status` = :status, `locked` = :locked, `channel` = :channel where `kkid` = :kkid and u_kkid = :u_kkid ;";

                Logger::info(__FILE__, __CLASS__, __LINE__, $sql);

                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute($data);
        }

        public function cancel_product_by_kkid($c_kkid, $u_kkid, $data) {
                //
                $data['u_kkid'] = $u_kkid;
                $data['kkid'] = $c_kkid;
                unset($data['created']);
                unset($data['client_ip']);
                $reg = self::get_product($c_kkid, $u_kkid);
                if(!empty($reg) && $reg['payment_status'] == 1){
                   $data['payment_status'] = 5; // 5: 取消退款中 6: 取消
                }
                if(!empty($reg) && $reg['payment_status'] == 3){
                   $data['payment_status'] = 5; // 5: 取消退款中 6: 取消
                }
                if(!empty($reg) && $reg['payment_status'] == 4){
                   $data['payment_status'] = 6; // 5: 取消退款中 6: 取消
                }

                $sql = "update `t_product` set `payment_status` = :payment_status where `kkid` = :kkid and u_kkid = :u_kkid ;";

                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute($data);
        }

		public function madeinId2countryDetail() {
				$sql = "select id, name, icon from s_country_detail where active = 1;";
				$stmt = $this->pdo->prepare($sql);
				$stmt->execute();
				$res = $stmt->fetchAll();
				if(!$res){
					return array();
				}
				$list = [];
				foreach($res as $k=>$v){
					$list[$v['id']] = [
						'name' => $v['name'],
						'icon' => $v['icon'],
					];
				}
				return $list;
		}
        public function set_product_paystatus_by_kkid($c_kkid, $u_kkid, $status) {
                //
                $data = array();
                $data['u_kkid'] = $u_kkid;
                $data['kkid'] = $c_kkid;
                $data['payment_status'] = $status;
                $sql = "update `t_product` set `payment_status` = :payment_status where `kkid` = :kkid and u_kkid = :u_kkid ;";
                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute($data);
        }

        public function add_product($u_kkid, $data) {
                #Logger::info(__FILE__, __CLASS__, __LINE__, var_export($data, true));
                #Logger::info(__FILE__, __CLASS__, __LINE__, $u_kkid);
                $data['u_kkid'] = $u_kkid;
                $sql = "insert into `t_product` (`rid`, `kkid`, `u_kkid`, `truename`, `identitycard`, `mobile_num`, `h_kkid`, `hd_kkid`, `d_kkid`, `first_visit`, `checkin_date`, `checkin_hour`, `disease_type`, `outpatient_type`, `price`, `payment_method`, `service_charge`, `payment_channel`, `payment_order_sid`, `payment_status`, `payment_modify`, `status`, `created`, `update_date`, `client_ip`) values(0, replace(upper(uuid()),'-',''), :u_kkid, :truename, :identitycard, :mobile_num, :h_kkid, :hd_kkid, :d_kkid, :first_visit, :checkin_date, :checkin_hour, :disease_type, :outpatient_type, :price, :payment_method, :service_charge, :payment_channel, :payment_order_sid, :payment_status, :payment_modify, :status, :created, now(), :client_ip);";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                $last_id = $this->pdo->lastInsertId();
                $c_kkid = self::get_product_kkid_by_hid($last_id);
                return $c_kkid;
        }

        //检查 product 是否已存在
        public function check_product_is_exist($u_kkid, $data){
            $sql = "select `kkid` from `t_product` where `u_kkid` = :u_kkid and `h_kkid` = :h_kkid and `hd_kkid` = :hd_kkid and `d_kkid` = :d_kkid and `checkin_date` = :checkin_date and `checkin_hour` = :checkin_hour and `truename` = :truename limit 1;";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($data);
            $c_kkid = $stmt->fetchColumn();
            if($c_kkid){
                return $c_kkid;
            }else{
                return array();
            }
        }

        public function get_product($p_kkid) {
                $row = array();

                $wh1 =  " and `id_product_kkh` = ? ";
                if(strlen($p_kkid) == 32) $wh1 =  " and `kkid` = ? ";

                /*$sql = "select `id_product`, `kkid`, `id_supplier`, `id_manufacturer`, `id_category_default`, `id_shop_default`, `id_tax_rules_group`, `on_sale`, `online_only`, `ean13`, `isbn`, `upc`, `ecotax`, `quantity`, `minimal_quantity`, `price`, `wholesale_price`, `jd_price` ,`tb_price`, `short_msg`, `madein`, `kkhtags`, `guige`, `unity`, `unit_price_ratio`, `additional_shipping_cost`, `reference`, `supplier_reference`, `location`, `width`, `height`, `depth`, `weight`, `out_of_stock`, `quantity_discount`, `customizable`, `uploadable_files`, `text_fields`, `active`, `redirect_type`, `id_type_redirected`, `available_for_order`, `available_date`, `show_condition`, `condition`, `show_price`, `indexed`, `visibility`, `cache_is_pack`, `cache_has_attachments`, `is_virtual`, `cache_default_attribute`, `date_add`, `date_upd`, `advanced_stock_management`, `pack_stock_type`, `state`, `id_product_kkh`, `doctor_visible`, `patient_visible`, `jd_spider_url`, `tb_spider_url`, `allowed_coupon` from `s_product` where 1 = 1   $wh1 limit 1;";*/
				$sql = "select `id_product`, `kkid`, `id_supplier`, `quantity`, `price`, `wholesale_price`, `active`, `date_add`, `date_upd`, `state`, `id_product_kkh`, `allowed_coupon`, `jd_price` ,`tb_price` ,`short_msg` ,`kkhtags` ,`madein` ,`guige`, cornertag, hospital_price from `s_product` where 1 = 1   $wh1 limit 1;";
                // `active` = 1 and and `price` > 0
                //Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$p_kkid"));
                
                $row = $stmt->fetch();

                if(isset($row['id_product']) && !empty($row['id_product'])){
                    $id_product = $row['id_product'];
                    $product_lang = self::get_product_lang($id_product, 1);
                    $product_shop = self::get_product_shop($id_product);
                    $product_images = self::get_product_image_list($id_product);
                    $product_images_detail = self::get_product_image_list($id_product, 1);
                    $product_feature = self::get_product_feature_list($id_product);
                    $product_attribute = self::get_product_attribute_list($id_product);
                    $row = array_merge($product_lang, $product_shop, $row);
                    $row['images'] = $product_images;
                    $row['images_detail'] = $product_images_detail;
                    $row['feature'] = $product_feature;
                    $row['attribute'] = $product_attribute;
                }

                if(empty($row)){
                   $row = array();
                }
                return $row;

        }

		public function get_product_by_id_product($id_product) {
				$row = array();
				$sql = "select id_product, name from s_product_lang where id_product = ? and id_lang =1 limit 1;";
				$stmt = $this->pdo->prepare($sql);
				$stmt->execute(array("$id_product"));
				$row = $stmt->fetch();
				if (!$row) {
					return array();
				}
				return $row;
		}

        public function get_product_by_id($id_product) {
                $row = array();
                //$sql = "select `kkid`, `id_product`, `id_supplier`, `id_manufacturer`, `id_category_default`, `id_shop_default`, `id_tax_rules_group`, `on_sale`, `online_only`, `ean13`, `isbn`, `upc`, `ecotax`, `quantity`, `minimal_quantity`, `price`, `wholesale_price`, `jd_price` ,`tb_price`, `short_msg`, `madein`, `kkhtags`, `guige`, `unity`, `unit_price_ratio`, `additional_shipping_cost`, `reference`, `supplier_reference`, `location`, `width`, `height`, `depth`, `weight`, `out_of_stock`, `quantity_discount`, `customizable`, `uploadable_files`, `text_fields`, `active`, `redirect_type`, `id_type_redirected`, `available_for_order`, `available_date`, `show_condition`, `condition`, `show_price`, `indexed`, `visibility`, `cache_is_pack`, `cache_has_attachments`, `is_virtual`, `cache_default_attribute`, `date_add`, `date_upd`, `advanced_stock_management`, `pack_stock_type`, `state`, `id_product_kkh`, `doctor_visible`, `patient_visible`, `jd_spider_url`, `tb_spider_url`, `allowed_coupon` from `s_product` where id_product = ? limit 1;";
                $sql = "select `id_product`, `id_supplier`, `kkid`, `quantity`, `price`, `wholesale_price`, `active`, `date_add`, `date_upd`, `state`, `id_product_kkh`, `allowed_coupon` ,`jd_price` ,`tb_price` ,`short_msg` ,`kkhtags` ,`madein` ,`guige`, cornertag, hospital_price from `s_product` where id_product = ? limit 1;";
				// active = 1 and price > 0
                //Logger::info(__FILE__, __CLASS__, __LINE__, "id_product : ".$id_product);
                //Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$id_product"));
                
                $row = $stmt->fetch();

                if(isset($row['id_product']) && !empty($row['id_product'])){
                    $id_product = $row['id_product'];
                    $product_lang = self::get_product_lang($id_product, 1);
                    $product_shop = self::get_product_shop($id_product);
                    $product_images = self::get_product_image_list($id_product);
                    $row = array_merge($product_lang, $product_shop, $row);
                    $row['images'] = $product_images;
                }

                if(empty($row)){
                   $row = array();
                }
                return $row;

        }

        //后台管理系统获取商品列表（所有商品）
        public function get_shop_list(){
            $sql = "select `id_product`, `kkid`,  `price`, `wholesale_price`, `jd_price` ,`tb_price`,  `active`,`id_product_kkh` from `s_product` where active = 1 order by id_product_kkh desc";
            Logger::info(__FILE__, __CLASS__, __LINE__, "sql: $sql");
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $jobs = $stmt->fetchAll();
            $job = array();
            foreach($jobs as $k=>$j){
                if(isset($j['id_product']) && !empty($j['id_product'])){
                    $id_product = $j['id_product'];
                    $product_lang = self::get_product_lang($id_product, 1);
                    $product_images = self::get_product_image_list($id_product);
                    $j = array_merge($product_lang, $j);
                    $j['images'] = $product_images;
                }
                $job[$k] = $j;
            }
            return $job;
        }

        //获取口碑、精选、新品商品
        public function get_operation_shop_list($type)
        {   
		    if($type == 1){
			   $params = " WHERE o.is_choiceness=1 AND p.active=1";
			}
			elseif($type == 2)
			{
			   $params = " WHERE o.is_public_praise=1 AND p.active=1";
			}
			elseif($type == 3)
			{
			  $params = " WHERE o.is_new_recommend=1 AND p.active=1";
			}
			else
			{
			  $params = " WHERE  p.active=1";
			}

            $sql = "SELECT
                        p.`id_product`,
                        p.`kkid`,
                        p.`price`,
                        p.`wholesale_price`,
                        o.position,
                        o.is_choiceness,
                        o.is_public_praise,
                        o.is_new_recommend,
                        o.id,
						o.created_at
                    FROM
                        `s_operation_product` AS o
                    LEFT JOIN `s_product` AS p ON p.id_product = o.id_product $params  ORDER BY o.created_at DESC";
            Logger::info(__FILE__, __CLASS__, __LINE__, "sql: $sql");
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $jobs = $stmt->fetchAll();
            $job = array();
            foreach($jobs as $k=>$j){
                if(isset($j['id_product']) && !empty($j['id_product'])){
                    $id_product = $j['id_product'];
                    $product_lang = self::get_product_lang($id_product, 1);
                    $product_images = self::get_product_image_list($id_product);
                    $j = array_merge($product_lang, $j);
                    $j['images'] = $product_images;

                }
                $job[$k] = $j;
            }
            return $job;
        }
         //新增口碑、精选、新品商品
        public function add_operation_shop($data){
            $data['create_time'] = date("Y-m-d H:i:s");
            $sql = "INSERT INTO `s_operation_product` (
                        `id_product`,
                        `position`,
                        `is_choiceness`,
                        `is_public_praise`,
                        `is_new_recommend`,
                        `created_at`
                    )
                    VALUES
                        (
                            :id_product ,:position ,:is_choiceness ,:is_public_praise ,:is_new_recommend ,:create_time
                        )";
            $stmt = $this->pdo->prepare($sql);
            $res = $stmt->execute($data);
            $last_id = $this->pdo->lastInsertId();
            return $last_id;
        }

        //删除口碑、精选、新品商品
        public function del_operation_shop($id){
            $sql = "DELETE FROM `s_operation_product` WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $res = $stmt->execute(array($id));
            return $res;
        }

        public function get_product_list($limit, $offset)
        {
            if(!is_numeric($limit) || !is_numeric($offset)){
                return array();
            }
    
            $sql = "select `id_product`, `kkid`, `id_supplier`, `id_manufacturer`, `id_category_default`, `id_shop_default`, `id_tax_rules_group`, `on_sale`, `online_only`, `ean13`, `isbn`, `upc`, `ecotax`, `quantity`, `minimal_quantity`, `price`, `wholesale_price`, `jd_price` ,`tb_price`, `short_msg`, `madein`, `kkhtags`, `guige`, `cornertag`, `hospital_price`, `unity`, `unit_price_ratio`, `additional_shipping_cost`, `reference`, `supplier_reference`, `location`, `width`, `height`, `depth`, `weight`, `out_of_stock`, `quantity_discount`, `customizable`, `uploadable_files`, `text_fields`, `active`, `redirect_type`, `id_type_redirected`, `available_for_order`, `available_date`, `show_condition`, `condition`, `show_price`, `indexed`, `visibility`, `cache_is_pack`, `cache_has_attachments`, `is_virtual`, `cache_default_attribute`, `date_add`, `date_upd`, `advanced_stock_management`, `pack_stock_type`, `state`, `id_product_kkh`, `doctor_visible`, `patient_visible`, `jd_spider_url`, `tb_spider_url`, `allowed_coupon` from `s_product` where active = 1 order by id_product_kkh desc LIMIT :limit OFFSET :offset;";
            //$sql = "select `id_product`, `kkid`, `id_supplier`, `quantity`, `price`, `wholesale_price`, `date_add`, `date_upd`, `state`, `id_product_kkh`, `active`, `allowed_coupon`,`jd_price` ,`tb_price` ,`short_msg` ,`kkhtags` ,`madein` ,`guige` from `s_product` where active = 1 order by id_product_kkh desc LIMIT :limit OFFSET :offset;";
			Logger::info(__FILE__, __CLASS__, __LINE__, "sql: $sql");
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $jobs = $stmt->fetchAll();
            $job = array();
            foreach($jobs as $k=>$j){
                if(isset($j['id_product']) && !empty($j['id_product'])){
                    $id_product = $j['id_product'];
                    $product_lang = self::get_product_lang($id_product, 1);
                    $product_shop = self::get_product_shop($id_product);
                    $product_images = self::get_product_image_list($id_product);
                    $product_images_detail = self::get_product_image_list($id_product, 1);
                    $j = array_merge($product_lang, $product_shop, $j);
                    $j['images'] = $product_images;
                    $j['images_detail'] = $product_images_detail;
                    $j['feature'] = $product_feature;
                }
                $job[$k] = $j;
            }
            return $job;
        }
    
        public function get_product_count()
        {
            $c = 0;
            $get_count_sql = "select count(*) c from `s_product` where active = 1 ;";
            Logger::info(__FILE__, __CLASS__, __LINE__, "sql: $get_count_sql");
            $stmt = $this->pdo->prepare($get_count_sql);
            $stmt->execute();
            $c = $stmt->fetchColumn();
            return $c;

        }

        public function get_product_search_list($p_kw, $limit, $offset)
        {
            if(!is_numeric($limit) || !is_numeric($offset)){
                return array();
            }

            $p_kw_like = '%' . $p_kw . '%';
    
            $sql = "select a.* from `s_product` a left join `s_product_lang` b on a.id_product=b.id_product where a.active = 1 and a.quantity > 0 and a.price > 0 and b.name like :name and b.id_lang=1 order by a.id_product_kkh desc LIMIT :limit OFFSET :offset;";
            Logger::info(__FILE__, __CLASS__, __LINE__, "sql: $sql");
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindParam(':name', $p_kw_like, PDO::PARAM_STR);
            $stmt->execute();
            $jobs = $stmt->fetchAll();
            $job = array();
            foreach($jobs as $k=>$j){
                if(isset($j['id_product']) && !empty($j['id_product'])){
                    $id_product = $j['id_product'];
                    $product_lang = self::get_product_lang($id_product, 1);
                    $product_shop = self::get_product_shop($id_product);
                    $product_images = self::get_product_image_list($id_product);
                    $product_images_detail = self::get_product_image_list($id_product, 1);
                    $j = array_merge($product_lang, $product_shop, $j);
                    $j['images'] = $product_images;
                    $j['images_detail'] = $product_images_detail;
                    $j['feature'] = $product_feature;
                }
                $job[$k] = $j;
            }
            return $job;
        }

		public function cornertag_id_2_detail($id_cornertag){
			$row = [];
			$sql = "select title, img_url from s_cornertags where id = ? and active = 1 limit 1;";
			$stmt = $this->pdo->prepare($sql);
			$stmt->execute(array($id_cornertag));
			$res = $stmt->fetch();
			if(!$res){
				return array();
			}
			return $res;
		}

        public function get_product_search_count($p_kw)
        {
            $c = 0;
            $p_kw_like = '%' . $p_kw . '%';
            $get_count_sql = "select count(*) c from `s_product` a left join `s_product_lang` b on a.id_product=b.id_product where a.active = 1 and a.quantity > 0 and a.price > 0 and b.name like :name and b.id_lang=1;";
            Logger::info(__FILE__, __CLASS__, __LINE__, "sql: $get_count_sql");
            $stmt = $this->pdo->prepare($get_count_sql);
            $stmt->bindParam(':name', $p_kw_like, PDO::PARAM_STR);
            $stmt->execute();
            $c = $stmt->fetchColumn();
            return $c;

        }

        public function get_product_image_list($id_product, $is_detail = 0)
        {
            //$sql = "select `id_image`, `id_product`, `position`, `cover`, `is_detail_pic`, `id_product_kkh`, `id_product_kkh_url` from `s_image` where `id_product` = :id_product and is_detail_pic = '$is_detail' order by position asc;";
            $sql = "select `id_image`, `cover`, `is_detail_pic`, `id_product_kkh_url` from `s_image` where `id_product` = :id_product and is_detail_pic = '$is_detail' order by position asc;";
			//Logger::info(__FILE__, __CLASS__, __LINE__, "sql: $sql");
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_product', $id_product, PDO::PARAM_INT);
            $stmt->execute();
            $jobs = $stmt->fetchAll();
            $job = array();
            foreach($jobs as $k=>$j){
                if(isset($j['id_image']) && !empty($j['id_image'])){
                    $id_image = $j['id_image'];
                    $image_lang = self::get_product_image_lang($id_image, 1);
                    $j = array_merge($image_lang, $j);
                }
                $job[$k] = $j;
            }
            return $job;
        }

        public function get_product_feature_list($id_product)
        {
            $sql = "SELECT name, value, pf.id_feature FROM s_feature_product pf LEFT JOIN s_feature_lang fl ON (fl.id_feature = pf.id_feature AND fl.id_lang = 1) LEFT JOIN s_feature_value_lang fvl ON (fvl.id_feature_value = pf.id_feature_value AND fvl.id_lang = 1) LEFT JOIN s_feature f ON (f.id_feature = pf.id_feature AND fl.id_lang = 1) INNER JOIN s_feature_shop feature_shop ON (feature_shop.id_feature = f.id_feature AND feature_shop.id_shop = 1) WHERE pf.id_product = :id_product ORDER BY f.position ASC;";
            //Logger::info(__FILE__, __CLASS__, __LINE__, "sql: $sql");
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_product', $id_product, PDO::PARAM_INT);
            $stmt->execute();
            $jobs = $stmt->fetchAll();
            $job = array();
            foreach($jobs as $k=>$j){
                $job[$k] = $j;
            }
            return $job;
        }

        public function get_product_attribute_list($id_product)
        {
            $sql = "select * from (select * from (SELECT ag.`id_attribute_group`, ag.`is_color_group`, agl.`name` AS group_name, a.`id_attribute`, al.`name` AS attribute_name, a.`color` AS attribute_color, product_attribute_shop.`id_product_attribute`, ag.`group_type` FROM `s_product_attribute` pa INNER JOIN s_product_attribute_shop product_attribute_shop ON (product_attribute_shop.id_product_attribute = pa.id_product_attribute AND product_attribute_shop.id_shop = 1) LEFT JOIN s_stock_available stock ON (stock.id_product = `pa`.id_product AND stock.id_product_attribute = IFNULL(`pa`.id_product_attribute, 0) AND stock.id_shop = 1  AND stock.id_shop_group = 0  ) LEFT JOIN `s_product_attribute_combination` pac ON (pac.`id_product_attribute` = pa.`id_product_attribute`) LEFT JOIN `s_attribute` a ON (a.`id_attribute` = pac.`id_attribute`) LEFT JOIN `s_attribute_group` ag ON (ag.`id_attribute_group` = a.`id_attribute_group`) LEFT JOIN `s_attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` and al.`id_lang` = 1) LEFT JOIN `s_attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` and agl.`id_lang` = 1) INNER JOIN s_attribute_shop attribute_shop ON (attribute_shop.id_attribute = a.id_attribute AND attribute_shop.id_shop = 1) WHERE pa.`id_product` = :id_product) as t1 order by group_name asc , attribute_name asc) as t2 group by id_attribute;";
            //Logger::info(__FILE__, __CLASS__, __LINE__, "sql: $sql");
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_product', $id_product, PDO::PARAM_INT);
            $stmt->execute();
            $jobs = $stmt->fetchAll();
            $job = array();
            foreach($jobs as $k=>$j){
                $job[$k] = $j;
            }
            return $job;
        }

    private function get_practice_data($hd_kkid, $d_kkid, $h_kkid)
    {
        if(empty($hd_kkid)){
            return array();
        }

        $sql = "select doctor doctor_name, job_title, degree, photo, hospital, department, r_score, reg_num_int, pat_num_int, clinic_type, price from t_practice_points where hd_kkid=? and d_kkid=? and h_kkid=? and status=1 order by r_score desc, reg_num_int desc limit 1;";

        #Logger::info(__FILE__, __CLASS__, __LINE__, var_export($sql, true));
        #Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array("$hd_kkid", "$d_kkid", "$h_kkid"));
        $j = $stmt->fetch();
        if(isset($j['photo']) && !empty($j['photo'])){
          $j['photo'] = IMG_CDN_DOCTOR . $j['photo'] . "/" . "headpic.jpg";
        }
        $de = self::get_doctor_data($d_kkid);
        if(isset($de['expertise'])){
          $j['expertise'] = $de['expertise'];
          $j['tags']      = $de['tags'];
        }
        return $j;
    }

    private function get_doctor_data($kkid)
    {
        $sql = "select expertise, tags from t_doctor where kkid = ?  and status=1 limit 1;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array($kkid));
        return $stmt->fetch();
    }

        private function get_product_image_lang($id_image, $id_lang) {
                $row = array();
                $sql = "select `id_image`, `legend` from `s_image_lang` where `id_image` = ?  and id_lang = ? ;";
                //Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$id_image", "$id_lang"));
                $row = $stmt->fetch();
                if(empty($row)){
                   $row = array();
                }
                return $row;

        }

        private function get_product_lang($id_product, $id_lang) {
                $row = array();
                //$sql = "select `id_product`, `id_shop`, `id_lang`, `description`, `description_short`, `link_rewrite`, `meta_description`, `meta_keywords`, `meta_title`, `name`, `available_now`, `available_later`, `id_product_kkh` from `s_product_lang` where `id_product` = ? and id_lang = ? and id_shop = 1 limit 1;";
                $sql = "select `id_product`, `description`, `description_short`, `name`, `id_product_kkh` from `s_product_lang` where `id_product` = ? and id_lang = ? and id_shop = 1 limit 1;";
				//Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$id_product", "$id_lang"));
                $row = $stmt->fetch();
                if(isset($row['description_short']) && !empty($row['description_short'])){ 
                   $row['description_short'] = strip_tags($row['description_short']);
                }
                if(empty($row)){
                   $row = array();
                }
                return $row;

        }

        private function get_product_shop($id_product) {
                $row = array();
                //$sql = "select `id_product`, `id_shop`, `id_category_default`, `id_tax_rules_group`, `on_sale`, `online_only`, `ecotax`, `minimal_quantity`, `price`, `wholesale_price`, `unity`, `unit_price_ratio`, `additional_shipping_cost`, `customizable`, `uploadable_files`, `text_fields`, `active`, `redirect_type`, `id_type_redirected`, `available_for_order`, `available_date`, `show_condition`, `condition`, `show_price`, `indexed`, `visibility`, `cache_default_attribute`, `advanced_stock_management`, `date_add`, `date_upd`, `pack_stock_type`, `id_product_kkh` from `s_product_shop` where `id_product` = ? limit 1;";
                $sql = "select `id_product`, `id_category_default`, `price`, `wholesale_price`, `active`, `visibility`, `date_add`, `date_upd`, `id_product_kkh` from `s_product_shop` where `id_product` = ? limit 1;";
				//Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$id_product"));
                $row = $stmt->fetch();
                if(empty($row)){
                   $row = array();
                }
                return $row;

        }
    
        public function get_category_by_parentid($id_parent) {
                $row = array();
                $sql = "SELECT a.id_category, `img_kkh_url`, `name`, `level_depth`, `description`, sa.`position` AS `position`, `active` , sa.position position FROM s_category a LEFT JOIN `s_category_lang` b ON (b.id_category = a.id_category AND b.`id_lang` = 1 AND b.`id_shop` = 1) LEFT JOIN `s_category_shop` sa ON (a.id_category = sa.id_category AND sa.id_shop = 1) WHERE 1 AND id_parent = ? AND `active` = 1 ORDER BY sa.`position` ASC LIMIT 0, 50;";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$id_parent"));
                $row = $stmt->fetchall();
                if(empty($row)){
                    $row = array();        
                }
                return $row;
        }

        public function get_cateprodlist_count($id_category) {
                $c = 0;
                $sql = "SELECT count(*) c from `s_category_product` a LEFT JOIN `s_product` b ON (a.`id_product` = b.`id_product`) LEFT JOIN s_product_lang pl ON (a.id_product = pl.id_product) where a.id_category = :id_category and pl.id_lang = 1 and b.active = 1;";
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam(':id_category', $id_category, PDO::PARAM_INT);
                $stmt->execute();
                $c = $stmt->fetchColumn();
        
                return $c;
        }


        public function get_productlist_by_idcategory($id_category, $limit, $offset) {
                $row = array();
                $sql = "SELECT a.`id_product`, a.`position` ,b.`kkid` ,`quantity` ,`price` ,`wholesale_price` ,`jd_price` ,`tb_price`, `short_msg`, `madein`, `kkhtags`, `guige`, `cornertag`, `hospital_price`, b.`id_product_kkh` ,`allowed_coupon` ,`name` ,si.`id_product_kkh_url` as cover_img from `s_category_product` a LEFT JOIN `s_product` b ON (a.`id_product` = b.`id_product`) LEFT JOIN s_product_lang pl ON (a.id_product = pl.id_product) LEFT JOIN `s_image` si ON (si.id_product = b.id_product) where si.cover = 1 and a.id_category = :id_category and pl.id_lang = 1 and b.active = 1 order by a.`position` desc LIMIT :limit OFFSET :offset;";
                Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam(':id_category', $id_category, PDO::PARAM_INT);
                $stmt->bindParam(':limit', $offset, PDO::PARAM_INT);
                $stmt->bindParam(':offset', $limit, PDO::PARAM_INT);
                $stmt->execute();
                $row = $stmt->fetchall();

                if(empty($row)){
                    $row = array();        
                }
				foreach($row as $k=>$v){
					if($v['cornertag'] == 0){
						$v['cornertag'] = array();
					}else{
						$v['cornertag'] = self::cornertag_id_2_detail($v['cornertag']);
					}
					$row[$k] = $v;
				}

                return $row;
        }
		
		public function get_price_by_p_kkid($p_kkid) {
				$price = 0;
				if (strlen($p_kkid) == 32) { 
					$sql = "select price from s_product where kkid = ?;";
				} else {
					$sql = "select price from s_product where id_product_kkh = ?;";
				}
				$stmt = $this->pdo->prepare($sql);
				$stmt->execute(array("$p_kkid"));
				$price = $stmt->fetchColumn();
				Logger::info(__FILE__, __CLASS__, __LINE__, $price);				
				return $price;
		}

		public function id_product2p_kkid($id_product){
			if(!$id_product) return '';
			$sql = "select kkid from s_product where id_product = ? limit 1;";
			$stmt = $this->pdo->prepare($sql);
			$stmt->execute(array($id_product));
			$res = $stmt->fetchColumn();
			if(!$res){
				return '';
			}
			return $res;
		}
		/**
		*获取猜你喜欢的商品
		*@param $page_start  "查询开始位置"
		*@param $page_size  "查询条数"
		*/
        public function get_product_guess_you_like($page_start,$page_size)
		{
		    $sql = "
					SELECT
						b.id_product,
						b.`kkid`,
						`quantity`,
						`price`,
						`wholesale_price`,
						`jd_price`,
						`tb_price`,
						`short_msg`,
						`madein`,
						`kkhtags`,
						`guige`,
						`cornertag`,
						`hospital_price`,
						b.`id_product_kkh`,
						`allowed_coupon`,
						`name`,
						si.`id_product_kkh_url` AS cover_img 
					FROM
						`s_product` b 
					LEFT JOIN s_product_lang pl ON (b.id_product = pl.id_product)
					LEFT JOIN `s_image` si ON (si.id_product = b.id_product)
					WHERE
						si.cover = 1
					AND b.price <=100
					AND pl.id_lang = 1
					AND b.active = 1
					GROUP BY b.id_product
					ORDER BY
						b.`price` asc
					LIMIT :page_size OFFSET :page_start;
			";
			 Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
			 $stmt = $this->pdo->prepare($sql);
			
 			 $stmt->bindParam(':page_size', $page_size, PDO::PARAM_INT);
			 $stmt->bindParam(':page_start', $page_start, PDO::PARAM_INT);
			 $stmt->execute();
			 $row = $stmt->fetchall();
			 if(empty($row)) $row = array();
			 foreach($row as $k=>&$v){
			 	if($v['cornertag'] == 0){
			 		$v['cornertag'] = array();
			 	}else{
			 		$v['cornertag'] = self::cornertag_id_2_detail($v['cornertag']);
			 	}
				if($v['id_product'])
				{
				    $product_attribute = self::get_product_attribute_list($id_product);
					$v['product_attribute'] = $product_attribute;

				}
				$v['price'] = sprintf("%.1f",$v['price']);
				$v['wholesale_price'] = sprintf("%.1f",$v['wholesale_price']);
			 	//$row[$k] = $v;
			 }
			 return $row;

		}
		//获取猜你喜欢数据有多少条
		public function get_guess_you_like_count()
		{
		    $sql = "

					SELECT
						count(DISTINCT b.id_product) c
					FROM
						`s_category_product` a
					LEFT JOIN `s_product` b ON (
						a.`id_product` = b.`id_product`
					)
					LEFT JOIN s_product_lang pl ON (a.id_product = pl.id_product)
					LEFT JOIN `s_image` si ON (si.id_product = b.id_product)
					WHERE
						b.price <=100
					AND si.cover = 1
					AND pl.id_lang = 1
					AND b.active = 1
			";
			$stmt = $this->pdo->prepare($sql);
			$stmt->execute();
			$c = $stmt->fetchColumn();
			return $c;
		}

		//获取某个商品所属的分类
		public function get_product_category_list($id_product)
		{
		    $sql = "SELECT id_category FROM s_category_product WHERE id_product=:id_product";
			$stmt = $this->pdo->prepare($sql);
			$stmt->bindParam(':id_product', $id_product, PDO::PARAM_INT);
			$stmt->execute();
			$row = $stmt->fetchAll();
			if(empty($row)) $row=[];
			return $row;
		}
        /**
		*通过分类id获取相似商品
		*@param $category_str 分类字符串 以逗号隔开 如：2,3,4,5,6
		*@param $page_start 分页开始查询位置
		*@param $page_size  查询条数
		*/
		public function get_similar_product_list($category_str,$page_start,$page_size)
		{
		    $sql = "
			SELECT
					a.`id_product`,
					a.`position`,
					b.`kkid`,
					`quantity`,
					`price`,
					`wholesale_price`,
					`jd_price`,
					`tb_price`,
					`short_msg`,
					`madein`,
					`kkhtags`,
					`guige`,
					`cornertag`,
					`hospital_price`,
					b.`id_product_kkh`,
					`allowed_coupon`,
					`name`,
					si.`id_product_kkh_url` AS cover_img
			FROM
				`s_category_product` a
			LEFT JOIN `s_product` b ON (
					a.`id_product` = b.`id_product`
			)
			LEFT JOIN s_product_lang pl ON (a.id_product = pl.id_product)
			LEFT JOIN `s_image` si ON (si.id_product = b.id_product)
			WHERE
				si.cover = 1
			AND a.id_category in(:category_str)
			AND pl.id_lang = 1
			AND b.active = 1
			GROUP BY id_product 
			ORDER BY
				a.`position` DESC
			LIMIT :page_size OFFSET :page_start";
			Logger::info(__FILE__, __CLASS__, __LINE__, $sql.'===='.$page_start.'==='.$page_size.'===='.$category_str);
			$stmt = $this->pdo->prepare($sql);
			try{
					$stmt->bindParam(":category_str", $category_str, PDO::PARAM_STR);
					$stmt->bindParam(":page_size", $page_size, PDO::PARAM_INT);
					$stmt->bindParam(":page_start", $page_start, PDO::PARAM_INT);
					$stmt->execute();
					$row = $stmt->fetchall();
			}catch(Exception $e)
			{
			     Logger::info(__FILE__, __CLASS__, __LINE__, $e->getMessage());
			}
			if(empty($row)) $row = array();
			foreach($row as $k=>&$v){
					if($v['cornertag'] == 0){
						$v['cornertag'] = array();
					}else{
						$v['cornertag'] = self::cornertag_id_2_detail($v['cornertag']);
					}
						//$row[$k] = $v;
					$v['price'] = sprintf("%.1f",$v['price']);
					$v['wholesale_price'] = sprintf("%.1f",$v['wholesale_price']);
			}
			
			return $row;

		}

		public function get_similar_product_list_count($category_str)
		{
		    $c = 0;
			$sql = "
					SELECT
						count(*) c
					FROM
						`s_category_product` a
					LEFT JOIN `s_product` b ON (
						a.`id_product` = b.`id_product`
					)
					LEFT JOIN s_product_lang pl ON (a.id_product = pl.id_product)
					WHERE
						a.id_category = :category_str
						AND pl.id_lang = 1
						AND b.active = 1;
			";
			$stmt = $this->pdo->prepare($sql);
			$stmt->bindParam(':category_str', $category_str, PDO::PARAM_STR);
			$stmt->execute();
			$c = $stmt->fetchColumn();

			return $c;
		}

}
