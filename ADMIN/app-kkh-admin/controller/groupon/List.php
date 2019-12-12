<?php
apf_require_class("APF_Controller");

class Groupon_ListController extends APF_Controller
{
    private $pdo;
	private $shop_pdo;

    public function __construct() {
        $this->pdo = APF_DB_Factory::get_instance()->get_pdo("groupon_master");
		$this->shop_pdo = APF_DB_Factory::get_instance()->get_pdo("shop_master");
    }   

    public function handle_request()
    {   

        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET, POST, OPTIONS, DELETE");
        header("Access-Control-Allow-Headers:SIG,DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type, Accept-Language, Origin, Accept-Encoding");
        header("Content-type: application/json; charset=utf-8");

        $req = APF::get_instance()->get_request();
        $params = $req->get_parameters();

//        $username = isset($params['username']) ? $params['username'] : ''; 
//        $access_token = isset($params['access_token']) ? $params['access_token'] : '';
				

		$list = $this->get_online_goupon_list();

		echo Util_Json::json_str(200, 'success', $list);
		return false;
	}

	public function get_online_goupon_list(){
		$sql = "select `id_group`, `kkid`, `p_kkid`, `id_product`, `from_date`, `to_date`, `vouchers`, `is_online`, `is_active`, `discount_amount`, `created_by`, `last_modified`, `created_at`, `updated_at` from `s_product_group` where is_online = 1 and is_active = 1 and from_date <= date(now()) and to_date >= date(now())  order by id_group desc;";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();
		$res = $stmt->fetchAll();
		if(!$res){
			return array();
		}
		foreach($res as $k=>$v){
			$res[$k]['name'] = $this->get_product_detail($v['id_product']);
		}
		return $res;
	}

	public function get_product_detail($id_product){
		$sql = "select name from s_product_lang where id_lang = 1 and id_product = ? limit 1;";
		$stmt = $this->shop_pdo->prepare($sql);
		$stmt->execute(array($id_product));
		$res = $stmt->fetchColumn();
		return $res;
	}
}
