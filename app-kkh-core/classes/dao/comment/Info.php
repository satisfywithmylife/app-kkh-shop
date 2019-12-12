<?php
//todo 评论小程序前端传过来的id_product是新id还是老id

apf_require_class("APF_DB_Factory");

class Dao_Comment_Info {
	private $pdo;

	public function __construct() {
		$this->pdo = APF_DB_FACTORY::get_instance()->get_pdo('shop_master');
	}
 
	/*
   	* 获取商品列表
	* @return array
   	*/
	public function productList() {
		$ret = array();
		//$sql = 'select distinct pl.id_product, pl.name from s_product_lang pl left join s_product p on p.active=1';
		$sql = "SELECT DISTINCT
					p.id_product,
					pl.name
				FROM
					s_product p,
					s_product_lang pl
				WHERE
					p.id_product = pl.id_product
					AND p.active = 1
				GROUP BY
					p.id_product";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();
		$ret = $stmt->fetchAll();

		return $ret;
	}

	public function externalInfo($keyword, $page_num, $page_size) {
		$ret = array();
		
		$id_product_arr = array();
		if($keyword !== '') {
			$sql = 'select distinct id_product from s_product_lang where name like "%' . $keyword . '%"';
			Logger::info('sql = ' . $sql);
			$stmt = $this->pdo->prepare($sql);
			$stmt->execute();
			$rows = $stmt->fetchAll();
			if(!empty($rows)) {
				foreach($rows as $row) {
					$id_product_arr[] = intval($row['id_product']);
				}
			}
		}
		$total_num = 0;
		$sql = 'select count(*) as num from t_pc_ext_info';
		if(!empty($id_product_arr)) {
			$sql .= ' where id_product in (' . implode(',', $id_product_arr) . ')';
		}
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();
		$row = $stmt->fetch();
		if(!empty($row) && !empty($row['num'])) {
			$total_num = intval($row['num']);
		}
		
		$id_end = $total_num - ($page_num - 1) * $page_size;
		if($id_end <= 0) {
			return $ret;
		}
		$id_begin = $id_end - $page_size;
		if($id_begin < 0) {
			$id_begin = 0;
		}

		$sql = 'select pcei.id_product, pcei.url_jd, pcei.url_tm, pcei.operator, pcei.update_ts, pcei.create_ts as created_at, pl.name as product_name from t_pc_ext_info pcei left join s_product_lang pl on pcei.id_product = pl.id_product where pl.id_lang = 1';
		
		if(!empty($id_product_arr)) {
			$sql .= ' and pcei.id_product in (' . implode(',', $id_product_arr) . ')';
		}
		$sql .= ' order by pcei.create_ts desc limit ' . $id_begin . ', ' . $page_size;
//		Logger::info('sql2 = ' . $sql);
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchAll();

		if(!empty($rows)) {
			foreach($rows as &$row) {
				$id_product = intval($row['id_product']);
				$sql = 'select count(*) as num from t_product_comment where id_product = :id_product and id_source = :id_source and display = :display';
				$stmt = $this->pdo->prepare($sql);
				$stmt->execute(array(
					':id_product' => $id_product,
					':id_source' => 1,
					':display' => 1
				));
				$row_jd = $stmt->fetch();

				$stmt->execute(array(
					':id_product' => $id_product,
					':id_source' => 2,
					':display' => 1
				));
				$row_tm = $stmt->fetch();

				$num_jd = 0;
				$num_tm = 0;
				
				if(!empty($row_jd) && !empty($row_jd['num'])) {
					$num_jd = intval($row_jd['num']);
				}
				if(!empty($row_tm) && !empty($row_tm['num'])) {
					$num_tm = intval($row_tm['num']);
				}

				$row['num_jd'] = $num_jd;
				$row['num_tm'] = $num_tm;
			}
		}
		
		$ret['list'] = $rows;
		$ret['total'] = $total_num;
		$ret['page_num'] = $page_num;
		$ret['keyword'] = $keyword;
		return $ret;
	}

