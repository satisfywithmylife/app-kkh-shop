<?php
/**
 * Created by PhpStorm.
 * User: hanxiaolong
 * Date: 2018/4/16
 * Time: 17:39
 * todo now 货道管理 - 激活和编辑筛选医院, 从10号机的green2数据库中获取医院列表
 * todo 为何stockoutgetdetail会路由到stockoutget, 之前也遇到过类似
 * todo 所有表的索引优化
 */
apf_require_class('APF_DB_FACTORY');

class Dao_Cabinet_Info {
    private $pdo_admin;
    private $pdo_shop;

	public static $INVALID_ORDER_STATE = array(
        1 => '待付款',
        2 => '已付款',
        4 => '已发货',
        6 => '已取消',
        7 => '已退款',
        13 => '已完成',
		14 => '新零售已付款但出货失败'
    );

    public function __construct() {
        $this->pdo_admin = APF_DB_Factory::get_instance()->get_pdo('admin_master');
        $this->pdo_shop = APF_DB_Factory::get_instance()->get_pdo('shop_master');
    }

    /**
     * 医院 - 省列表
     * @return array|bool
     */
    public function hospital_province() {
        $ret = [];

        $url = CABINET_HOSPITAL_URL . 'hospital/province/';

        $data = [];
        $res = Util_Curl::http_post($url, $data, 1);
		//test
		Logger::info('test1, url = ' . $url . ', res = ' . json_encode($res));
        if (isset($res['status']) && intval($res['status']) === 200 && !empty($res['data']) && !empty($res['data']['list'])){
            $ret = $res['data']['list'];
        } else {
            Logger::info(__METHOD__ . ' fail, res = ' . json_encode($res));
            return false;
        }

        return $ret;
    }

    /**
     * 医院 - 市列表
     * @param $province_id
     * @return array|bool
     */
    public function hospital_area($province_id) {
        $ret = [];

        $url = CABINET_HOSPITAL_URL . 'hospital/area/';

        $data = [
            'province_id' => $province_id
        ];
        $res = Util_Curl::http_post($url, $data, 1);
        if (isset($res['status']) && intval($res['status']) === 200 && !empty($res['data']) && !empty($res['data']['list'])){
            $ret = $res['data']['list'];
        } else {
            Logger::info(__METHOD__ . ' fail, res = ' . json_encode($res) . ', province_id = ' . $province_id);
            return false;
        }

        return $ret;
    }

    /**
     * 医院 - 列表
     * @param $area_id
     * @return array|bool
     */
    public function hospital_list($area_id) {
        $ret = [];

        $url = CABINET_HOSPITAL_URL . 'hospital/list/';

        $data = [
            'area_id' => $area_id
        ];
        $res = Util_Curl::http_post($url, $data, 1);
        if (isset($res['status']) && intval($res['status']) === 200 && !empty($res['data']) && !empty($res['data']['list'])){
            $ret = $res['data']['list'];
        } else {
            Logger::info(__METHOD__ . ' fail, res = ' . json_encode($res) . ', area_id = ' . $area_id);
            return false;
        }

        return $ret;
    }

    /**
     * 售货柜 - 申请激活
	 * todo id_hospital
     * @param $cd_key
     * @return array|bool
     */
    public function cabinet_ask_for_active($cd_key) {
        $id_hospital = 0;
        $cabinet_name = '';
        $cabinet_address = '';
        $cabinet_status = 1;
        $charge_person = '';

        $ret = $this->cabinet_add($id_hospital, $cd_key, $cabinet_name, $cabinet_address, $cabinet_status, $charge_person);
        if ($ret !== false) {
            return ['id_cabinet' => $ret];
        } else {
            return false;
        }
    }

