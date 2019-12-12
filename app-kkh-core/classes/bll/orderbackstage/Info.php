<?php
/**
 * Created by PhpStorm.
 * User: hanxiaolong
 * Date: 2018/4/4
 * Time: 17:05
 */

class Bll_OrderBackStage_Info {
    private $order_back_stage_dao;

    public function __construct() {
        $this->order_back_stage_dao = new Dao_OrderBackStage_Info();
    }

    public function get($begin_date, $end_date, $first_order, $order_status, $order_source, $order_type, $page_num, $page_size) {
        return $this->order_back_stage_dao->get($begin_date, $end_date, $first_order, $order_status, $order_source, $order_type, $page_num, $page_size);
    }

    public function modify_order_status($id_order, $operator, $order_status) {
        return $this->order_back_stage_dao->modify_order_status($id_order, $operator, $order_status);
    }

    public function operation_log($id_order) {
        return $this->order_back_stage_dao->operation_log($id_order);
    }

    public function modify_note($id_order, $operator, $note) {
        return $this->order_back_stage_dao->modify_note($id_order, $operator, $note);
    }

    public function export($begin_date, $end_date, $first_order, $order_status, $order_source, $order_type, $page_num, $page_size) {
        return $this->order_back_stage_dao->export($begin_date, $end_date, $first_order, $order_status, $order_source, $order_type, $page_num, $page_size);
    }

	public function source_and_type_list() {
		return $this->order_back_stage_dao->source_and_type_list();
	}
}
