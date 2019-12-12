<?php
class Bll_Comment_Info {
	private $commentInfoDao;

	public function __construct() {
		$this->commentInfoDao = new Dao_Comment_Info();
	}
	
	public function productList() {
		return $this->commentInfoDao->productList();
	}

	public function sourceList() {
		return $this->commentInfoDao->sourceList();
	}

	public function get($source, $product_name, $only_display, $have_picture, $only_negative, $page_num, $page_size) {
		return $this->commentInfoDao->get($source, $product_name, $only_display, $have_picture, $only_negative, $page_num, $page_size);
	}

	public function display($id_comment, $display, $operator) {
		return $this->commentInfoDao->display($id_comment, $display, $operator);
	}

	public function importExternal($data) {
		return $this->commentInfoDao->importExternal($data);
	}

	public function externalInfo($keyword, $page_num, $page_size) {
		return $this->commentInfoDao->externalInfo($keyword, $page_num, $page_size);
	}

	public function externalInfoSingle($id_product) {
		return $this->commentInfoDao->externalInfoSingle($id_product);
	}

	public function saveExtInfo($id_product, $url_jd, $url_tm, $operator) {
		return $this->commentInfoDao->saveExtInfo($id_product, $url_jd, $url_tm, $operator);
	}

	public function getExternal($id_product, $only_display, $have_picture, $page_num, $page_size, $id_source) {
		return $this->commentInfoDao->getExternal($id_product, $only_display, $have_picture, $page_num, $page_size, $id_source);
	}

	public function getNature($product_name, $only_negative, $have_picture, $page_num, $page_size) {
		return $this->commentInfoDao->getNature($product_name, $only_negative, $have_picture, $page_num, $page_size);
	}
}