    /**
     * 售货柜 - 激活
     * todo 激活的相关错误码
     * @param $id_hospital
	 * @param $id_province
	 * @param $id_city
     * @param $id_cabinet
     * @param $cabinet_name
     * @param $cabinet_address
     * @param $cabinet_status
     * @param $charge_person
     * @return bool
     */
    public function cabinet_active($id_hospital, $id_province, $id_city, $id_cabinet, $cabinet_name, $cabinet_address, $cabinet_status, $charge_person) {
        $active_status = 1;
        $param = [
            'id_hospital' => $id_hospital,
			'id_province' => $id_province,
			'id_city' => $id_city,
            'id_cabinet' => $id_cabinet,
            'cabinet_name' => $cabinet_name,
            'cabinet_address' => $cabinet_address,
            'cabinet_status' => $cabinet_status,
            'charge_person' => $charge_person,
            'active_status' => $active_status
        ];

        $sql = 'update t_cabinet_cabinet set id_hospital = :id_hospital, id_province = :id_province, id_city = :id_city, cabinet_name = :cabinet_name, cabinet_address = :cabinet_address';
        $sql .= ', cabinet_status = :cabinet_status, charge_person = :charge_person, active_status = :active_status,';
        $sql .= ' update_ts = :update_ts, active_ts = :active_ts';
        $sql .= ' where id = :id_cabinet';

        $time_now = date('Y-m-d H:i:s');
        try {
            $stmt = $this->pdo_admin->prepare($sql);
            $ret = $stmt->execute(
                [
                    ':id_hospital' => $id_hospital,
					':id_province' => $id_province,
					':id_city' => $id_city,
                    ':id_cabinet' => $id_cabinet,
                    ':cabinet_name' => $cabinet_name,
                    ':cabinet_address' => $cabinet_address,
                    ':cabinet_status' => $cabinet_status,
                    ':charge_person' => $charge_person,
                    ':active_status' => $active_status,
                    ':update_ts' => $time_now,
                    ':active_ts' => $time_now
                ]
            );
            if($ret === false) {
                Logger::info(__METHOD__ . ' db error, sql = ' . $sql . ', param = ' . json_encode($param));
                return false;
            }

            $row_count = $stmt->rowCount();
            if($row_count <= 0) {
                Logger::info(__METHOD__ . ' affect 0 row');
                return false;
            }
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' exception = ' . $e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * 售货柜 - 获取
     * @param $active_status - 0未激活, 1已激活
     * @param $page_size
     * @param $page_num
     * @return array|bool
     */
    public function cabinet_get($active_status, $page_size, $page_num) {
        $ret = [];

        $id_begin = ($page_num - 1) * $page_size;
        $sql = 'select id_hospital, id_province, id_city, id as id_cabinet, cd_key, cabinet_name, cabinet_address, cabinet_status, charge_person, create_ts from t_cabinet_cabinet';
        $sql .= ' where active_status = :active_status';
		$sql .= ' order by id desc';
        $sql .= ' limit ' . $id_begin . ', ' . $page_size;

        try {
            $stmt = $this->pdo_admin->prepare($sql);
            $stmt->execute(
                [
                    ':active_status' => $active_status
                ]
            );

            $rows = $stmt->fetchAll();
            foreach ($rows as &$row) {
                $row['id_hospital'] = intval($row['id_hospital']);
				$row['id_province'] = intval($row['id_province']);
				$row['id_city'] = intval($row['id_city']);
                $row['id_cabinet'] = intval($row['id_cabinet']);
                $row['cabinet_status'] = intval($row['cabinet_status']);
            }
            $ret['list'] = $rows;
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' exception = ' . $e->getMessage());
            return false;
        }

        $ret['total_num'] = $this->get_cabinet_total_num($active_status);

        return $ret;
    }

    /**
     * 售货柜 - 新建
     * @param $id_hospital
     * @param $cd_key
     * @param $cabinet_name
     * @param $cabinet_address
     * @param $cabinet_status
     * @param $charge_person
     * @return bool|int
     */
    public function cabinet_add($id_hospital, $cd_key, $cabinet_name, $cabinet_address, $cabinet_status, $charge_person) {
        $param = [
            'id_hospital' => $id_hospital,
            'cd_key' => $cd_key,
            'cabinet_name' => $cabinet_name,
            'cabinet_address' => $cabinet_address,
            'cabinet_status' => $cabinet_status,
            'charge_person' => $charge_person
        ];

        $sql = 'insert into t_cabinet_cabinet(id_hospital, cd_key, cabinet_name, cabinet_address, cabinet_status, charge_person, create_ts, update_ts)';
        $sql .= ' values (:id_hospital, :cd_key, :cabinet_name, :cabinet_address, :cabinet_status, :charge_person, :create_ts, :update_ts)';
		
		$time_now = date('Y-m-d H:i:s');
        try {
            $stmt = $this->pdo_admin->prepare($sql);
            $ret = $stmt->execute(
                [
                    ':id_hospital' => $id_hospital,
                    ':cd_key' => $cd_key,
                    ':cabinet_name' => $cabinet_name,
                    ':cabinet_address' => $cabinet_address,
                    ':cabinet_status' => $cabinet_status,
                    ':charge_person' => $charge_person,
					':create_ts' => $time_now,
					':update_ts' => $time_now
                ]
            );
            if($ret === false) {
                Logger::info(__METHOD__ . ' db error, sql = ' . $sql . ', param = ' . json_encode($param));
                return false;
            }

            return intval($this->pdo_admin->lastInsertId());
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' exception = ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 售货柜 - 编辑
     * @param $id_hospital
	 * @param $id_province
	 * @param $id_city
     * @param $id_cabinet
     * @param $cabinet_name
     * @param $cabinet_address
     * @param $cabinet_status
     * @param $charge_person
     * @return bool
     */
    public function cabinet_edit($id_hospital, $id_province, $id_city, $id_cabinet, $cabinet_name, $cabinet_address, $cabinet_status, $charge_person) {
        $param = [
            'id_hospital' => $id_hospital,
			'id_province' => $id_province,
			'id_city' => $id_city,
            'id_cabinet' => $id_cabinet,
            'cabinet_name' => $cabinet_name,
            'cabinet_address' => $cabinet_address,
            'cabinet_status' => $cabinet_status,
            'charge_person' => $charge_person
        ];

        $sql = 'update t_cabinet_cabinet set id_hospital = :id_hospital, cabinet_name = :cabinet_name, cabinet_address = :cabinet_address';
		$sql .= ', id_province = :id_province, id_city = :id_city';
        $sql .= ', cabinet_status = :cabinet_status, charge_person = :charge_person, update_ts = :update_ts  where id = :id_cabinet';
		
		$time_now = date('Y-m-d H:i:s');
		try {
			$stmt = $this->pdo_admin->prepare($sql);
        	$ret = $stmt->execute(
            	[
            	    ':id_hospital' => $id_hospital,
					':id_province' => $id_province,
					':id_city' => $id_city,
	                ':id_cabinet' => $id_cabinet,
	                ':cabinet_name' => $cabinet_name,
    	            ':cabinet_address' => $cabinet_address,
        	        ':cabinet_status' => $cabinet_status,
            	    ':charge_person' => $charge_person,
					':update_ts' => $time_now
	            ]
    	    );
        	if($ret === false) {
                Logger::info(__METHOD__ . ' db error, sql = ' . $sql . ', param = ' . json_encode($param));
                return false;
            }

			$row_count = $stmt->rowCount();
			if($row_count <= 0) {
				Logger::info(__METHOD__ . ' affect 0 row');
				return false;
			}
		} catch (Exception $e) {
			Logger::info(__METHOD__ . ' exception = ' . $e->getMessage());
			return false;
		}

        return true;
    }

    /**
     * 库存 - 获取
     * todo 下单时创建异步任务, 24小时后未支付的解除锁定, 并更新为已取消
     * todo 其他操作导致的解除锁定
     * todo 给c端提供获取库存, 锁库存, 解锁库存的接口
     * @param $id_cabinet
     * @param $page_size
     * @param $page_num
     * @return array|bool
     */
    public function stock_get($id_cabinet, $page_size, $page_num) {
        $id_begin = ($page_num - 1) * $page_size;

        $sql = 'select id as id_stock, id_product, current_num as product_num, amount from t_cabinet_stock';
        $sql .= ' where id_cabinet = :id_cabinet order by id desc';
        $sql .= ' limit ' . $id_begin . ', ' . $page_size;

        $param = [
            'id_cabinet' => $id_cabinet,
            'page_size' => $page_size,
            'page_num' => $page_num
        ];

        try {
            $stmt = $this->pdo_admin->prepare($sql);
            $ret = $stmt->execute(
                [
                    ':id_cabinet' => $id_cabinet //todo
                ]
            );
            if($ret === false) {
                Logger::info(__METHOD__ . ', line = ' . __LINE__ . ' db error, sql = ' . $sql . ', param = ' . json_encode($param));
                return false;
            }

            $rows = $stmt->fetchAll();
            if(empty($rows)) {
                return [];
            }

            foreach ($rows as &$row) {
                $average_price_in = 0;
                $product_name = '';
                $price_out = '0.00';
                $product_status = 1; // 商品状态: 1在架, 2缺货

                $id_product = intval($row['id_product']);
                $sql = 'select sum(product_num) as product_num_locked from t_cabinet_stock_lock where id_cabinet = :id_cabinet';
                $sql .= ' and id_product = :id_product and is_lock = :is_lock';
                $stmt = $this->pdo_admin->prepare($sql);
                $stmt->execute(
                    [
                        ':id_cabinet' => $id_cabinet,
                        ':id_product' => $id_product,
                        ':is_lock' => 1
                    ]
                );
                $row_lock = $stmt->fetch();
                if (!empty($row_lock)) {
                    $product_num_locked = intval($row_lock['product_num_locked']);
                    $row['product_num'] -= $product_num_locked; // todo 库存当前显示的是减去被锁的数量, 是否需要把小于0的变为0
                }

                $stock_single_first = $this->get_single_stock_first($id_product);
                if (empty($stock_single_first)) {
                    Logger::info(__METHOD__ . ' stock_first empty, param = ' . json_encode($param));
                } else {
                    $average_price_in = $stock_single_first['total_money_in'] / $stock_single_first['total_num_in'];
                    $product_name = $stock_single_first['name'];
                    $price_out = number_format($stock_single_first['price'], 2, '.', '');
                }

                // 库存总金额 = 进货均价 * 当前库存num
                $product_num = intval($row['product_num']);
                if ($product_num > 0) {
                    $amount = $average_price_in * intval($row['product_num']);
                } else {
                    $amount = 0;
                }

                $row['id_stock'] = intval($row['id_stock']);
                $row['amount'] = number_format($amount, 2, '.', ''); // todo 库存字段可以去掉吧
                $row['id_product'] = intval($row['id_product']);
                $row['product_num'] = $product_num;
                $row['product_name'] = $product_name;
                $row['price_out'] = $price_out;

                if ($product_num <= 0) {
                    $product_status = 2;
                }
                $row['product_status'] = $product_status;
            }
            unset($row);
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' db error, exception = ' . $e->getMessage());
            return false;
        }

		$ret = [];
		$ret['list'] = $rows;
		$ret['total_num'] = $this->stock_get_total_num($id_cabinet);

        return $ret;
    }


    /**
     * 入库 - 新增 (新建商品)
     * todo notice 因为能直接编辑库存, 所以入库和出库记录没多大意义
     * $param $id_cabinet
     * @param $id_product
     * @param $num_in
     * @return bool
     */
    public function stock_in_add($id_cabinet, $id_product, $num_in) {
		// 售货柜的库存不能大于进销存的库存
		$stock_first = $this->get_single_stock_first($id_product);
		if (empty($stock_first)) {
			Logger::info(__METHOD__ . ' get_single_stock_first return empty, id_product = ' . $id_product);
			return false;
		}
		
		$stock_second = $this->get_single_stock_second($id_cabinet, $id_product);
		if (empty($stock_second)) {
			Logger::info(__METHOD__ . ' get_single_stock_first return empty, id_product = ' . $id_product);
			return false;
		}
		if ($num_in + intval($stock_second['current_num']) > intval($stock_first['num'])) {
			Logger::info(__MEHTOD__ . ' cabinet product_num cannot more than new_stock product_num, id_cabinet = '
				. $id_cabinet . ', id_product = ' . $id_product);
			return false;
		}

        $sql = 'insert into t_cabinet_stock_in(id_cabinet, id_product, num_in, create_ts, update_ts)';
        $sql .= ' values(:id_cabinet, :id_product, :num_in, :create_ts, :update_ts)';

        $time_now = date('Y-m-d H:i:s');
        $param = [
            'id_cabinet' => $id_cabinet,
            'id_product' => $id_product,
            'num_in' => $num_in
        ];

        try {
            // 更新入库
            $stmt = $this->pdo_admin->prepare($sql);
            $ret = $stmt->execute(
                [
                    ':id_cabinet' => $id_cabinet,
                    ':id_product' => $id_product,
                    ':num_in' => $num_in,
                    ':create_ts' => $time_now,
                    ':update_ts' => $time_now
                ]
            );

            if ($ret === false) {
                Logger::info(__METHOD__ . ' db error, sql = ' . $sql . ', param = ' . json_encode($param));
                return false;
            }

            /** 更新库存 */
            $single_cabinet = $this->get_single_cabinet($id_cabinet);
            if (empty($single_cabinet)) {
                Logger::info(__METHOD__ . ' cabinet empty, id_cabinet = ' . $id_cabinet);
                return false;
            }
            $cd_key = $single_cabinet['cd_key'];

            $num_add = $num_in;
            $this->update_stock($id_cabinet, $cd_key, $id_product, $num_add);
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' db error, exception = ' . $e->getMessage());
            return false;
        }

        return true;
    }

    public function stock_in_product_list() {
        $ret = array();

        $sql = 'select ts.id_product, spl.name';
        $sql .= ' from t_stock ts left join s_product_lang spl';
        $sql .= ' on ts.id_product = spl.id_product';
        $sql .= ' left join s_product sp on ts.id_product = sp.id_product';
        $sql .= ' where spl.id_lang = 1 and sp.active = 1';
        $sql .= ' order by ts.id_product';
        try {
            $stmt = $this->pdo_shop->prepare($sql);
            $stmt->execute();
            $ret = $stmt->fetchAll();
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' db error, exception = ' . $e->getMessage());
            return $ret;
        }

        return $ret;
    }

    /**
     * 入库 - 获取某商品信息
     * @param $id_product
     * @return array
     */
    public function stock_in_product_info($id_product) {
        $ret = [
            'product_price' => '0.00',
            'product_name' => ''
        ];

        $sql = 'select sp.price, spl.name from s_product sp left join s_product_lang spl';
        $sql .= ' on sp.id_product = spl.id_product and spl.id_lang = 1';
        $sql .= ' where sp.id_product = :id_product';
        try {
            $stmt = $this->pdo_shop->prepare($sql);
            $stmt->execute([
                ':id_product' => $id_product
            ]);
            $row = $stmt->fetch();
            if (!empty($row)) {
                $ret['product_name'] = $row['name'];
                $ret['product_price'] = number_format($row['price'], 2, '.', '');
            }
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' db error, exception = ' . $e->getMessage());
        }

