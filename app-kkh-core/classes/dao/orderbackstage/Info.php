<?php
/**
 * Created by PhpStorm.
 * User: hanxiaolong
 * Date: 2018/4/4
 * Time: 17:11
 */
apf_require_class('APF_DB_Factory');

class Dao_OrderBackStage_Info {
    private $pdo_shop;
    private $pdo_admin;

    public static $INVALID_ORDER_STATE = array(
        1 => '待付款',
        2 => '已付款',
        4 => '已发货',
        6 => '已取消',
        7 => '已退款',
        13 => '已完成'
    );

    public function __construct() {
        $this->pdo_shop = APF_DB_FACTORY::get_instance()->get_pdo('shop_master');
        $this->pdo_admin = APF_DB_Factory::get_instance()->get_pdo('admin_master');
    }

    /**
     * 后台订单 - 获取订单列表
     * @param $begin_date - s_orders: date_add
     * @param $end_date
     * @param $first_order - 是否是首次下单: 1是, 0否, 为-1时表示无此限制
     * @param $order_status - 订单状态: 1待付款, 2已付款, 4已发货, 13已签收(已完成), 为-1时表示无此限制 - s_orders: current_state
	 * @param $order_source 
	 * @param $order_type
     * @param $page_num - 第几页
     * @param $page_size - 一页显示多少评论
     * @param $export - 是否导出: 1是, 0否
     * @return array
     */
    public function get($begin_date, $end_date, $first_order, $order_status, $order_source, $order_type, $page_num, $page_size) {
        $ret = array();
		
		$tmp_arr = array();

		$sql = 'select so.id_order, so.reference, so.date_add as create_ts, so.current_state as order_status,';
		$sql .= ' so.total_paid - so.c_value as total_paid, so.gift_message as buyer_note,';
		$sql .= ' so.ref_doctor_id, so.order_source, so.order_type, so.u_kkid,';
        $sql .= ' sa.firstname as buyer_name, sa.phone_mobile as phone_number, concat_ws(" ", sa.address1, sa.address2) as address'; //notice: cannot use newline in one sql
        $sql .= ' from s_orders so';
        $sql .= ' left join s_address sa on so.id_address_delivery = sa.id_address';

        if ($order_status === -1) {
            $sql .= ' where so.current_state in (' . implode(',', array_keys(self::$INVALID_ORDER_STATE)) . ')'; //1 - 待付款, 2 - 已付款, 4 - 已发货, 6 - 已取消, 7 - 已退款, 13 - 已签收(已完成)
        } else {
            $sql .= ' where so.current_state = :order_status';
			$tmp_arr[':order_status'] = $order_status;
        }

        if (!empty($begin_date)) {
            $sql .= ' and so.date_add >= :begin_date';
            $tmp_arr[':begin_date'] = $begin_date;
        }
        if (!empty($end_date)) {
            $sql .= ' and so.date_add < :end_date';
            $tmp_arr[':end_date'] = $end_date;
        }

		if (!empty($order_source)) {
			$sql .= ' and order_source = :order_source';
			$tmp_arr[':order_source'] = $order_source;
		}

		if (!empty($order_type)) {
			$sql .= ' and order_type = :order_type';
			$tmp_arr[':order_type'] = $order_type;
		}
		//$sql .= ' order by date_add desc limit :id_begin, :page_size'; // notice: limit 1, 2 : not include 1
		$sql .= ' order by so.id_order desc';

		//Logger::info(__METHOD__ . ' sql = ' . $sql);

        $stmt = $this->pdo_shop->prepare($sql);	
		$stmt->execute($tmp_arr);
        $rows = $stmt->fetchAll();

		//Logger::info('ok5, rows = ' . json_encode($rows));

        if (empty($rows)) {
            return $ret;
        }

        $total_num = $this->get_total_num($begin_date, $end_date, $first_order, $order_status, $order_source, $order_type, $page_num, $page_size);

        $bll_order_config = new Bll_Order_Config();
        $type_list = $bll_order_config->get_order_config();
        $order_source_arr = [];
        $order_type_arr = [];
        foreach ($type_list['order_source'] as $v) {
            $order_source_arr[$v['id']] = $v['name'];
        }
        foreach ($type_list['order_type'] as $v) {
            $order_type_arr[$v['id']] = $v['name'];
        }


        //todo 一个查询能否实现
        foreach ($rows as $k => &$row) {
            //是否首次下单
            $row['first_order'] = $this->check_first_order($row['u_kkid'], intval($row['id_order']));

            //首次下单 - 根据条件筛选
            if(($first_order !== -1) && ($row['first_order'] !== $first_order)) {
                unset($rows[$k]);
                continue;
            }

            $id_order = intval($row['id_order']);

            //商品信息 - 商品名字, 数量, 价格
            $sql = 'select product_name, product_quantity as product_num, product_price from s_order_detail where id_order = :id_order';
            $stmt = $this->pdo_shop->prepare($sql);
            $stmt->execute(array(
                ':id_order' => $id_order
            ));
            $product_list = $stmt->fetchAll();
            if (empty($product_list)) {
                $row['product_list'] = array();
                //Logger::info(__METHOD__ . ' product_list empty, id_order = ' . $id_order . ', sql = ' . $sql);
            } else {
				foreach($product_list as &$row_pl) {
					$row_pl['product_num'] = intval($row_pl['product_num']);
					$row_pl['product_price'] = number_format($row_pl['product_price'], 2, '.', '');
				}
				unset($row_pl);
                $row['product_list'] = $product_list;
            }

            //操作人备注
			$note = '';
            $sql = 'select note from t_order_note where id_order = :id_order';
            $stmt = $this->pdo_admin->prepare($sql);
            $stmt->execute(array(
                ':id_order' => $id_order
            ));
			$row_note = $stmt->fetch();
            if (!empty($row_note) && !empty($row_note['note'])) {
				$note = $row_note['note'];
            }
			$row['note'] = $note;
			
			$doctor_name = '';
			if(!empty($row['ref_doctor_id'])) {
				$doctor_id = intval($row['ref_doctor_id']);
				$doctor_name = $this->get_doctor_name($doctor_id); 
			}
			$row['doctor_name'] = $doctor_name;
			unset($row['ref_doctor_id']);

			//format output
			$row['id_order'] = strval($row['id_order']);
			$row['order_status'] = intval($row['order_status']);
			$row['total_paid'] = number_format($row['total_paid'], 2, '.', '');

            $order_source = intval($row['order_source']);
			if (isset($order_source_arr[$order_source])) {
                $row['order_source'] = $order_source_arr[$order_source];
            }
            $order_type = intval($row['order_type']);
            if (isset($order_type_arr[$order_type])) {
                $row['order_type'] = $order_type_arr[$order_type];
            }
        }
        unset($row); //销毁引用

        if($page_num !== -1) { //export, inner call
            $id_begin = ($page_num - 1) * $page_size;
            $rows = array_slice($rows, $id_begin, $page_size);
        }

        $ret['list'] = $rows;
		$ret['total_num'] = $total_num;

		//test
		//Logger::info('ret = ' . json_encode($ret) . ', type = ' . gettype($ret) . ', num = ' . count($ret));

        return $ret;
    }

