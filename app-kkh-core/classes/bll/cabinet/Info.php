<?php
/**
 * Created by PhpStorm.
 * User: hanxiaolong
 * Date: 2018/4/16
 * Time: 17:33
 */

class Bll_Cabinet_Info {
    private $cabinet_dao;

    public function __construct() {
        $this->cabinet_dao = new Dao_Cabinet_Info();
    }

    public function hospital_province() {
        return $this->cabinet_dao->hospital_province();
    }

    public function hospital_area($province_id) {
        return $this->cabinet_dao->hospital_area($province_id);
    }

    public function hospital_list($area_id) {
        return $this->cabinet_dao->hospital_list($area_id);
    }

    public function cabinet_ask_for_active($cd_key) {
        return $this->cabinet_dao->cabinet_ask_for_active($cd_key);
    }

    public function cabinet_active($id_hospital, $id_province, $id_city, $id_cabinet, $cabinet_name, $cabinet_address, $cabinet_status, $charge_person) {
        return $this->cabinet_dao->cabinet_active($id_hospital, $id_province, $id_city, $id_cabinet, $cabinet_name, $cabinet_address, $cabinet_status, $charge_person);
    }

    public function cabinet_get($active_status, $page_size, $page_num) {
        return $this->cabinet_dao->cabinet_get($active_status, $page_size, $page_num);
    }

    public function cabinet_edit($id_hospital, $id_province, $id_city, $id_cabinet, $cabinet_name, $address, $cabinet_status, $charge_person) {
        return $this->cabinet_dao->cabinet_edit($id_hospital, $id_province, $id_city, $id_cabinet, $cabinet_name, $address, $cabinet_status, $charge_person);
    }

    public function stock_get($id_cabinet, $page_size, $page_num) {
        return $this->cabinet_dao->stock_get($id_cabinet, $page_size, $page_num);
    }

    public function stock_in_add($id_cabinet, $id_product, $num_in) {
        return $this->cabinet_dao->stock_in_add($id_cabinet, $id_product, $num_in);
    }

    public function stock_in_product_info($id_product) {
        return $this->cabinet_dao->stock_in_product_info($id_product);
    }

    public function stock_in_product_list() {
        return $this->cabinet_dao->stock_in_product_list();
    }

    public function stock_edit($id_stock, $product_num) {
        return $this->cabinet_dao->stock_edit($id_stock, $product_num);
    }

    public function stock_out_get($date_begin, $date_end, $order_status, $page_num, $page_size) {
        return $this->cabinet_dao->stock_out_get($date_begin, $date_end, $order_status, $page_num, $page_size);
    }

    public function stock_out_get_detail($id_order) {
        return $this->cabinet_dao->stock_out_get_detail($id_order);
    }

    public function stock_out_export($order_status) {
        return $this->cabinet_dao->stock_out_export($order_status);
    }

    public function counter_get($id_cabinet) {
        return $this->cabinet_dao->counter_get($id_cabinet);
    }

    public function counter_product_list($id_cabinet, $key_word) {
        return $this->cabinet_dao->counter_product_list($id_cabinet, $key_word);
    }

    public function counter_add($id_cabinet, $counter_row, $counter_column, $add_num) {
        return $this->cabinet_dao->counter_add($id_cabinet, $counter_row, $counter_column, $add_num);
    }

    public function counter_assign($id_cabinet, $counter_row, $counter_column, $id_product, $assign_num) {
        return $this->cabinet_dao->counter_assign($id_cabinet, $counter_row, $counter_column, $id_product, $assign_num);
    }

    public function counter_clear($id_cabinet, $counter_row, $counter_column) {
        return $this->cabinet_dao->counter_clear($id_cabinet, $counter_row, $counter_column);
    }

    /* 提供给后端的接口 - begin */
    public function counter_get_one($cd_key, $id_product) {
        return $this->cabinet_dao->counter_get_one($cd_key, $id_product);
    }

    public function stock_lock($cd_key, $id_order, $order_type) {
        return $this->cabinet_dao->stock_lock($cd_key, $id_order, $order_type);
    }

    public function stock_unlock($cd_key, $id_order, $order_type) {
        return $this->cabinet_dao->stock_unlock($cd_key, $id_order, $order_type);
    }

    public function stock_out_compute($cd_key, $id_order, $order_type, $extra) {
        return $this->cabinet_dao->stock_out_compute($cd_key, $id_order, $order_type, $extra);
    }

    public function stock_out_success($cd_key, $id_order) {
        return $this->cabinet_dao->stock_out_success($cd_key, $id_order);
    }

    public function counter_list($cd_key) {
        return $this->cabinet_dao->counter_list($cd_key);
    }
    /* 提供给后端的接口 - end */
}
