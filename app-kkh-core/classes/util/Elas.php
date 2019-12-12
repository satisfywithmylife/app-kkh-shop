<?php
//include '/data/webapp/php-v2/DEV/SHOP-QA/2017_08_18/app-kkh-shop/vendor/autoload.php';

class Util_Elas 
{
	public $client;
/*	public $ik_domain;
	public $pinyin_domain;

	public function __construct()
	{
		$this->ik_domain = IK_DOMAIN;
		$this->pinyin_domain = PINYIN_DOMAIN;
		$this->client = Elasticsearch\ClientBuilder::create()->build();
	}
*/

    /*public function handle_request()
    {

        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET, POST, OPTIONS, DELETE");
        header("Access-Control-Allow-Headers:SIG,DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type, Accept-Language, Origin, Accept-Encoding");
        header("Content-type: application/json; charset=utf-8");
        
		$req    = APF::get_instance()->get_request();
        $params = $req->get_parameters();
        Logger::info(__FILE__, __CLASS__, __LINE__, var_export($params, true));
 
        $security = Util_Security::Security($params);
 
        if (!$security) {
			echo self::json_str(400, 'illegal_request', array());
			return false;
        }

		$action = isset($params['action']) ? $params['action'] : 'search';
		
		switch($action){
			case 'search':
			echo $this->json_str(200, 'success', self::search($params['data']));
			break;
			case 'add_inex':
			echo $this->json_str(200, 'success', self::add_index($params['data']));
			break;
            case 'add_document':
			echo $this->json_str(200, 'success', self::add_document($params['data']));
            break;
			case 'del_index':
            echo $this->json_str(200, 'success', self::del_index($params['data']));
            break;
			case 'del_document':
            echo $this->json_str(200, 'success', self::del_document($params['data']));
            break;
			case 'update_document':
            echo $this->json_str(200, 'success', self::update_document($params['data']));
            break;
			case 'get_document':
            echo $this->json_str(200, 'success', self::get_document($params['data']));
            break;
			default :
			echo $this->json_str(400, 'wrong action type', array());

		}
		return false;

	}
	*/

	public function ik($params){
		$url = $this->ik_domain;
		Util::http_post($url, $params, 0);
	}

	public function pinyin($params){
		$url = $this->pinyin_domain;
		Util::http_post($url, $params, 0);
	}

	public function add_index($params){
		/*
		$params = [
			'index' => 'my_index',
			'type' => 'my_index',
			'body' => [
				'settings' => [
					'number_of_shards' => 2,
					'number_of_replicas' => 0,
				],
			],
		];
		*/
		$this->client->create($params);
	}

	public function add_document($params){
		/*
		$params = array();
        $params['body'] = array(
            'testField' => 'dfdsfdsf'
        );
        $params['index'] = 'my_index';
        $params['type'] = 'my_index';
        $params['id'] = 'w1231313';
        */
		$ret = $this->client->index($params);	
	}

	public function del_index($params){
		//$deleteParams['index'] = $index;
        $this->client->indices()->delete($params);
	}

	public function del_document($params){
		/*
		$deleteParams = array();
        $deleteParams['index'] = 'my_index';
        $deleteParams['type'] = 'my_index';
        $deleteParams['id'] = 'AU4Kmmj-WOmOrmyOj2qf';
        */
		$retDelete = $this->client->delete($params);
	}

	public function update_ducoment($params){
		/*
		$updateParams = array();
        $updateParams['index'] = 'my_index';
        $updateParams['type'] = 'my_index';
        $updateParams['id'] = 'my_id';
        $updateParams['body']['doc']['asas']  = '111111';
       	*/
		$response = $this->client->update($params);
	}

	public function get_document($params){
		/*
		$updateParams = array();
        $updateParams['index'] = 'my_index';
        $updateParams['type'] = 'my_index';
        $updateParams['id'] = 'my_id';
        $updateParams['body']['doc']['asas']  = '111111';
       	*/
		$response = $this->client->update($params);
	}

	public function search($params){
		
		/*
	    $searchParams['index'] = 'my_index';
        $searchParams['type'] = 'my_index';
        $searchParams['from'] = 0;
        $searchParams['size'] = 100;
        $searchParams['sort'] = array(
            '_score' => array(
                'order' => 'desc'
            )
        );
		*/
        //$searchParams['body']['query']['match']['testField'] = 'abc';
        $retDoc = $this->client->search($params);
       	return $retDoc;
	}

}