	public function externalInfoSingle($id_product) {
		$ret = array();
		
		$sql = 'select pcei.id_product, pcei.url_jd, pcei.url_tm, pcei.operator, pcei.update_ts, pcei.create_ts as created_at, pl.name as product_name from t_pc_ext_info pcei left join s_product_lang pl on pcei.id_product = pl.id_product where pl.id_lang = 1 and pcei.id_product = ' . $id_product;
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();
		
		$row = $stmt->fetch();
		if(empty($row)) {
			return $ret;
		}

		$sql = 'select count(*) as num from t_product_comment where id_product = :id_product and id_source = :id_source and display = :display';
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute(array(
			':id_product' => $id_product,
			':id_source' => 1, 
			':display' => 1
		));
		$row_jd = $stmt->fetch();

		$stmt->execute(array(
			':id_product' => $id_product,
			':id_source' => 2,
			':display' => 1
		));
		$row_tm = $stmt->fetch();

		$num_jd = 0;
		$num_tm = 0;

		if(!empty($row_jd) && !empty($row_jd['num'])) {
			$num_jd = intval($row_jd['num']);
		}
		if(!empty($row_tm) && !empty($row_tm['num'])) {
			$num_tm = intval($row_tm['num']);
		}

		$row['num_jd'] = $num_jd;
		$row['num_tm'] = $num_tm;

		$ret = $row;

		return $ret;
	}

	/**
	 * 外部评论 - 获取评论来源列表 not used
	 */
	public function sourceList() {
		$ret = array();

		$sql = '';
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();
		$ret = $stmt->fetchAll();

		return $ret;
	}

	/**
	 * 展示/隐藏评论 todo error处理
	 * @param $id_comment
	 * @param $display
	 * @param $operator
	 */
	public function display($id_comment, $display, $operator) {
		$sql = 'update t_product_comment set display = :display, operator = :operator where id_comment = :id_comment';
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute(array(
			':display' => $display,
			':operator' => $operator,
			':id_comment' => $id_comment
		)); 
		
		return true;
	}

	/** 
     * 导入外部评论 todo 是否要rawurldecode; todo 事务
     * @param $data
     * @return mixed
     */
    public function importExternal($data) {
		$sql_ext_user = 'insert into t_pc_ext_user(kkid, name, user_photo, update_ts) values';
		foreach($data as $v) {
			$id_product = $v['id_product'];
			$kkid = $v['kkid'];
			$quality_score = $v['quality_score'];
			$service_score = $v['service_score'];
			$logistics_score = $v['logistics_score'];
			$content = $v['content'];
			$picture = $v['picture'];
			$display = $v['display'];
			$id_source = $v['id_source'];
			$comment_nature = $v['comment_nature'];
			$comment_ts = $v['comment_ts'];

			$name = $v['name'];
			$user_photo = $v['user_photo'];

			$picture = json_decode($picture, true); //todo
			if(!empty($picture)) {
				$have_picture = 1;
			} else {
				$have_picture = 0;
			}
			
			$sql = 'insert into t_product_comment(id_product, kkid, quality_score, service_score, logistics_score, content, picture, display, id_source, comment_nature, comment_ts) values';
			$sql .= '(' . $id_product . ', \'' . addslashes($kkid) . '\', ' . $quality_score . ', ' . $service_score . ', ' . $logistics_score
					. ', \'' . addslashes($content) . '\', ' . $have_picture . ', ' . $display . ', ' . $id_source
					. ', ' . $comment_nature . ', \'' . addslashes($comment_ts) . '\')';

			$stmt = $this->pdo->prepare($sql);
			$stmt->execute();
			$id_comment = intval($this->pdo->lastInsertId());
			if($id_comment > 0) {
        	    if(!empty($picture)) {
    	            $sql = 'insert into t_pc_picture(id_comment, picture, comment_ts) values';
	                foreach($picture as $v) {
                	    $sql .= '(' . $id_comment . ', \'' . addslashes($v) . '\', \'' . addslashes($comment_ts) . '\'),';
            	    }   
        	        $sql = substr($sql, 0, strlen($sql) - 1); 
    	            Logger::info('sql = ' . $sql);
	                $stmt = $this->pdo->prepare($sql);
                	$stmt->execute();
            	}   
			} else {
    	        Logger::info(__METHOD__ . ' last_inert_id invalid, last_insert_id = ' . json_encode($id_comment));
        	}

			$sql_ext_user .= '(\'' . addslashes($kkid) . '\', \'' . addslashes($name) . '\', \'' . addslashes($user_photo) . '\', \'' . addslashes($comment_ts) . '\'), ';
		}

		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();

		$sql_ext_user = substr($sql_ext_user, 0, strlen($sql_ext_user) - 2);
	//	Logger::info('sql_ext_user = ' . $sql_ext_user);
		$stmt = $this->pdo->prepare($sql_ext_user); //todo error回滚
		$stmt->execute();

		return true;
    }

