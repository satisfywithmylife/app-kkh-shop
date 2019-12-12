<?php

class  Bll_Order_Config {
	private $Dao;

	public function __construct() {
		//$this->Dao = new Dao_Order_Config();
	}

    public function get_order_config() {
    	$res = [
			'order_source' =>[
				[
					'id' => 1,
					'name' => '微信公众号'
				],
				[
					'id' =>2,
					'name' => '微信小程序'
				],
				[
					'id' =>3,
					'name' => 'app',
				],
				[
					'id' =>4,
					'name' => '线下',
				],

			],
			'order_type' =>[
				[
					'id' =>1,
					'name' => '购物车',
				],
				[
					'id' =>2,
					'name' => '立即购买',
				],
				[
					'id' =>3,
					'name' => '拼团',
				],
				[
					'id' =>4,
					'name' => '医生推荐',
				],
				[
					'id' =>5,
					'name' =>'新零售',
				]
			],
		];
		return $res;
	}
}