    /**
     * 后台订单 - 修改订单状态
     * @param $id_order
     * @param $operator
     * @param $order_status
     * @return bool
     */
    public function modify_order_status($id_order, $operator, $order_status) {
        if($this->check_order($id_order, $order_status) === false) {
            return false;
        }

        try {
            //更新订单状态
            $time_now = date('Y-m-d H:i:s');
            $this->pdo_shop->beginTransaction();
            $sql = 'update s_orders set current_state = :order_status, date_upd = :date_upd where id_order = :id_order';
            $stmt = $this->pdo_shop->prepare($sql);
            $stmt->execute(array(
                ':order_status' => $order_status,
                ':date_upd' => $time_now,
                ':id_order' => $id_order
            ));
            $this->pdo_shop->commit();

            //更新操作记录
            $this->pdo_admin->beginTransaction();
            $sql = 'insert into t_order_operation(id_order, operator, order_status, modify_ts) values(:id_order, :operator, :order_status, :modify_ts)';
            $stmt = $this->pdo_admin->prepare($sql);
            $stmt->execute(array(
                ':id_order' => $id_order,
                ':operator' => $operator,
                ':order_status' => $order_status,
                ':modify_ts' => date('Y-m-d H:i:s')
            ));
            $this->pdo_admin->commit();

            //如果订单状态修改为已完成
            if ($order_status === 13) {
                //将医生奖励加入医生个人账户
                $u_kkid = $this->get_ukkid($id_order);
                $doctor_id = $this->get_doctor_id($id_order);
				$info = $this->get_info_for_doctor_award($id_order);
				if ($info === false) {
				    Logger::info(__METHOD__ . ' get_info_for_doctor_award fail, id_order = ' . $id_order);
                    $this->pdo_shop->rollBack();
                    $this->pdo_admin->rollBack();
                    Logger::info(__METHOD__  . ' rollback success!!');
                    return false;
                }
				$id_product_kkh = intval($info['id_product_kkh']);
                $quantity = intval($info['product_quantity']);

				//test
				//$id_product_kkh = 100;
				//$doctor_id = 10;

                if ($u_kkid !== false && $id_product_kkh !== false) {
					$award_ret = false;
					$after_award_ret = false;

				    if ($doctor_id !== false) {
                        $award_ret = $this->doctor_award($id_product_kkh, $quantity, $doctor_id);
						if($award_ret === true) {
							$after_award_ret = $this->after_doctor_award($u_kkid, $doctor_id, $id_product_kkh);
						}
                    } else {
                        $doctor_id = $this->get_rebuy_doctor_id($u_kkid, $id_product_kkh); // 获取满足复购奖励条件的doctor_id
                        if ($doctor_id !== false) {
                            $award_ret = $this->doctor_award($id_product_kkh, $quantity, $doctor_id);
							if($award_ret === true) {
								$after_award_ret = $this->after_doctor_award($u_kkid, $doctor_id, $id_product_kkh);
							}
                        }
                    }

                    if ($award_ret === false) {
                        Logger::info(__METHOD__ . ' send doctor awrad fail, id_order = ' . $id_order
                            . ', doctor_id = ' . $doctor_id . ', id_product_kkh = ' . $id_product_kkh);
                        $this->pdo_shop->rollBack();
                        $this->pdo_admin->rollBack();
                        Logger::info(__METHOD__  . ' rollback success!!');
                        return false;
                    }
                    if ($after_award_ret === false) {
                        Logger::info(__METHOD__ . ' after_doctor_award fail, id_order = ' . $id_order . ', u_kkid = '
                            . $u_kkid . ', doctor_id = ' . $doctor_id . ', id_product_kkh = ' . $id_product_kkh);
                    }
                    if ($award_ret !== $after_award_ret) {
                        Logger::info(__METHOD__ . ' award_ret and after_award_ret not same, one of them is false');
                    }
                } else {
                    Logger::info(__METHOD__ . ' db error, u_kkid or id_product_kkh is false id_order = ' . $id_order
                        . ', u_kkid = ' . json_encode($u_kkid) . ', id_product_kkh = ' . json_encode($id_product_kkh));
                    $this->pdo_shop->rollBack();
                    $this->pdo_admin->rollBack();
                    Logger::info(__METHOD__  . ' rollback success!!');
                    return false;
                }

                //邮件推送至order组中 todo 标题内容是什么
//				$this->send_mail();
            }
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' db error, err_msg = ' . $e->getMessage());
            $this->pdo_shop->rollBack();
            $this->pdo_admin->rollBack();
            Logger::info(__METHOD__  . ' rollback success!!');

            return false;
        }

