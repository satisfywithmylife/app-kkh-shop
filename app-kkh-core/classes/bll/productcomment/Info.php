<?php
/**
 * Created by PhpStorm.
 * User: hanxiaolong
 * Date: 2018/3/20
 * Time: 19:29
 */
class Bll_ProductComment_Info {
    private $productCommentInfoDao;

    public function __construct() {
        $this->productCommentInfoDao = new Dao_ProductComment_Info();
    }

    public function commit($data) {
        return $this->productCommentInfoDao->commit($data);
    }

    public function getDetail($id_product) {
        return $this->productCommentInfoDao->getDetail($id_product);
    }

    public function get($id_product, $type, $page_num, $page_size) {
        return $this->productCommentInfoDao->get($id_product, $type, $page_num, $page_size);
    }
}