	//todo shiwu
	public function saveExtInfo($id_product, $url_jd, $url_tm, $operator) {
		$sql = 'select id from t_pc_ext_info where id_product = :id_product';
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute(array(
			':id_product' => $id_product
		));
		$row = $stmt->fetch();
		$time_now = date('Y-m-d H:i:s');
		if(empty($row)) {
			$sql = 'insert into t_pc_ext_info(id_product, url_jd, url_tm, operator, update_ts, create_ts) values(:id_product, :url_jd, :url_tm, :operator, :update_ts, :create_ts)';
			$stmt = $this->pdo->prepare($sql);
			$stmt->execute(array(
				':id_product' => $id_product,
				':url_jd' => $url_jd,
				':url_tm' => $url_tm,
				':operator' => $operator,
				':update_ts' => $time_now,
				':create_ts' => $time_now
			));
		} else {
			$sql = 'update t_pc_ext_info set url_jd = :url_jd, url_tm = :url_tm, operator = :operator, update_ts = :update_ts where id_product = :id_product';
			$stmt = $this->pdo->prepare($sql);
			$stmt->execute(array(
				':id_product' => $id_product,
				':url_jd' => $url_jd,
				':url_tm' => $url_tm,
				':operator' => $operator,
				':update_ts' => $time_now
			));
		}

		return true;
	}