        return true;
    }

    /**
     * 后台订单 - 获取操作记录列表
     * @param $id_order
     * @return array
     */
    public function operation_log($id_order) {
        $ret = array();

        if($this->check_order($id_order) === false) {
            return $ret;
        }

        $sql = 'select operator, order_status, modify_ts from t_order_operation where id_order = :id_order
                order by modify_ts desc';
        $stmt = $this->pdo_admin->prepare($sql);
        $exec_ret = $stmt->execute(array(
            ':id_order' => $id_order
        ));
        if($exec_ret === false) {
            return $ret;
        }

        $rows = $stmt->fetchAll();
        if(empty($rows)) {
            return $ret;
        }

		foreach($rows as &$row) {
			$row['order_status'] = intval($row['order_status']);
		}
		unset($row);

		$ret['list'] = $rows;

        return $ret;
    }

    /**
     * 后台订单 - 导出
     * @param $begin_date
     * @param $end_date
     * @param $first_order
     * @param $order_status
	 * @param $order_source
	 * @param $order_type
     * @param $page_num
     * @param $page_size
     * @return array|bool
     */
    public function export($begin_date, $end_date, $first_order, $order_status, $order_source, $order_type, $page_num, $page_size) {
        $data = $this->get($begin_date, $end_date, $first_order, $order_status, $order_source, $order_type, $page_num, $page_size);
        if(empty($data) || empty($data['list'])) {
            $rows = [];
            //return false;
        } else {
            $rows = $data['list'];
        }

		$this->export_excel($rows);

        return true;
    }

    /**
     * 后台订单 - 修改订单备注
     * @param $id_order
     * @param $operator
     * @param $note
     * @return bool
     */
	public function modify_note($id_order, $operator, $note) {
        if($this->check_order($id_order) === false) {
            return false;
        }

        $modify_ts = date('Y-m-d H:i:s');

        $sql = 'select note from t_order_note where id_order = :id_order';
        $stmt = $this->pdo_admin->prepare($sql);
        $stmt->execute(array(
            ':id_order' => $id_order
        ));
        $row = $stmt->fetch();
        if(empty($row) || !isset($row['note'])) {
            $sql = 'insert into t_order_note(id_order, operator, note, modify_ts) values (:id_order, :operator, :note, :modify_ts)';
            $stmt = $this->pdo_admin->prepare($sql);
            $exec_ret = $stmt->execute(array(
				':id_order' => $id_order,
                ':operator' => $operator,
                ':note' => $note,
                ':modify_ts' => $modify_ts
            ));
        } else {
            $sql = 'update t_order_note set operator = :operator, note = :note, modify_ts = :modify_ts where id_order = :id_order';
            $stmt = $this->pdo_admin->prepare($sql);
            $exec_ret = $stmt->execute(array(
                ':operator' => $operator,
                ':note' => $note,
                ':modify_ts' => $modify_ts,
                'id_order' => $id_order
            ));
        }

        if($exec_ret === false) {
            Logger::info(__METHOD__ . ' db error, id_order = ' . $id_order . ', note = ' . $note
                . ', modify_ts = ' . $modify_ts . ', sql = ' . $sql);
            return false;
        }

        return true;
    }

	public function source_and_type_list() {
		$ret = array(
			'source_list' => array(),
			'type_list' => array()
		);
		
		$sql = 'select distinct order_source from s_orders where order_source != :order_source';
		$stmt = $this->pdo_shop->prepare($sql);
		$stmt->execute(array(
			':order_source' => ''
		));
		$rows = $stmt->fetchAll();
		if(!empty($rows)) {
			foreach($rows as $row) {
				if(!empty($row['order_source'])) {
					$ret['source_list'][] = $row['order_source'];
				}
			}
		}
		
		$sql = 'select distinct order_type from s_orders where order_type != :order_type';
        $stmt = $this->pdo_shop->prepare($sql);
        $stmt->execute(array(
            ':order_type' => ''
        )); 
        $rows = $stmt->fetchAll();
        if(!empty($rows)) {
            foreach($rows as $row) {
                if(!empty($row['order_type'])) {
                    $ret['type_list'][] = $row['order_type'];
                }   
            }   
        }

		return $ret;
	}

    //================tool

    /**
     * 获取订单中的u_kkid
     * @param $id_order
     * @return bool
     */
    public function get_ukkid($id_order) {
        $sql = 'select u_kkid from s_orders where id_order = :id_order';
        try {
            $stmt = $this->pdo_shop->prepare($sql);
            $stmt->execute(
                array(
                    ':id_order' => $id_order
                )
            );
            $row = $stmt->fetch();
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' db error, exception = ' . $e->getMessage());
        }

        if(empty($row) || !isset($row['u_kkid'])) {
            return false;
        }

        return $row['u_kkid'];
    }

    /**
     * 获取满足复购奖励条件的doctor_id
     * @param $u_kkid
     * @param $id_product_kkh
     * @return bool|int
     */
    public function get_rebuy_doctor_id($u_kkid, $id_product_kkh) {
        $sql = 'select doctor_id from t_order_doctor_award where kkid = :u_kkid and id_product_kkh = :id_product_kkh and award_times < :award_times order by id desc limit 1';
        try {
            $stmt = $this->pdo_admin->prepare($sql);
            $stmt->execute(
                array(
                    ':u_kkid' => $u_kkid,
                    ':id_product_kkh' => $id_product_kkh,
					':award_times' => 3
                )
            );
            $row = $stmt->fetch();
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' db error, exception = ' . $e->getMessage());
			return false;
        }

        if(empty($row) || !isset($row['doctor_id'])) {
            return false;
        }

        return intval($row['doctor_id']);
    }

    /**
     * 获取总数量
     * @param $begin_date
     * @param $end_date
     * @param $first_order
     * @param $order_status
     * @param $order_source
     * @param $order_type
     * @param $page_num
     * @param $page_size
     * @return int
     */
    public function get_total_num($begin_date, $end_date, $first_order, $order_status, $order_source, $order_type, $page_num, $page_size) {
        $ret = 0;

        $tmp_arr = array();

        $sql = 'select so.id_order, so.reference, so.date_add as create_ts, so.current_state as order_status,';
        $sql .= ' so.total_paid - so.c_value as total_paid, so.gift_message as buyer_note,';
        $sql .= ' so.ref_doctor_id, so.order_source, so.order_type, so.u_kkid,';
        $sql .= ' sa.firstname as buyer_name, sa.phone_mobile as phone_number, concat_ws(" ", sa.address1, sa.address2) as address'; //notice: cannot use newline in one sql
        $sql .= ' from s_orders so';
        $sql .= ' left join s_address sa on so.id_address_delivery = sa.id_address';

        if ($order_status === -1) {
            $sql .= ' where so.current_state in (' . implode(',', array_keys(self::$INVALID_ORDER_STATE)) . ')'; //1 - 待付款, 2 - 已付款, 4 - 已发货, 6 - 已取消, 7 - 已退款, 13 - 已签收(已完成)
        } else {
            $sql .= ' where so.current_state = :order_status';
            $tmp_arr[':order_status'] = $order_status;
        }

        if (!empty($begin_date)) {
            $sql .= ' and so.date_add >= :begin_date';
            $tmp_arr[':begin_date'] = $begin_date;
        }
        if (!empty($end_date)) {
            $sql .= ' and so.date_add < :end_date';
            $tmp_arr[':end_date'] = $end_date;
        }

        if (!empty($order_source)) {
            $sql .= ' and order_source = :order_source';
            $tmp_arr[':order_source'] = $order_source;
        }

        if (!empty($order_type)) {
            $sql .= ' and order_type = :order_type';
            $tmp_arr[':order_type'] = $order_type;
        }
        $sql .= ' order by so.id_order desc';

        //Logger::info(__METHOD__ . ' sql = ' . $sql);

        $stmt = $this->pdo_shop->prepare($sql);
        $stmt->execute($tmp_arr);
        $rows = $stmt->fetchAll();

        if (empty($rows)) {
            return $ret;
        }

        foreach ($rows as $k => &$row) {
            //是否首次下单
            $row['first_order'] = $this->check_first_order($row['u_kkid'], intval($row['id_order']));

            //首次下单 - 根据条件筛选
            if(($first_order !== -1) && ($row['first_order'] !== $first_order)) {
                unset($rows[$k]);
                continue;
            }
        }
        unset($row);

        $ret = count($rows);

        return $ret;
    }

    /**
     * 订单合法性检查 - 1.订单是否存在, 2.订单状态检查
     * @param $id_order
     * @return bool
     */
    public function check_order($id_order, $order_status = -1) {
        $sql = 'select current_state from s_orders where id_order = :id_order';
        $stmt = $this->pdo_shop->prepare($sql);
        $stmt->execute(array(
            ':id_order' => $id_order
        ));
		
		$row = $stmt->fetch();
        if(empty($row) || !isset($row['current_state'])) {
            Logger::info(__METHOD__ . ' invalid result, id_order = ' . $id_order . ', sql = ' . $sql);
            return false;
        }

        $current_state = intval($row['current_state']);
        if (!array_key_exists($current_state, self::$INVALID_ORDER_STATE)) {
            Logger::info(__METHOD__ . ' invalid current_state, id_order = ' . $id_order . ', current_state = ' . $current_state);
            return false;
        }

		if($order_status !== -1) {
			if($order_status === $current_state) {
				Logger::info(__METHOD__ . ' new order_status equals current_order_state, cannot modify order_status');
				return false;
			}
		}

        return true;
    }

    /**
     * 判断用户是否是首次下单; 没有处于这两种状态的订单表示首次下单: 2 - 已付款, 7 - 已退款, 13 - 已签收(已完成)
     * @param $u_kkid todo 用u_kkid还是id_customer判断
     * @param $id_order
     * @return int - 0:否, 1是
     */
    public function check_first_order($u_kkid, $id_order) {
        $sql = 'select id_order, date_upd from s_orders where u_kkid = :u_kkid and current_state in (2, 7, 13)';
        $stmt = $this->pdo_shop->prepare($sql);
        $stmt->execute(array(
            ':u_kkid' => $u_kkid
        ));
        $rows = $stmt->fetchAll();
        if(empty($rows)) {
            return 0;
        }

        $tmpArr = array();
        foreach ($rows as $row) {
            $tmpArr[intval($row['id_order'])] = strtotime($row['date_upd']);
        }
        if ($tmpArr[$id_order] !== min($tmpArr)) {
            return 0;
        }

        //Logger::info(__METHOD__ . ' first order, u_kkid = ' . $u_kkid . ', id_order = ' . $id_order);
        return 1;
    }


    /**
     * 导出excel
     * @param $data
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
            '订单编号',
            '创建时间',
            '客户姓名',

            '手机号',
            '地址',
            '订单备注信息',

            '商品信息', 
            '总金额',
            '推荐医生',

            '首次下单',
            '状态',
            '来源',

            '类型',
            '备注',
            '操作记录' 
        );

        foreach ($title as $k => $v) {
            $sheet->setCellValueByColumnAndRow($k, 1, $v);
        }

		$format_data = array();

		foreach($data as $row) {
			$tmp_arr = array();
			
			$tmp_arr[] = $row['reference'];
			$tmp_arr[] = $row['create_ts'];
			$tmp_arr[] = $row['buyer_name'];

			$tmp_arr[] = $row['phone_number'];
			$tmp_arr[] = $row['address'];
			$tmp_arr[] = $row['buyer_note'];

			$product_info = '';
			foreach($row['product_list'] as $v) {
				$product_info .= $v['product_price'] . '&' . $v['product_num'] . '&' . $v['product_name'] . "\n";
			}
			$tmp_arr[] = $product_info;
			$tmp_arr[] = $row['total_paid'];
			$tmp_arr[] = $row['doctor_name'];

			if (intval($row['first_order']) === 1) {
			    $row['first_order'] = '是';
            } else {
			    $row['first_order'] = '否';
            }
			$tmp_arr[] = $row['first_order'];
			$tmp_arr[] = isset(self::$INVALID_ORDER_STATE[$row['order_status']]) ? self::$INVALID_ORDER_STATE[$row['order_status']] : $row['order_status'];
			$tmp_arr[] = $row['order_source'];

			$tmp_arr[] = $row['order_type'];
			$tmp_arr[] = $row['note'];
			$operation_log = '';
			$operation_ret = $this->operation_log($row['id_order']);
			if(!empty($operation_ret['list'])) {
				$operation_list = $operation_ret['list'];
				foreach($operation_list as $v) {
                    $v['order_status'] = isset(self::$INVALID_ORDER_STATE[$v['order_status']]) ? self::$INVALID_ORDER_STATE[$v['order_status']] : $v['order_status'];
					$operation_log .= $v['modify_ts'] . ' ' . $v['operator'] . ' ' . $v['order_status'] . "\n";
				}
			}
			$tmp_arr[] = $operation_log;
			
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
	
	public function doctor_award($id_product_kkh, $quantity, $doctor_id) {
		$url = ORDERBACKSTAGE_DOCTOR_URL . '/doctor/recomment/reward_amount/';
		$data = array(
			'product_id' => $id_product_kkh,
			'quantity' => $quantity,
			'doctor_id' => $doctor_id
		);

		$res = Util_Curl::http_post($url, $data, 1);
		Logger::info('param = ' . json_encode($data) . ', res = ' . json_encode($res));
		if (isset($res['status']) && intval($res['status']) === 200){
		    Logger::info(__METHOD__ . ' send doctor award success!!');
			return true;
		} else {
		    Logger::info(__METHOD__ . ' send doctor_award fail, res = ' . json_encode($res) . ', param = ' . json_encode($data));
			return false;
		}
	}

    /**
     * 发送医生奖励后, 更新医生奖励表
     * @param $u_kkid
     * @param $doctor_id
     * @param $id_product_kkh
     * @return bool
     */
	public function after_doctor_award($u_kkid, $doctor_id, $id_product_kkh) {
        $sql = 'select id from t_order_doctor_award where kkid = :u_kkid and doctor_id = :doctor_id';
        $sql .= ' and id_product_kkh = :id_product_kkh';

        $time_now = date('Y-m-d H:i:s');
        try {
            $stmt = $this->pdo_admin->prepare($sql);
            $stmt->execute(
                array(
                    ':u_kkid' => $u_kkid,
                    ':doctor_id' => $doctor_id,
                    ':id_product_kkh' => $id_product_kkh
                )
            );
            $row = $stmt->fetch();
            if(empty($row)) { // insert
                $sql = 'insert into t_order_doctor_award(kkid, doctor_id, id_product_kkh, award_times, create_ts, update_ts)';
                $sql .= ' values(:kkid, :doctor_id, :id_product_kkh, :award_times, :create_ts, :update_ts)';

                $stmt = $this->pdo_admin->prepare($sql);
                $stmt->execute(
                    array(
                        ':kkid' => $u_kkid,
                        ':doctor_id' => $doctor_id,
                        ':id_product_kkh' => $id_product_kkh,
                        ':award_times' => 1,
                        ':create_ts' => $time_now,
                        ':update_ts' => $time_now
                    )
                );
            } else { // update
                $sql = 'update t_order_doctor_award set award_times = award_times + 1, update_ts = :update_ts';
                $sql .= ' where kkid = :u_kkid and doctor_id = :doctor_id and id_product_kkh = :id_product_kkh';

                $stmt = $this->pdo_admin->prepare($sql);
                $stmt->execute(
                    array(
                        ':u_kkid' => $u_kkid,
                        ':doctor_id' => $doctor_id,
                        ':id_product_kkh' => $id_product_kkh,
                        ':update_ts' => $time_now
                    )
                );
            }
        } catch (Exception $e) {
            Logger::info(__METHOD__ . ' db error, u_kkid = ' . $u_kkid . ', doctor_id = ' . $doctor_id
                . ', id_product_kkh = ' . $id_product_kkh);
            return false;
        }

        return true;
    }

	public function get_info_for_doctor_award($id_order) {
		$ret = false;

		$sql = 'select product_id, product_quantity from s_order_detail where id_order = :id_order limit 1';
        $stmt = $this->pdo_shop->prepare($sql);
        $stmt->execute(array(
  	      ':id_order' => $id_order
        ));
        $row = $stmt->fetch();
		if(empty($row)) {
			Logger::info(__METHOD__ . ' db select return empty, id_order = ' . $id_order . ', sql = ' . $sql);
			return false;
		}
		$id_product = intval($row['product_id']);
        $product_quantity = intval($row['product_quantity']);

        $sql = 'select id_product_kkh from s_product where id_product = :id_product';
        $stmt = $this->pdo_shop->prepare($sql);
        $stmt->execute(array(
            ':id_product' => $id_product
        ));
        $row = $stmt->fetch();
        if(empty($row)) {
            Logger::info(__METHOD__ . ' db select return empty, id_order = ' . $id_order . ', sql = ' . $sql);
            return false;
        }
        $id_product_kkh = intval($row['id_product_kkh']);

		$ret = array();
		$ret['id_product_kkh'] = $id_product_kkh;
		$ret['product_quantity'] = $product_quantity;
		return $ret;
	}
	
	public function get_doctor_id($id_order) {
		$ret = false;

		$sql = 'select ref_doctor_id from s_orders where id_order = :id_order';
		$stmt = $this->pdo_shop->prepare($sql);
		$stmt->execute(array(
			':id_order' => $id_order
		));
		$res = $stmt->fetch();
		if(empty($res) || !isset($res['ref_doctor_id']) || $res['ref_doctor_id'] <= 0) {
			Logger::info(__METHOD__ . ' db select return empty, id_order = ' . $id_order . ', sql = ' . $sql);
			return false;
		}

		return intval($res['ref_doctor_id']);
	}

	public function get_doctor_name($doctor_id) {
		$ret = '';
		$url = ORDERBACKSTAGE_DOCTOR_URL . '/doctor/info/';
		$data = array(
			'doctor_id' => $doctor_id
		);
		$res = Util_Curl::http_post($url, $data, 1);
		//Logger::info(__METHOD__ . ' res = ' . json_encode($res));
		if(isset($res['status']) && intval($res['status']) === 200) {
			if(isset($res['data']['info']['real_name'])) {
				$ret = $res['data']['info']['real_name'];
			} else {
				Logger::info(__METHOD__ . ' curl success, but return invalid, res = ' . json_encode($res));
			}
		} else {
			Logger::info(__METHOD__ . ' curl fail, doctor_id = ' . $doctor_id);
		}

		//Logger::info('ret = ' . $ret . ', res = ' . json_encode($res) . ', url = ' . $url);
		return $ret;
	}

	public function send_mail() {
		//$to = CS_MAIL_GROUP; //todo
		$to = '18301805881@163.com';
        $subject = '订单完成';
		$mailbody = <<<MAILBODY
后台修改订单状态为已完成 <br />
MAILBODY;
                $ret = Util_SmtpMail::send_encrypt(
                  $to,
                  $subject,
                  $mailbody
                );
		if($ret === false) {
			Logger::info(__METHOD__ . '  send mail fail, to = ' . $to . ', subject = ' . $subject . ', mailbody = ' . $mailbody);
		}
	}
}