        return $ret;
    }

    /**
     * 库存 - 编辑 (编辑商品)
     * todo 把库存表id和id_product的索引类型改变
     * todo 删除库存表的库存总金额amout字段, 库存总金额amount通过进销存的平均价格和自动售货柜的库存数量计算得到, 需要询问是否影响到小郭, 小郭那边的需求都改为给他提供相关接口
     * @param $id_stock
     * @param $product_num
     * @return bool
     */
    public function stock_edit($id_stock, $product_num) {
		// 售货柜的库存不能大于进销存的库存
		$id_product = 0;
		$sql = 'select id_product from t_cabinet_stock where id = :id_stock';
		try {
			$stmt = $this->pdo_admin->prepare($sql);
			$stmt->execute([
				':id_stock' => $id_stock
			]);
			$row = $stmt->fetch();

			if ($row !== false && empty($row)) {
				Logger::info(__METHOD__ . ' db select return empty, sql = ' . $sql . ', id_stock = ' . $id_stock);
				return false;
			}
			$id_product = intval($row['id_product']);
		} catch (Exception $e) {
			Logger::info(__METHOD__ . ' db error, exception = ' . $e->getMessage());
			return false;
		}

		$stock_first = $this->get_single_stock_first($id_product);
		if (empty($stock_first)) {
			Logger::info(__METHOD__ . ' get_single_stock_first return empty, id_product = ' . $id_product);
			return false;
		}
		if ($product_num > intval($stock_first['num'])) {
			Logger::info(__METHOD__ . ' cabinet product_num cannot more than new_stock product_num, id_stock = '
				. $id_stock . ', product_num = ' . $product_num);
			return false;
		}
		
        $sql = 'update t_cabinet_stock set current_num = :product_num, update_ts = :update_ts';
        $sql .= ' where id = :id_stock';

        $time_now = date('Y-m-d H:i:s');
        $param = [
            'id_stock' => $id_stock,
            'product_num' => $product_num,
            'update_ts' => $time_now
        ];

        try {
            // 更新库存
            $stmt = $this->pdo_admin->prepare($sql);
            $stmt->execute(
                [
                    ':id_stock' => $id_stock,
                    ':product_num' => $product_num,
                    ':update_ts' => $time_now
                ]
            );

            $row_count = $stmt->rowCount();
            if($row_count <= 0) {
                Logger::info(__METHOD__ . ' affect 0 row, sql = ' . $sql . ', param = ' . json_encode($param));
                return false;
            }
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' db error, exception = ' . $e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * 出库 - 获取
     * @param $date_begin
     * @param $date_end
     * @param $order_status
     * @param $page_num
     * @param $page_size
     * @return array|bool
     */
    public function stock_out_get($date_begin, $date_end, $order_status, $page_num, $page_size) {
        $ret = array();

        $pdo_param = [];
        $order_source = 4; // todo notice 目前不管哪个售货柜, 只需要获取新零售的, 订单来源线下表示新零售
        $order_type = 5; // notice 新零售的order_source为4, order_type为5

        $pdo_param['order_source'] = $order_source;
        $pdo_param['order_type'] = $order_type;

        $id_begin = ($page_num - 1) * $page_size;
        $sql = 'select id_order, reference, payment as pay_type, cd_key, date_add, current_state as order_status, total_paid - c_value as amount from s_orders';
        $sql .= ' where order_source = :order_source and order_type = :order_type';

        if (!(empty($date_begin) && empty($date_end) && $order_status === -1)) {
            if (!empty($date_begin)) {
                $pdo_param['date_begin'] = $date_begin;

                $sql .= ' and date_add >= :date_begin';
            }
            if (!empty($date_end)) {
                $pdo_param['date_end'] = $date_end;

                $sql .= ' and date_add <= :date_end';
            }
            if ($order_status !== -1) {
                $pdo_param['order_status'] = $order_status;

                $sql .= ' and current_state = :order_status';
            }
        }

        if ($page_num === 0) { // export
            $sql .= ' order by id_order desc';
        } else {
            $sql .= ' order by id_order desc limit ' . $id_begin . ', ' . $page_size;
        }

        try {
            $stmt = $this->pdo_shop->prepare($sql);
            $stmt->execute($pdo_param);

            $rows = $stmt->fetchAll();
            if (!empty($rows)) {
                foreach ($rows as &$row) {
                    $id_order = intval($row['id_order']);

					$row['id_order'] = $id_order;

                    $row['product_num'] = $this->get_order_product_num($id_order);

                    $tmp_order_status = intval($row['order_status']);
                    $row['order_status'] = isset(self::$INVALID_ORDER_STATE[$tmp_order_status])
                        ? self::$INVALID_ORDER_STATE[$tmp_order_status] : $tmp_order_status;
					
					if ($row['pay_type'] == 'alipay') {
						$row['pay_type'] = '支付宝';
					} else {
						$row['pay_type'] = '微信';
					}

					$cd_key = $row['cd_key'];
					$row['cabinet_name'] = '';
					$single_cabinet = $this->get_single_cabinet_by_cd_key($cd_key);
					if (!empty($single_cabinet)) {
						$row['cabinet_name'] = $single_cabinet['cabinet_name'];
					}
					unset($row['cd_key']);
                }
                unset($row);

                // todo now 目前返回空
                $whole_info = $this->get_stock_out_whole_info($order_source);

                $ret['list'] = $rows;
                $ret['total_num'] = $this->get_stock_out_total_num($date_begin, $date_end, $order_status);
                $ret['whole_num'] = $whole_info['whole_num'];
                $ret['whole_amount'] = $whole_info['whole_amount'];
            }
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' db error, exception = ' . $e->getMessage());
            return false;
        }

        return $ret;
    }

    /**
     * 出库 - 获取单笔出库详情
     * todo 目前一个订单只有一个商品, 利润需要有权限, 暂时不用后端处理
     * @param $id_order
     * @return bool|mixed
     */
    public function stock_out_get_detail($id_order) {
        $ret = array();

        $bll_order_config = new Bll_Order_Config();
        $type_list = $bll_order_config->get_order_config();
        $order_type_arr = [];
        foreach ($type_list['order_type'] as $v) {
            $order_type_arr[$v['id']] = $v['name'];
        }

        $sql = 'select sod.product_id, sod.product_quantity,';
        $sql .= ' spl.name as product_name,';
        $sql .= ' sp.price as price_out,';
        $sql .= ' scp.id_category,';
        $sql .= ' scl.name as category_name';
        $sql .= ' from s_order_detail sod left join s_product_lang spl on sod.product_id = spl.id_product and spl.id_lang = 1';
        $sql .= ' left join s_product sp on sod.product_id = sp.id_product';
        $sql .= ' left join s_category_product scp on sod.product_id = scp.id_product';
		$sql .= ' left join s_category sc on scp.id_category = sc.id_category'; 
		$sql .= ' left join s_category_lang scl on scp.id_category = scl.id_category and scl.id_lang = 1';
        $sql .= ' where sod.id_order = :id_order';
		$sql .= ' order by sc.level_depth desc limit 1';
        try {
            $stmt = $this->pdo_shop->prepare($sql);
            $stmt->execute(
                [
                    ':id_order' => $id_order
                ]
            );
            $rows = $stmt->fetchAll();

			//test
			Logger::info('ok11, num = ' . count($rows) . ', id_order = ' . $id_order . ', sql = ' . $sql);

            if (empty($rows)) {
                Logger::info(__METHOD__ . ' db search return empty, sql = ' . $sql . ', id_order = ' . $id_order);
                return $ret;
            }

            foreach ($rows as $k => &$row) { // todo 跟上面合并为一个sql
                $id_product = intval($row['product_id']);
                $price_out = $row['price_out'];

                $stock_single_first = $this->get_single_stock_first($id_product);
                $average_price_in = $stock_single_first['total_money_in'] / $stock_single_first['total_num_in'];
                $average_price_in = number_format($average_price_in, 2, '.', '');

                $earnings = $price_out - $average_price_in > 0 ? $price_out - $average_price_in : 0;
                $earnings = number_format($earnings, 2, '.', '');

                $row['price_in'] = $average_price_in; // 成本(进价)
                $row['earnings'] = $earnings; // 利润

                $row['pay_type'] = '';
                $row['order_status'] = '';
                $row['date_add'] = '';

                $sql = 'select reference, payment as pay_type, current_state as order_status, date_add from s_orders';
                $sql .= ' where id_order = :id_order';
                try {
                    $stmt = $this->pdo_shop->prepare($sql);
                    $stmt->execute([
                        ':id_order' => $id_order
                    ]);
                    $row_tmp = $stmt->fetch();
                    if (!empty($row_tmp)) {
                        $pay_type = $row_tmp['pay_type'];
                        $order_status = intval($row_tmp['order_status']);
                        $date_add = $row_tmp['date_add'];

                        $row['order_status'] = isset(self::$INVALID_ORDER_STATE[$order_status])
                            ? self::$INVALID_ORDER_STATE[$order_status] : $order_status;
						
						if ($pay_type == 'alipay') {
							$row['pay_type'] = '支付宝';
						} else {
							$row['pay_type'] = '微信';
						}
                        $row['date_add'] = $date_add;

						$row['reference'] = $row_tmp['reference'];
                    }
                } catch (Exception $e) {
                    Logger::info(__METHOD__ . ' db error, exception = ' . $e->getMessage());
                }

                // 格式化
                if (is_null($row['product_name'])) {
                    $row['product_name'] = '';
                }
                if (is_null($row['category_name'])) {
                    $row['category_name'] = '';
                }
                if (is_null($row['price_out'])) {
                    $row['price_out'] = '0.00';
                }
                $row['id_order'] = $id_order;
                $row['price_out'] = number_format($row['price_out'], 2, '.', '');
                $row['product_id'] = $id_product;
                $row['product_quantity'] = intval($row['product_quantity']);
                $row['id_category'] = intval($row['id_category']);
            }
            unset($row);

            $ret['list'] = $rows;
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' db error, sql = ' . $sql . ', id_order = ' . $id_order);
            return false;
        }

        return $ret;
    }

    /**
     * 出库 - 导出
     * @param $order_status
     * @return bool
     */
    public function stock_out_export($order_status) {
        $data = $this->stock_out_get('', '', $order_status, 0, 0);
        if(empty($data) || empty($data['list'])) {
            $rows = [];
        } else {
            $rows = $data['list'];
        }

        $this->export_excel($rows);

        return true;
    }

    /**
     * 货道管理 - 获取
     * todo 客户端不能依靠我的排序吧, 网络传输时是顺序的吗
     * todo 为什么发出去的数组内容都是倒序的
     * @param $id_cabinet
     * @return array
     */
    public function counter_get($id_cabinet) {
        $ret = [];

        $counter_row_column_config = [
            1 => [
                'row_name' => 'A',
                'column_num' => 8,
                'depth' => 6
            ],
            2 => [
                'row_name' => 'B',
                'column_num' => 8,
                'depth' => 6
            ],
            3 => [
                'row_name' => 'C',
                'column_num' => 6,
                'depth' => 4
            ],
            4 => [
                'row_name' => 'D',
                'column_num' => 6,
                'depth' => 4
            ],
            5 => [
                'row_name' => 'E',
                'column_num' => 4,
                'depth' => 3
            ],
            6 => [
                'row_name' => 'F',
                'column_num' => 4,
                'depth' => 3
            ],
        ];

        // 生成默认返回值
        foreach ($counter_row_column_config as $k => $v) {
            $row_name = $v['row_name'];
            $depth = $v['depth'];
            $max_column_num = $v['column_num'];

            $list = [];
            for ($i = 1; $i <= $max_column_num; ++$i) {
                $list[] = [
                    'column' => $i,
                    'id_product' => 0, // 0表示该位置未分配商品
                    'product_name' => '',
                    'product_num' => 0
                ];
            }

            $ret[$k] = [
                'list' => $list,
                'depth' => $depth,
                'row' => $row_name
            ];
        }

        $sql = 'select counter_row, counter_column, id_product, product_num from t_cabinet_counter';
        $sql .= ' where id_cabinet = :id_cabinet';
        $sql .= ' and counter_row in (' . implode(',', array_keys($counter_row_column_config)) . ')';
        try {
            $stmt = $this->pdo_admin->prepare($sql);
            $stmt->execute([
                ':id_cabinet' => $id_cabinet
            ]);
            $rows = $stmt->fetchAll();

            if (empty($rows)) {
                return array_values($ret);
            }

            // 将默认返回值更新为实际返回值
            foreach ($rows as $row) {
                $counter_row = intval($row['counter_row']);
                $counter_column = intval($row['counter_column']);
                $id_product = intval($row['id_product']);
                $product_num = intval($row['product_num']);

                $product_name = '';
                $sql = 'select name as product_name from s_product_lang';
                $sql .= ' where id_product = :id_product and id_lang = :id_lang';
                $stmt = $this->pdo_shop->prepare($sql);
                $stmt->execute([
                    ':id_product' => $id_product,
                    ':id_lang' => 1
                ]);
                $row = $stmt->fetch();
                if (!empty($row) && !empty($row['product_name'])) {
                    $product_name = $row['product_name'];
                }
				
				// 商品数量减去锁定数量
				$sql = 'select sum(product_num) as product_num_locked from t_cabinet_stock_lock where id_cabinet = :id_cabinet';
				$sql .= ' and id_product = :id_product and is_lock = :is_lock';
				$stmt = $this->pdo_admin->prepare($sql);
				$stmt->execute([
					':id_cabinet' => $id_cabinet,
					':id_product' => $id_product,
					':is_lock' => 1
				]);
				$row_lock = $stmt->fetch();
				if (!empty($row_lock)) {
					$product_num_lock = intval($row_lock['product_num_locked']);
					$product_num -= $product_num_lock;
					if ($product_num < 0) {
						$product_num = 0;
					} 
				}

                foreach ($ret as $k => &$v) {
                    if ($k === $counter_row) {
                        foreach ($v['list'] as &$v_column) {
                            if (intval($v_column['column']) === $counter_column) {
                                $v_column['id_product'] = $id_product;
                                $v_column['product_name'] = $product_name;
                                $v_column['product_num'] = $product_num;

                                break;
                            }
                        }
                        unset($v_column);

                        break;
                    }
                }
                unset($v);
            }
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' db error, exception = ' . $e->getMessage());
            return array_values($ret);
        }

        return array_values($ret);
    }

    /**
     * 货道管理 - 返回售货柜库存中可用商品列表
     * @param $id_cabinet
     * @param $key_word
     * @return array
     */
    public function counter_product_list($id_cabinet, $key_word) {
        $ret = [];

        $sql = 'select id_product, current_num as product_num from t_cabinet_stock';
        $sql .= ' where id_cabinet = :id_cabinet';

        try {
            $stmt = $this->pdo_admin->prepare($sql);
            $stmt->execute([
                ':id_cabinet' => $id_cabinet
            ]);
            $rows = $stmt->fetchAll();

            if (empty($rows)) {
                return $ret;
            }

            foreach ($rows as $k => &$row) {
                $id_product = intval($row['id_product']);
                $product_name = '';
                $product_num = intval($row['product_num']);

                $sql = 'select name as product_name from s_product_lang';
                $sql .= ' where id_product = :id_product and id_lang = :id_lang';

                try {
                    $stmt = $this->pdo_shop->prepare($sql);
                    $stmt->execute([
                        ':id_product' => $id_product,
                        ':id_lang' => 1
                    ]);
                    $row_name = $stmt->fetch();
                    if (!empty($row_name) && !empty($row_name['product_name'])) {
                        $product_name = $row_name['product_name'];
                    }
                } catch (Exception $e) {
                    Logger::info(__METHOD__ . ' db error, exception = ' . $e->getMessage());
                    unset($rows[$k]);
                    continue;
                }

                if (!empty($key_word) && strpos($product_name, $key_word) === false) {
                    unset($rows[$k]);
                    continue;
                }

                $sql = 'select sum(product_num) as product_num_locked from t_cabinet_stock_lock where id_cabinet = :id_cabinet';
                $sql .= ' and id_product = :id_product and is_lock = :is_lock';
                $stmt = $this->pdo_admin->prepare($sql);
                $stmt->execute(
                    [
                        ':id_cabinet' => $id_cabinet,
                        ':id_product' => $id_product,
                        ':is_lock' => 1
                    ]
                );
                $row_lock = $stmt->fetch();
                if (!empty($row_lock)) {
                    $product_num_locked = intval($row_lock['product_num_locked']);
                    $product_num -= $product_num_locked; // todo 库存当前显示的是减去被锁的数量, 是否需要把小于0的变为0
                }

                $row['id_product'] = $id_product;
                $row['product_name'] = $product_name;
                $row['product_num'] = $product_num;
            }
            unset($row);
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' db error, exception = ' . $e->getMessage());
            return $ret;
        }

        return array_values($rows);
    }

    /**
     * 货道管理 - 增加商品数量
     * todo 售货柜数量上限
     * @param $id_cabinet
     * @param $counter_row
     * @param $counter_column
     * @param $add_num
     * @return array|bool
     */
    public function counter_add($id_cabinet, $counter_row, $counter_column, $add_num) {
        $ret = [];

        /** 检查该位置是否能增加商品数量 */
        $sql = 'select id_product from t_cabinet_counter';
        $sql .= ' where id_cabinet = :id_cabinet and counter_row = :counter_row and counter_column = :counter_column';

        try {
            $stmt = $this->pdo_admin->prepare($sql);
            $stmt->execute([
                ':id_cabinet' => $id_cabinet,
                ':counter_row' => $counter_row,
                ':counter_column' => $counter_column
            ]);
            $row = $stmt->fetch();
            if (empty($row) || empty($row['id_product'])) {
                $param = [
                    'id_cabinet' => $id_cabinet,
                    'counter_row' => $counter_row,
                    'counter_column' => $counter_column
                ];
                Logger::info(__METHOD__ . ' counter_add refuse! illegal operation! param = ' . json_encode($param));
                return false;
            }

            /** 检测库存 todo */

            /** 增加商品数量 */
            $sql = 'update t_cabinet_counter set product_num = product_num + :add_num';
            $sql .= ' where id_cabinet = :id_cabinet and counter_row = :counter_row and counter_column = :counter_column';
            $stmt = $this->pdo_admin->prepare($sql);
            $stmt->execute([
                ':add_num' => $add_num,
                ':id_cabinet' => $id_cabinet,
                ':counter_row' => $counter_row,
                ':counter_column' => $counter_column
            ]);

            /** 返回该商品当前数量 */
            $sql = 'select id_product, product_num from t_cabinet_counter';
            $sql .=' where id_cabinet = :id_cabinet and counter_row = :counter_row and counter_column = :counter_column';
            $stmt = $this->pdo_admin->prepare($sql);
            $stmt->execute([
                ':id_cabinet' => $id_cabinet,
                ':counter_row' => $counter_row,
                ':counter_column' => $counter_column
            ]);
            $row = $stmt->fetch();
            if (empty($row)) {
                Logger::info(__METHOD__ . ' db error, here should not be empty');
                return false;
            }

            $ret['id_product'] = intval($row['id_product']);
            $ret['product_num'] = intval($row['product_num']);

            return $ret;
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' db error, exception = ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 货道管理 - 分配商品
     * todo 数据库是否需要加时间字段
     * todo 检测是否存在这样的行列
     * @param $id_cabinet
     * @param $counter_row
     * @param $counter_column
     * @param $id_product
     * @param $assign_num
     * @return bool
     */
    public function counter_assign($id_cabinet, $counter_row, $counter_column, $id_product, $assign_num) {
        $ret = false;

        /** 检测该位置是否能被分配 $sql */
        $sql = 'select id_product from t_cabinet_counter';
        $sql .= ' where id_cabinet = :id_cabinet and counter_row = :counter_row and counter_column = :counter_column';
        try {
            $stmt = $this->pdo_admin->prepare($sql);
            $stmt->execute([
                ':id_cabinet' => $id_cabinet,
                ':counter_row' => $counter_row,
                ':counter_column' => $counter_column
            ]);
            $row = $stmt->fetch();
            if (!empty($row) && !empty($row['id_product'])) {
                Logger::info(__METHOD__ . ' cannot assign, current id_product = ' . intval($row['id_product']));
                return false;
            }

            /** 检测库存是否足够 todo */

            /** 检测数量是否超过最大值 todo */

            /** 分配商品 */
            if (empty($row)) {
                $sql = 'insert into t_cabinet_counter(id_cabinet, counter_row, counter_column, id_product, product_num)';
                $sql .= ' values (:id_cabinet, :counter_row, :counter_column, :id_product, :product_num)';
            } else {
                $sql = 'update t_cabinet_counter set id_product = :id_product, product_num = :product_num';
                $sql .= ' where id_cabinet = :id_cabinet and counter_row = :counter_row and counter_column = :counter_column';
            }
            $stmt = $this->pdo_admin->prepare($sql);
            $stmt->execute([
                ':id_cabinet' => $id_cabinet,
                ':counter_row' => $counter_row,
                ':counter_column' => $counter_column,
                ':id_product' => $id_product,
                ':product_num' => $assign_num
            ]);
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' db error, exception = ' . $e->getMessage());
            return $ret;
        }

        return true;
    }

    /**
     * 货道管理 - 清除某位置的商品
     * @param $id_cabinet
     * @param $counter_row
     * @param $counter_column
     * @return bool
     */
    public function counter_clear($id_cabinet, $counter_row, $counter_column) {
        $ret = false;

        $sql = 'update t_cabinet_counter set id_product = :id_product, product_num = :product_num';
        $sql .= ' where id_cabinet = :id_cabinet and counter_row = :counter_row and counter_column = :counter_column';
        try {
            $stmt = $this->pdo_admin->prepare($sql);
            $stmt->execute([
                ':id_cabinet' => $id_cabinet,
                ':counter_row' => $counter_row,
                ':counter_column' => $counter_column,
                ':id_product' => 0,
                ':product_num' => 0
            ]);
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' db error, exception = ' . $e->getMessage());
            return $ret;
        }

        return true;
    }

    /**
     * 库存 - 根据cd_key获取某商品当前售货柜机器的可用库存数量 - 后端间的接口
     * @param $cd_key
     * @param $id_product
     * @return array|bool
     */
    public function counter_get_one($cd_key, $id_product) {
        $ret = [];
        $product_num = 0;

        // 根据cd_key获取id_cabinet todo 暂时需求不用
//        $id_cabinet = $this->get_id_cabinet_by_cd_key($cd_key);
        $id_cabinet = 1;

        /** 获取该商品的库存数量 */
        $sql = 'select sum(product_num) as product_num from t_cabinet_counter';
        $sql .= ' where id_cabinet = :id_cabinet and id_product = :id_product';

        try {
            $stmt = $this->pdo_admin->prepare($sql);
            $stmt->execute([
                ':id_cabinet' => $id_cabinet,
                ':id_product' => $id_product
            ]);
            $row = $stmt->fetch();
            if (empty($row)) {
                $param = [
                  'id_cabinet' => $id_cabinet,
                  'id_product' => $id_product
                ];
                Logger::info(__METHOD__ . ' db search ret is empty, param = ' . json_encode($param) . ', sql = ' . $sql);
                return $product_num;
            }
            $product_num = intval($row['product_num']);

            /** 获取被锁定的商品数量 */
            $sql = 'select sum(product_num) as product_num_locked from t_cabinet_stock_lock where id_cabinet = :id_cabinet';
            $sql .= ' and id_product = :id_product and is_lock = :is_lock';
            $stmt = $this->pdo_admin->prepare($sql);
            $stmt->execute(
                [
                    ':id_cabinet' => $id_cabinet,
                    ':id_product' => $id_product,
                    ':is_lock' => 1
                ]
            );
            $row_lock = $stmt->fetch();
			//test
			Logger::info('test1, id_product = ' . $id_product . ', product_num = ' . $product_num . ', row_lock = ' . json_encode($row_lock));
            if (!empty($row_lock)) {
                $product_num_locked = intval($row_lock['product_num_locked']);

                // 计算出可用库存
                $product_num -= $product_num_locked; // todo 库存当前显示的是减去被锁的数量, 是否需要把小于0的变为0

                if ($product_num < 0) {
                    Logger::info(__METHOD__ . ' available product_num less than 0, product_num = ' . $product_num
                        . ', product_num_locked = ' . $product_num_locked);

                    $product_num = 0;
                }
            }

            $ret['product_num'] = $product_num;
            return $ret;
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' db error, exception = ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 库存 - 加锁 - 后端间的接口 (加锁会影响售货柜库存 和 售货柜机器库存, 在获取相关库存时起作用)
     * todo 需要注意! 加锁只针对下单时, 作用是为了下单时库存不足不让下单
     * todo: 根据需求只考虑一个订单包含一个商品id
     * @param $cd_key
     * @param $id_order
     * @param $order_type - 1线上订单(sho_db_for_test.s_order_detail), 2线下临时表订单(newsale_db.s_customer_group)
     * @return bool
     */
    public function stock_lock($cd_key, $id_order, $order_type) { // todo now
        // 根据cd_key获取id_cabinet todo 暂时需求不用
//        $id_cabinet = $this->get_id_cabinet_by_cd_key($cd_key);
        $id_cabinet = 1;

        $id_product = 0;
        $product_num = 0;

        /* 获取订单中商品信息 */
        switch ($order_type) {
            case 1: // 线上订单
                $sql = 'select product_id, product_quantity from s_order_detail';
                $sql .= ' where id_order = :id_order';
                try {
                    $stmt = $this->pdo_shop->prepare($sql);
                    $stmt->execute([
                        ':id_order' => $id_order
                    ]);
                    $row = $stmt->fetch();
                    if (empty($row)) {
                        Logger::info(__METHOD__ . ' db error, select return empty, sql = ' . $sql . ', id_order = ' . $id_order);
                        return false;
                    }

                    $id_product = intval($row['product_id']);
                    $product_num = intval($row['product_quantity']);
                } catch (Exception $e) {
                    Logger::info(__METHOD__ . ' db error, exception = ' . $e->getMessage());
                    return false;
                }
                break;
            case 2: // 临时表订单 todo now quantity
                $pdo_newsale = APF_DB_Factory::get_instance()->get_pdo('newsale_master');
                $sql = 'select id_product, quantity from s_customer_group';
                $sql .= ' where id_customer_group = :id_order';
                try {
                    $stmt = $pdo_newsale->prepare($sql);
                    $stmt->execute([
                        ':id_order' => $id_order
                    ]);
                    $row = $stmt->fetch();
                    if (empty($row)) {
                        Logger::info(__METHOD__ . ' db error, select return empty, sql = ' . $sql . ', id_order = ' . $id_order);
                        return false;
                    }

                    $id_product = intval($row['id_product']);
                    $product_num = intval($row['quantity']);
                } catch (Exception $e) {
                    Logger::info(__METHOD__ . ' db error, exception = ' . $e->getMessage());
                    return false;
                }
                break;
            default:
                Logger::info(__METHOD__ . ' invalid type, type = ' . $order_type);
                return false;
        }

        try {
            /* 锁库存 todo now 库存锁定 - 通过swoole异步, 下单时, 下单24小时后判断出库状态, 要有一张表记录相关出库和状态 */
            $is_lock = 1;
            $time_now = date('Y-m-d H:i:s');
            $sql = 'insert into t_cabinet_stock_lock(id_cabinet, order_type, id_order, id_product, product_num, is_lock, create_ts, update_ts)';
            $sql .= ' values(:id_cabinet, :order_type, :id_order, :id_product, :product_num, :is_lock, :create_ts, :update_ts)';

            $stmt = $this->pdo_admin->prepare($sql);
            $stmt->execute(
                [
                    ':id_cabinet' => $id_cabinet,
                    ':order_type' => $order_type,
                    ':id_order' => $id_order,
                    ':id_product' => $id_product,
                    ':product_num' => $product_num,
                    ':is_lock' => $is_lock,
                    ':create_ts' => $time_now,
                    ':update_ts' => $time_now
                ]
            );

            $insert_id = $this->pdo_admin->lastInsertId();

            // 24小时后该记录如果还是锁定状态, 解锁之 - swoole异步毫秒定时器 todo now 框架怎么用不了
//            swoole_timer_after(86400000, function ($insert_id) {
//                $sql = 'select is_lock from t_cabinet_stock_lock where id = :insert_id';
//                $stmt = $this->pdo_admin->prepare($sql);
//                $stmt->execute([
//                    ':insert_id' => $insert_id
//                ]);
//                $row = $stmt->fetch();
//                if (empty($row)) {
//                    Logger::info(__METHOD__ . ' db select return empty, sql = ' . $sql . ', insert_id = ' . $insert_id);
//                } else {
//                    $is_lock = intval($row['is_lock']);
//                    if ($is_lock === 1) { // is_lock: 1锁定, 2解锁
//                        $sql = 'update t_cabinet_stock_lock set is_lock = :is_lock where id = :insert_id';
//                        $stmt = $this->pdo_admin->prepare($sql);
//                        $stmt->execute([
//                            ':insert_id' => $insert_id,
//                            ':is_lock' => 2
//                        ]);
//                    }
//                }
//            }, $insert_id);
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' db error, exception = ' . $e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * 库存 - 解锁 - 后端间的接口 todo now
     * @param $cd_key
     * @param $id_order
     * @param $order_type
     * @return bool
     */
    public function stock_unlock($cd_key, $id_order, $order_type) {
        // 根据cd_key获取id_cabinet todo 暂时需求不用
//        $id_cabinet = $this->get_id_cabinet_by_cd_key($cd_key);
        $id_cabinet = 1;

        /** 检测是否需要解锁 todo */

        $is_lock = 2; // 1是, 2否
        $time_now = date('Y-m-d H:i:s');

        $sql = 'update t_cabinet_stock_lock set is_lock = :is_lock, update_ts = :update_ts';
        $sql .= ' where id_cabinet = :id_cabinet and order_type = :order_type and id_order = :id_order';
        try {
            $stmt = $this->pdo_admin->prepare($sql);
            $stmt->execute([
                ':id_cabinet' => $id_cabinet,
                ':order_type' => $order_type,
                ':id_order' => $id_order,
                ':is_lock' => $is_lock,
                ':update_ts' => $time_now
            ]);
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' db error, exception = ' . $e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * 出库 - 计算出货口位置和商品数量 - 后端间的接口
     * todo 必须要有事务
     * todo 需要的话返回错误信息, 很简单
     * @param $cd_key
     * @param $id_order
	 * @param $order_type 订单类型 1线上订单, 2线下临时表订单
	 * @param $extra 如果为临时表订单， 该字段为线上订单id
     * @return array|bool
     */
    public function stock_out_compute($cd_key, $id_order, $order_type, $extra) {
        $ret = [];

        // 根据cd_key获取id_cabinet todo 暂时需求不用
//        $id_cabinet = $this->get_id_cabinet_by_cd_key($cd_key);
        $id_cabinet = 1;

        /** 获取该订单中商品信息, 从库存锁表中获取能检测是否操作是否合法 */
        $id_product = 0;
        $need_product_num = 0;
        $sql = 'select id_product, product_num from t_cabinet_stock_lock';
        $sql .= ' where id_cabinet = :id_cabinet and id_order = :id_order and order_type = :order_type and is_lock = :is_lock';
        try {
            $stmt = $this->pdo_admin->prepare($sql);
            $stmt->execute([
                ':id_cabinet' => $id_cabinet,
                ':id_order' => $id_order,
				':order_type' => $order_type,
                ':is_lock' => 1
            ]);
            $row = $stmt->fetch();
            if (empty($row)) {
                Logger::info(__METHOD__ . ' get product info fail, cd_key = ' . $cd_key . ', id_order = ' . $id_order);
                return false;
            }

            $id_product = intval($row['id_product']);
            $need_product_num = intval($row['product_num']);

            // 合法性检测
            if ($id_product < 1 || $need_product_num < 1) {
                Logger::info(__METHOD__ . ' db search return invalid, sql = ' . $sql . ', ret = ' . json_encode($row));
                return false;
            }
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' db error, exception = ' . $e->getMessage());
            return false;
        }

        /** 计算出货口和数量, 出货口选择优先级根据商品数量从大到小 */
        $sql = 'select counter_row, counter_column, product_num from t_cabinet_counter';
        $sql .= ' where id_cabinet = :id_cabinet and id_product = :id_product';
        $sql .= ' order by product_num desc';
        try {
            $stmt = $this->pdo_admin->prepare($sql);
            $stmt->execute([
                ':id_cabinet' => $id_cabinet,
                ':id_product' => $id_product
            ]);
            $rows = $stmt->fetchAll();
            if (empty($rows)) {
                Logger::info(__METHOD__ . ' db search return empty, sql = ' . $sql . ', id_cabinet = ' . $id_cabinet
                    . ', id_product = ' . $id_product);
                return false;
            }

            $tmp_product_num = 0;
            $list = [];
            $config = [ // todo
                0 => 0,
                1 => 8,
                2 => 8,
                3 => 6,
                4 => 6,
                5 => 4,
                6 => 4
            ];
            foreach ($rows as $k => $row) { // todo 全部数量都不够时返回失败
                $counter_row = intval($row['counter_row']);
                $counter_column = intval($row['counter_column']);
                $product_num = intval($row['product_num']);

                if (!isset($config[$counter_row])) {
                    Logger::info(__METHOD__ . ' invalid counter_row, counter_row = ' . $counter_row);
                    return false;
                }

                $tmp_arr = array_slice($config, 0, $counter_row, true);
                $id = array_sum($tmp_arr) + $counter_column - 1;

                $tmp_product_num_old = $tmp_product_num;
                $tmp_product_num += intval($row['product_num']);
                if ($tmp_product_num >= $need_product_num) {
                    $product_num = $need_product_num - $tmp_product_num_old;
                    $list[] = [
                        'id_position' => $id,
                        'num' => $product_num
                    ];
                    break;
                }

                $list[] = [
                    'id_position' => $id,
                    'num' => $product_num
                ];
            }

            /** 保存list, 用于出货成功stock_out_success接口 */
			if ($order_type === 2) { // 临时表订单id转为正式订单id
				$id_order = $extra;
			}
            $sql = 'insert into t_cabinet_stock_out_pos_num(id_cabinet, id_order, list)';
            $sql .= ' values(:id_cabinet, :id_order, :list)';
            $stmt = $this->pdo_admin->prepare($sql);
            $stmt->execute([
                ':id_cabinet' => $id_cabinet,
                ':id_order' => $id_order,
                ':list' => json_encode($list)
            ]);

            $ret['list'] = $list;
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' db error, exception = ' . $e->getMessage());
            return false;
        }

        /** 返回出货口和数量 */
        return $ret;
    }

    /**
     * 出库 - 出货成功 - 后端间的接口
     * @param $cd_key
     * @param $id_order
     * @return bool
     */
    public function stock_out_success($cd_key, $id_order) {
        /** 获取list - stock_out_compute生成的那个list */
        $list = [];
        $sql = 'select list from t_cabinet_stock_out_pos_num';
        $sql .= ' where id_order = :id_order';
        try {
            $stmt = $this->pdo_admin->prepare($sql);
            $stmt->execute([
                ':id_order' => $id_order
            ]);
            $row = $stmt->fetch();
            if ($row !== false && empty($row)) {
                Logger::info(__METHOD__ . ' db search result empty, sql = ' . $sql . ', id_order = ' . $id_order);
                return false;
            }
            $list = json_decode($row['list'], true);
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' db error, exception = ' . $e->getMessage());
            return false;
        }


        // 根据cd_key获取id_cabinet todo 暂时需求不用
//        $id_cabinet = $this->get_id_cabinet_by_cd_key($cd_key);
        $id_cabinet = 1;

        /** 获取该订单中商品信息, 从库存锁表中获取能检测是否操作是否合法 todo 搞成一个方法*/
        $id_product = 0;
        $need_product_num = 0;
        $sql = 'select id_product, product_num from t_cabinet_stock_lock';
        $sql .= ' where id_cabinet = :id_cabinet and id_order = :id_order and is_lock = :is_lock';
        try {
            $stmt = $this->pdo_admin->prepare($sql);
            $stmt->execute([
                ':id_cabinet' => $id_cabinet,
                ':id_order' => $id_order,
                ':is_lock' => 1
            ]);
            $row = $stmt->fetch();
            if (empty($row)) {
                Logger::info(__METHOD__ . ' get product info fail, cd_key = ' . $cd_key . ', id_order = ' . $id_order);
                return false;
            }

            $id_product = intval($row['id_product']);
            $need_product_num = intval($row['product_num']);

            // 合法性检测
            if ($id_product < 1 || $need_product_num < 1) {
                Logger::info(__METHOD__ . ' db search return invalid, sql = ' . $sql . ', ret = ' . json_encode($row));
                return false;
            }
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' db error, exception = ' . $e->getMessage());
            return false;
        }

        $list_total_num = 0;
        foreach ($list as $v) {
            $list_total_num += intval($v['num']);
        }
        // todo test
//        if ($list_total_num !== $need_product_num) {
//            Logger::info(__METHOD__ . ' invalid param , list = ' . json_encode($list) . ', need_product_num = '
//                . $need_product_num . ', list_total_num = ' . $list_total_num);
//            return false;
//        }

        /** 判断售货柜库存 和 售货柜机器库存 是否足够 todo */


        /** 连接数据库 - 为启用事务做准备 */
        try { // todo 正式服要修改为正式配置
            $dsn = ADMIN_DB_DSN; //'mysql:host=10.28.150.218;dbname=usercenter_db'
            $username = ADMIN_DB_USERNAME; //'kkhfast01',
            $passwd = ADMIN_DB_PASSWD; //'ufxjp3x#z&',
            $pdo_admin = new PDO($dsn, $username, $passwd, [PDO::ATTR_PERSISTENT => true]); //初始化一个PDO对象
            $pdo_admin->exec('SET CHARACTER SET utf8mb4');
            $pdo_admin->exec('SET NAMES utf8mb4');
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' db error, unable to connect, exception = ' . $e->getMessage());
            return false;
        }

        try {
            $pdo_admin->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $pdo_admin->beginTransaction(); // todo pdo事务可以prepare预处理吗

            $time_now = date('Y-m-d H:i:s');

            /** 库存解锁 */
            $is_lock = 2; // 1是, 2否
            $sql = 'update t_cabinet_stock_lock set is_lock = :is_lock, update_ts = :update_ts';
            $sql .= ' where id_cabinet = :id_cabinet and id_order = :id_order';
            $stmt = $pdo_admin->prepare($sql);
            $stmt->execute([
                ':id_cabinet' => $id_cabinet,
                ':id_order' => $id_order,
                ':is_lock' => $is_lock,
                ':update_ts' => $time_now
            ]);

            /** 减售货柜库存 */
            $num_add = -$need_product_num;
            $sql = 'select id from t_cabinet_stock where id_cabinet = :id_cabinet and id_product = :id_product';
            $stmt = $pdo_admin->prepare($sql);
            $stmt->execute(
                [
                    ':id_cabinet' => $id_cabinet,
                    ':id_product' => $id_product
                ]
            );
            $row = $stmt->fetch();
            if (empty($row)) { // insert
                $sql = 'insert into t_cabinet_stock(id_cabinet, cd_key, id_product, current_num, create_ts, update_ts) values';
                $sql .= '(:id_cabinet, :cd_key, :id_product, :current_num, :create_ts, :update_ts)';
                $stmt = $pdo_admin->prepare($sql);
                $stmt->execute(
                    [
                        ':id_cabinet' => $id_cabinet,
                        ':cd_key' => $cd_key,
                        ':id_product' => $id_product,
                        ':current_num' => $num_add,
                        ':create_ts' => $time_now,
                        ':update_ts' => $time_now
                    ]
                );
            } else { // update
                $sql = 'update t_cabinet_stock set current_num = current_num + :num_add,';
                $sql .= ' update_ts = :update_ts where id_cabinet = :id_cabinet and id_product = :id_product';
                $stmt = $pdo_admin->prepare($sql);
                $stmt->execute(
                    [
                        ':id_cabinet' => $id_cabinet,
                        ':id_product' => $id_product,
                        ':num_add' => $num_add,
                        ':update_ts' => $time_now
                    ]
                );
            }

            /** 减售货柜机器库存 */
            $id_row_column_config = [ // 根据id获取counter_row和counter_column
                0 => ['row' => 1, 'column' => 1],
                1 => ['row' => 1, 'column' => 2],
                2 => ['row' => 1, 'column' => 3],
                3 => ['row' => 1, 'column' => 4],
                4 => ['row' => 1, 'column' => 5],
                5 => ['row' => 1, 'column' => 6],
                6 => ['row' => 1, 'column' => 7],
                7 => ['row' => 1, 'column' => 8],

                8 => ['row' => 2, 'column' => 1],
                9 => ['row' => 2, 'column' => 2],
                10 => ['row' => 2, 'column' => 3],
                11 => ['row' => 2, 'column' => 4],
                12 => ['row' => 2, 'column' => 5],
                13 => ['row' => 2, 'column' => 6],
                14 => ['row' => 2, 'column' => 7],
                15 => ['row' => 2, 'column' => 8],

                16 => ['row' => 3, 'column' => 1],
                17 => ['row' => 3, 'column' => 2],
                18 => ['row' => 3, 'column' => 3],
                19 => ['row' => 3, 'column' => 4],
                20 => ['row' => 3, 'column' => 5],
                21 => ['row' => 3, 'column' => 6],

                22 => ['row' => 4, 'column' => 1],
                23 => ['row' => 4, 'column' => 2],
                24 => ['row' => 4, 'column' => 3],
                25 => ['row' => 4, 'column' => 4],
                26 => ['row' => 4, 'column' => 5],
                27 => ['row' => 4, 'column' => 6],

                28 => ['row' => 5, 'column' => 1],
                29 => ['row' => 5, 'column' => 2],
                30 => ['row' => 5, 'column' => 3],
                31 => ['row' => 5, 'column' => 4],

                32 => ['row' => 6, 'column' => 1],
                33 => ['row' => 6, 'column' => 2],
                34 => ['row' => 6, 'column' => 3],
                35 => ['row' => 6, 'column' => 4]
            ];
            foreach ($list as $v) {
                $id = intval($v['id_position']);

                $num = intval($v['num']);
                $counter_row = $id_row_column_config[$id]['row'];
                $counter_column = $id_row_column_config[$id]['column'];

                $sql = 'update t_cabinet_counter set product_num = product_num - :product_num';
                $sql .= ' where id_cabinet = :id_cabinet and counter_row = :counter_row and counter_column = :counter_column';

                $stmt = $pdo_admin->prepare($sql);
                $stmt->execute([
                    ':id_cabinet' => $id_cabinet,
                    ':counter_row' => $counter_row,
                    ':counter_column' => $counter_column,
                    ':product_num' => $num
                ]);
            }

            /** 记录出库 */
            $sql = 'insert into t_cabinet_stock_out(id_cabinet, id_order, list, create_ts)';
            $sql .= ' values(:id_cabinet, :id_order, :list, :create_ts)';
            $stmt = $pdo_admin->prepare($sql);
            $stmt->execute([
                ':id_cabinet' => $id_cabinet,
                ':id_order' => $id_order,
                ':list' => json_encode($list),
                ':create_ts' => $time_now
            ]);

            $pdo_admin->commit();
        } catch (Exception $e) {
            $pdo_admin->rollBack();
            Logger::info(__METHOD__ . ' db error, so rollback, exception = ' . $e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * 售货柜机器库存 - 可用商品列表 - 后端间接口
     * @param $cd_key
     * @return array|bool
     */
    public function counter_list($cd_key) {
        $ret = [];

        // 根据cd_key获取id_cabinet todo 暂时需求不用
//        $id_cabinet = $this->get_id_cabinet_by_cd_key($cd_key);
        $id_cabinet = 1;

        $sql = 'select id_product, product_num from t_cabinet_counter';
        $sql .= ' where id_cabinet = :id_cabinet and id_product != 0';

        try {
            $stmt = $this->pdo_admin->prepare($sql);
            $stmt->execute([
                ':id_cabinet' => $id_cabinet
            ]);
            $rows = $stmt->fetchAll();
            if ($rows !== false && empty($rows)) {
                Logger::info(__METHOD__ . ' db search return empty, sql = ' . $sql . ', id_cabinet = ' . $id_cabinet
                    . ', cd_key = ' . $cd_key);
                return $ret;
            }

            foreach ($rows as $row) {
                $id_product = intval($row['id_product']);
                $product_num = intval($row['product_num']);

                $sql = 'select sum(product_num) as product_num_locked from t_cabinet_stock_lock where id_cabinet = :id_cabinet';
                $sql .= ' and id_product = :id_product and is_lock = :is_lock';
                $stmt = $this->pdo_admin->prepare($sql);
                $stmt->execute(
                    [
                        ':id_cabinet' => $id_cabinet,
                        ':id_product' => $id_product,
                        ':is_lock' => 1
                    ]
                );
                $row_lock = $stmt->fetch();
                if (!empty($row_lock)) {
                    $product_num_locked = intval($row_lock['product_num_locked']);
                    $product_num -= $product_num_locked; // todo 库存当前显示的是减去被锁的数量, 是否需要把小于0的变为0
                }

                $ret[] = [
                    'id_product' => $id_product,
                    'product_num' => $product_num
                ];
            }
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' db error, exception = ' . $e->getMessage());
            return false;
        }

        return $ret;
    }

    //=========tool===========

    /**
     * 获取售货柜总数量
     * @param $active_status - 0未激活, 1已激活
     * @return bool|int
     */
    public function get_cabinet_total_num($active_status = 1) {
        $ret = 0;

        $sql = 'select count(*) as total_num from t_cabinet_cabinet where active_status = :active_status';

        try {
            $stmt = $this->pdo_admin->prepare($sql);
            $ret = $stmt->execute(
                [
                    ':active_status' => $active_status
                ]
            );
            if($ret === false) {
                Logger::info(__METHOD__ . ' db error, sql = ' . $sql);
                return $ret;
            }

            $row = $stmt->fetch();
            if(!empty($row) || isset($row['total_num'])) {
                $ret = intval($row['total_num']);
            }
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' exception = ' . $e->getMessage());
            return false;
        }

        return $ret;
    }

    /**
     * 获取满足查询条件的订单数
     * @param $date_begin
     * @param $date_end
     * @param $order_status
     * @return bool|int
     */
    public function get_stock_out_total_num($date_begin, $date_end, $order_status) {
        $ret = 0;

        $pdo_param = [];

        $order_source = 4;
        $order_type = 5;
        $pdo_param[':order_source'] = $order_source;
        $pdo_param[':order_type'] = $order_type;

        $sql = 'select count(*) as total_num from s_orders';
        $sql .= ' where order_source = :order_source and order_type = :order_type';

        if (!(empty($date_begin) && empty($date_end) && $order_status === -1)) {
            if (!empty($date_begin)) {
                $pdo_param[':date_begin'] = $date_begin;

                $sql .= ' and date_add >= :date_begin';
            }
            if (!empty($date_end)) {
                $pdo_param[':date_end'] = $date_end;

                $sql .= ' and date_add <= :date_end';
            }
            if ($order_status !== -1) {
                $pdo_param[':order_status'] = $order_status;

                $sql .= ' and current_state = :order_status';
            }
        }

        $sql .= ' order by id_order desc';

        try {
            $stmt = $this->pdo_shop->prepare($sql);
            $stmt->execute($pdo_param);

            $row = $stmt->fetch();
            if (!empty($row)) {
                $ret = intval($row['total_num']);
            }
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' db error, exception = ' . $e->getMessage());
            return false;
        }

        return $ret;
    }

    /**
     * 出库 - 获取总订单数和总业绩
     * @param $order_source
     * @return array
     */
    public function get_stock_out_whole_info($order_source) {
        $ret = [
            'whole_num' => 0,
            'whole_amount' => '0.00'
        ];

        $sql = 'select count(*) as whole_num, sum(total_paid - c_value) as whole_amount from s_orders';
        $sql .= ' where order_source = :order_source';

        try {
            $stmt = $this->pdo_shop->prepare($sql);
            $stmt->execute(
                [
                    ':order_source' => $order_source
                ]
            );
            $row = $stmt->fetch();
            if (!empty($row)) {
                if (!empty($row['whole_num'])) {
                    $ret['whole_num'] = intval($row['whole_num']);
                }
                if (!empty($row['whole_amount'])) {
                    $ret['whole_amount'] = number_format($row['whole_amount'], 2, '.', '');
                }
            }
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' db error, exception = ' . $e->getMessage());
            return $ret;
        }

        return $ret;
    }

    /**
     * todo 同一个pdo不同stmt能否事务
     * todo id_cabinet和id_product, cd_key和id_product组成唯一键
     * 更新库存
     * @param $id_cabinet
     * @param $cd_key
     * @param $id_product
     * @param $num_add
     * @return bool
     */
    public function update_stock($id_cabinet, $cd_key, $id_product, $num_add) {
        $sql = 'select id from t_cabinet_stock where id_cabinet = :id_cabinet and id_product = :id_product';
        try {
            $stmt = $this->pdo_admin->prepare($sql);
            $stmt->execute(
                [
                    ':id_cabinet' => $id_cabinet,
                    ':id_product' => $id_product
                ]
            );

            $row = $stmt->fetch(); // todo 如果为空时返回值是空数组还是false

            $time_now = date('Y-m-d H:i:s');
            if (empty($row)) { // insert
                $sql = 'insert into t_cabinet_stock(id_cabinet, cd_key, id_product, current_num, create_ts, update_ts) values';
                $sql .= '(:id_cabinet, :cd_key, :id_product, :current_num, :create_ts, :update_ts)';
                $stmt = $this->pdo_admin->prepare($sql);
                $stmt->execute(
                    [
                        ':id_cabinet' => $id_cabinet,
                        ':cd_key' => $cd_key,
                        ':id_product' => $id_product,
                        ':current_num' => $num_add,
                        ':create_ts' => $time_now,
                        ':update_ts' => $time_now
                    ]
                );
            } else { // update
                $sql = 'update t_cabinet_stock set current_num = current_num + :num_add,';
                $sql .= ' update_ts = :update_ts where id_cabinet = :id_cabinet and id_product = :id_product';
                $stmt = $this->pdo_admin->prepare($sql);
                $stmt->execute(
                    [
                        ':id_cabinet' => $id_cabinet,
                        ':id_product' => $id_product,
                        ':num_add' => $num_add,
                        ':update_ts' => $time_now
                    ]
                );
            }
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' db error, exception = ' . $e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * 获取入库表中的商品id
     * @param $id
     * @return bool|int
     */
    public function get_one_from_stock_in($id) {
        $sql = 'select id_product, num_in from t_cabinet_stock_in where id = :id';
        try {
            $stmt = $this->pdo_admin->prepare($sql);
            $ret = $stmt->execute(
                [
                    'id' => $id
                ]
            );
            if($ret === false) {
                Logger::info(__METHOD__ . ' db error, sql = ' . $sql . ', id = ' . $id);
                return false;
            }
            $row = $stmt->fetch();
            if(empty($row)) {
                Logger::info(__METHOD__ . ' invalid id, sql = ' . $sql . ', id = ' . $id);
                return false;
            }

            return $row;
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' db error, sql = ' . $sql . ', id = ' . $id);
            return false;
        }
    }

    /**
     * 获取一个订单中的商品数量 todo sum(product_quantity)是否是需求
     * @param $id_order
     * @return int -1表示出错
     */
    public function get_order_product_num($id_order) {
        $sql = 'select sum(product_quantity) as product_num from s_order_detail where id_order = :id_order';
        try {
            $stmt = $this->pdo_shop->prepare($sql);
            $stmt->execute(
                [
                    ':id_order' => $id_order
                ]
            );
            $row = $stmt->fetch();
            if(empty($row) || empty($row['product_num'])) {
                Logger::info(__METHOD__ . ' db error, row is empty, id_order = ' . $id_order);
                return -1;
            }

            return intval($row['product_num']);
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' db error, exception = ' . $e->getMessage());
            return -1;
        }
    }

    /**
     * 获取一个订单中的交易金额 todo sum(product_quantity * product_price)是否是需求
     * @param $id_order
     * @return int
     */
    public function get_order_amount($id_order) {
        $sql = 'select sum(product_quantity * product_price) as amount from s_order_detail where id_order = :id_order';
        try {
            $stmt = $this->pdo_shop->prepare($sql);
            $stmt->execute(
                [
                    ':id_order' => $id_order
                ]
            );
            $row = $stmt->fetch();
            if(empty($row) || empty($row['amount'])) {
                Logger::info(__METHOD__ . ' db error, row is empty, id_order = ' . $id_order);
                return -1;
            }

            return number_format(intval($row['product_num']), 2, '.', '');
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' db error, exception = ' . $e->getMessage());
            return -1;
        }
    }

    /**
     * 根据入库编号获取一条入库记录
     * @param $id
     * @return bool
     */
    public function stock_in_get_one($id) {
        $sql = 'select cd_key, id_product, price_in, num_in, id_supplier, create_ts, update_ts';
        $sql .= ' from t_cabinet_stock_in where id = :id';
        try {
            $stmt = $this->pdo_admin->prepare($sql);
            $stmt->execute(
                [
                    ':id' => $id
                ]
            );
            $row = $stmt->fetch();
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' db error, exception = ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 获取进销存单个商品库存数据
     * @param $id_product
     * @return array|bool
     */
    public function get_single_stock_first($id_product) {
        $sql = 'select distinct ts.id, ts.id_product, ts.total_num_in, (ts.total_num_in - ts.total_num_out) as num,';
        $sql .= ' ts.total_money_in, spl.name, sp.price';
        $sql .= ' from t_stock ts left join s_product_lang spl on (ts.id_product = spl.id_product)';
        $sql .= ' left join s_product sp on (ts.id_product = sp.id_product)';
        $sql .= ' where ts.id_product = :id_product';
        try {
            $stmt = $this->pdo_shop->prepare($sql);
            $stmt->execute(
                [
                    ':id_product' => $id_product
                ]
            );
            $row = $stmt->fetch();
            if ($row !== false && empty($row)) {
                Logger::info(__METHOD__ . ' invalid id_product, id_product = ' . $id_product);
                return [];
            }

            return $row;
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' db error, exception = ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 获取自动售货柜单个库存数据
     * @param $id_cabinet
     * @param $id_product
     * @return array|bool|mixed
     */
    public function get_single_stock_second($id_cabinet, $id_product) {
        $sql = 'select id, id_cabinet, cd_key, id_product, current_num, amount, create_ts, update_ts';
        $sql .= ' from t_cabinet_stock where id_cabinet = :id_cabinet and id_product = :id_product';
        try {
            $stmt = $this->pdo_admin->prepare($sql);
            $stmt->execute(
                [
                    ':id_cabinet' => $id_cabinet,
                    ':id_product' => $id_product
                ]
            );
            $row = $stmt->fetch();
            if ($row !== false && empty($row)) {
                Logger::info(__METHOD__ . ' invalid id_product, id_product = ' . $id_product);
                return [];
            }

            return $row;
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' db error, exception = ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 获取单个自动售货柜
     * @param $id_cabinet
     * @return array|bool|mixed
     */
    public function get_single_cabinet($id_cabinet) {
        $sql = 'select id_hospital, cd_key, cabinet_name, cabinet_address, cabinet_status,';
        $sql .= ' charge_person, id_customer, create_ts, update_ts';
        $sql .= ' from t_cabinet_cabinet where id = :id_cabinet';

        try {
            $stmt = $this->pdo_admin->prepare($sql);
            $stmt->execute(
                [
                    ':id_cabinet' => $id_cabinet
                ]
            );
            $row = $stmt->fetch();
            if ($row !== false && empty($row)) {
                return [];
            } else {
                return $row;
            }
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' db error, exception = ' . $e->getMessage());
            return false;
        }
    }
	
	public function get_single_cabinet_by_cd_key($cd_key) {
		$sql = 'select id_hospital, cd_key, cabinet_name, cabinet_address, cabinet_status,';
		$sql .= ' charge_person, id_customer, create_ts, update_ts';
		$sql .= ' from t_cabinet_cabinet where cd_key = :cd_key';

		try {
			$stmt = $this->pdo_admin->prepare($sql);
			$stmt->execute([
				':cd_key' => $cd_key
			]);
			$row = $stmt->fetch();
			if ($row !== false && empty($row)) {
				return [];
			} else {
				return $row;
			}
		} catch (Exception $e) {
			Logger::info(__METHOD__ . ' db error, exception = ' . $e->getMessage());
			return false;
		}
	}

    /**
     * 出库 - 导出excel
     * @param $data
     * @return bool
     */
    public function export_excel($data) {
        $obj_php_excel = new PHPExcel();
        $sheet = $obj_php_excel->getActiveSheet();

        // Set document properties
        $obj_php_excel->getProperties()->setCreator("Maarten Balliauw")
            ->setLastModifiedBy("Maarten Balliauw")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $obj_php_excel->setActiveSheetIndex(0);

        // Rename worksheet
        $obj_php_excel->getActiveSheet()->setTitle('sheet1');

        //set title
        $title = array(
            '流水号',
            '支付方式',
            '订单来源',

            '交易时间',
            '交易品项数',
            '交易金额',

            '订单状态'
        );

        foreach ($title as $k => $v) {
            $sheet->setCellValueByColumnAndRow($k, 1, $v);
        }

        $format_data = array();

        foreach($data as $row) {
            $tmp_arr = array();

            $tmp_arr[] = $row['reference'];
            $tmp_arr[] = $row['pay_type'];
            $tmp_arr[] = $row['cabinet_name'];

            $tmp_arr[] = $row['date_add'];
            $tmp_arr[] = $row['product_num'];
            $tmp_arr[] = $row['amount'];

            $tmp_arr[] = $row['order_status'];

            $format_data[] = $tmp_arr;
        }

        //set body
        $line = 2;
        foreach ($format_data as $row) {
            foreach ($row as $k => $v) {
                $sheet->setCellValueByColumnAndRow($k, $line, $v);
            }
            ++$line;
        }

        //output file
        $obj_writer = PHPExcel_IOFactory::createWriter($obj_php_excel, 'Excel2007');
        //$obj_writer->save($export_path); save for download, in this way, need to return a url to client

//		header('Content-Type: application/vnd.ms-excel');
//		header('Content-Disposition: attachment;filename=订单列表.xlsx');

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment;filename=kpi.xlsx');
        header('Cache-Control: max-age=0');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        $obj_writer->save('php://output');

        return true;
    }

    /**
     * 获取库存满足条件的总数
     * @param $id_cabinet
     * @param $page_size
     * @param $page_num
     * @return array|bool
     */
    public function stock_get_total_num($id_cabinet) {
        $ret = 0;

        $sql = 'select count(*) as total_num from t_cabinet_stock where id_cabinet = :id_cabinet';

        $param = [
            'id_cabinet' => $id_cabinet
        ];

        try {
            $stmt = $this->pdo_admin->prepare($sql);
            $ret = $stmt->execute(
                [
                    ':id_cabinet' => $id_cabinet //todo
                ]
            );

            $row = $stmt->fetch();
            if(empty($row)) {
                return $ret;
            }

            $ret = intval($row['total_num']);
            return $ret;
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' db error, exception = ' . $e->getMessage());
            return $ret;
        }
    }
}