	//===============================
	/**
     * 获取自然评论 todo 
     */
	public function getNature($product_name, $only_negative, $have_picture, $page_num, $page_size) {
		$ret = array();
		
		$id_product_arr = array();
		$sql = 'select distinct id_product from s_product_lang where name like "%' . $product_name . '%"';
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchAll();
		if(!empty($rows)) {
			foreach($rows as $row) {
				$id_product_arr[] = intval($row['id_product']);
			}
		}
		if(empty($rows) || empty($id_product_arr)) {
			Logger::info(__METHOD__ . ' invalid product_name, no id_product match this product_name');
			return $ret;
		}
		
		$comment_num = $this->get_comment_num_nature($id_product_arr, $only_negative, $have_picture);
		Logger::info('num = ' . $comment_num);
        if($comment_num === 0) {
            return $ret;
        }   

        //因为结果是最新评论在上, 要根据id_comment倒序查找 todo 这种做法有删除评论时会错
        $id_comment_begin = ($page_num - 1) * $page_size; //notice: limit 1, 2 : not include 1
        if(empty($product_name)) { //获取全部商品的自然评论
			$sql = 'select pl.name as product_name, pc.id_comment, pc.kkid, pc.content, pc.operator, pc.display, pc.comment_ts, pc.comment_ts as created_at from t_product_comment pc left join s_product_lang pl on pc.id_product = pl.id_product where pc.id_source = 0';
			if($only_negative) {
				$sql .= ' and pc.comment_nature = 3';
			}
			if($have_picture) {
				$sql .= ' and pc.picture = 1';
			}
	        $sql .= ' and pl.id_lang = 1';
    	    $sql .= ' order by id_comment desc limit ' . $id_comment_begin . ', ' . $page_size;
			Logger::info('sql = ' . $sql);
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll();
        } else { //获取单个商品的自然评论 todo operator_ts?,  limit id_comment_begin, id_comment_end这样不行
			$sql = 'select pl.name as product_name, pc.id_comment, pc.kkid, pc.content, pc.operator, pc.display, pc.comment_ts, pc.comment_ts as created_at from t_product_comment pc left join s_product_lang pl on pc.id_product = pl.id_product where pc.id_source = 0';
            if($only_negative) {
                $sql .= ' and pc.comment_nature = 3';
            }
			if($have_picture) {
				$sql .= ' and pc.picture = 1';
			}

            $sql .= ' and pc.id_product in (' . implode(',', $id_product_arr) . ')';
			$sql .= ' and pl.id_lang = 1';
            $sql .= ' order by pc.id_comment desc limit ' . $id_comment_begin . ', ' . $page_size;
            Logger::info('sql = ' . $sql);
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll();
        }

		if(empty($rows)) {
			return $ret;
		}
		foreach($rows as &$row) {
			$kkid = $row['kkid'];
			$sql = 'select name from t_users where kkid = :kkid';
			$pdo_user = APF_DB_FACTORY::get_instance()->get_pdo('master');
			$stmt = $pdo_user->prepare($sql);
			$stmt->execute(array(
			 	':kkid' => $kkid
			));

			 $row1 = $stmt->fetch();
			 if(!empty($row1) && !empty($row1['name'])) {
			 	$row['name'] = $row1['name'];
			 } else {
			 	$row['name'] = '';
			 }
			
			 $row['display'] = (intval($row['display']) === 1) ? true : false;

			 $id_comment = intval($row['id_comment']);
			 $row['picture'] = $this->get_picture($id_comment);

			 unset($row['kkid']);
		}
		
		$ret['list'] = $rows;
		$ret['total'] = $comment_num;
		$ret['page_num'] = $page_num;
		$ret['keyword'] = $product_name;
		return $ret;
	}

	/**
	 * 获取外部评论
	 */
	public function getExternal($id_product, $only_display, $have_picture, $page_num, $page_size, $id_source) {
		$ret = array();
		
		$id_product_arr = array($id_product);
		$comment_num = $this->get_comment_num_external($id_product_arr, $only_display, $have_picture, $id_source);
        if($comment_num === 0) {
    		return $ret;
	   	}   
		
		//因为结果是最新评论在上, 要根据id_comment倒序查找
		$id_comment_begin = ($page_num - 1) * $page_size; //notice: limit 1, 2 : not include 1

		$sql = 'select pl.name as product_name, pc.id_comment, pc.kkid, pc.content, pc.id_source, pc.operator, pc.display, pc.comment_ts, pc.comment_ts as created_at from t_product_comment pc left join s_product_lang pl on pc.id_product = pl.id_product where';
		if($id_source === '') {
			$sql .= ' id_source != 0';
		} else {
			$sql .= ' id_source = ' . $id_source;
		}

		if($only_display === 1) {
			$sql .= ' and pc.display = ' . $only_display;	
		}

		if($have_picture === 1) {
			$sql .= ' and pc.picture = 1';
		}

        $sql .= ' and pc.id_product = ' . $id_product;
		$sql .= ' and pl.id_lang = 1'; 
        $sql .= ' order by id_comment desc limit ' . $id_comment_begin . ', ' . $page_size;
        Logger::info('sql = ' . $sql);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll();
		
		if(empty($rows)) {
			return $ret;
		}

		foreach($rows as &$row) {
			$kkid = $row['kkid'];
			$sql = 'select name from t_pc_ext_user where kkid = :kkid';
			$stmt = $this->pdo->prepare($sql);
			$stmt->execute(array(
				':kkid' => $kkid
			));
			$row1 = $stmt->fetch();
			if(!empty($row1) && !empty($row1['name'])) {
				$row['name'] = $row1['name'];
			} else {
				$row['name'] = '';
			}
			
			if(intval($row['id_source']) === 1) {
				$row['id_source'] = '京东';
			} else {
				$row['id_source'] = '天猫';
			}
		    
			$row['display'] = (intval($row['display']) === 1) ? true : false;

			$id_comment = intval($row['id_comment']);
			$row['picture'] = $this->get_picture($id_comment);

			unset($row['kkid']);
		}

		$ret['list'] = $rows;
		$ret['total'] = $comment_num;
		$ret['page_num'] = $page_num;
		$ret['id_source'] = $id_source;
        return $ret;	
	}

    /**
     * 获取商品的评论数 todo type
     * @param $id_product_arr
	 * @param $source - 0:自然评论, 1:外部评论
     * @return int
     */
    public function get_comment_num($id_product_arr, $id_source = 0) {
        $comment_num = 0;
		
		if($id_source === '') {
			$sql = 'select count(id_comment) as comment_num from t_product_comment where id_product in (' . implode(',', $id_product_arr) . ') and id_source != 0';
		} else {
			$sql = 'select count(id_comment) as comment_num from t_product_comment where id_product in (' . implode(',', $id_product_arr) . ') and id_source = ' . $id_source;
		}

		Logger::info('sql = ' . $sql);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch();
        if(!empty($row) && !empty($row['comment_num'])) {
            $comment_num = intval($row['comment_num']);
        }

        return $comment_num;
    }

	public function get_comment_num_nature($id_product_arr, $only_negative, $have_picture) {
		$comment_num = 0;

		$sql = 'select count(id_comment) as comment_num from t_product_comment where id_product in (' . implode(',', $id_product_arr) . ') and id_source = 0';
		if($only_negative === 1) {
			$sql .= ' and comment_nature = 3';
		}
		if($have_picture) {
			$sql .= ' and picture = 1';
		}

		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();
		$row = $stmt->fetch();
		if(!empty($row) && !empty($row['comment_num'])) {
			$comment_num = intval($row['comment_num']);
		}

		return $comment_num;
	}

	public function get_comment_num_external($id_product_arr, $only_display, $have_picture, $id_source) {
		$comment_num = 0;
		if(empty($id_product_arr)) {
			return $comment_num;
		}

		if($id_source == '') {
			$sql = 'select count(id_comment) as comment_num from t_product_comment where id_product in (' . implode(',', $id_product_arr) . ') and id_source != 0';
		} else {
			$sql = 'select count(id_comment) as comment_num from t_product_comment where id_product in (' . implode(',', $id_product_arr) . ') and id_source = ' . $id_source;
		}

		$sql = 'select count(id_comment) as comment_num from t_product_comment where id_product in (' . implode(',', $id_product_arr) . ') and id_source != 0';
		if($only_display) {
			$sql .= ' and display = 1';
		}
		if($have_picture) {
			$sql .= ' and picture = 1';
		}
		Logger::info('sql = ' . $sql);
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();
		$row = $stmt->fetch();
		if(!empty($row) && !empty($row['comment_num'])) {
			$comment_num = intval($row['comment_num']);
		}

		return $comment_num;
	}

	public function get_picture($id_comment) {
        $ret = array();

        $sql = 'select picture from t_pc_picture where id_comment = :id_comment';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array(
            ':id_comment' => $id_comment
        )); 
        $rows = $stmt->fetchAll();
    
        if(empty($rows)) {
            return $ret;
        }   

        foreach($rows as $row) {
            $ret[] = $row['picture'];
        }   

        return $ret;
    }
}
