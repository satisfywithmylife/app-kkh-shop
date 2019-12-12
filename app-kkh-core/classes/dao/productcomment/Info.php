<?php
/**
 * Created by PhpStorm.
 * User: hanxiaolong
 * Date: 2018/3/20
 * Time: 19:32
 * todo pdo是否需要字符串转义
 * todo pdo api 
 * todo 是否需要同时插入多条的方法, 导入外部评论时
 * todo commit需要事务
 * todo 上传的图片另开t_product_comment_image表
 */
apf_require_class("APF_DB_Factory");

class Dao_ProductComment_Info {
    private $pdo;

    public function __construct() {
        $this->pdo = APF_DB_Factory::get_instance()->get_pdo("shop_master"); //todo
    }

    /**
     * 提交评论
     * @param $data
     * @return mixed
     */
    public function commit($data) {
		$sql = 'update s_order_detail set commented = 1 where id_order_detail = :id_order_detail';
		$stmt = $this->pdo->prepare($sql);
		$exeRet = $stmt->execute(array(
			':id_order_detail' => $data['id_order_detail']
		));
		//todo 需要判断执行是否成功, 及是否真的更新了一条记录, 这样可以减少一次查询是否存在id_order_detail这条记录

        $sql = 'insert into t_product_comment(id_product, kkid, quality_score, service_score, logistics_score, content, display, id_source, comment_nature, comment_ts, picture)
                values(:id_product, :kkid, :quality_score, :service_score, :logistics_score, :content, :display, :id_source, :comment_nature, :comment_ts, :picture)';
        $stmt = $this->pdo->prepare($sql);

        //一条商品评价是否是好中差评: 几颗星=几分，质量60%+服务30%+物流10%，然后加权计算得分, 4分-5分==好评, 2分-4分==中评, 2分以下==差评
        $comment_nature = intval($data['quality_score']) * 0.6 + intval($data['service_score']) * 0.3 + intval($data['logistics_score']) * 0.1;
		$comment_nature = strval($comment_nature); //浮点型要转为字符串比较
        if($comment_nature >= 4 && $comment_nature <= 5) {
            $comment_nature = 1; //好评
        } else if($comment_nature >= 2 && $comment_nature < 4) {
            $comment_nature = 2; //中评
        } else if($comment_nature >= 0 && $comment_nature < 2) {
            $comment_nature = 3; //差评
        } else {
            //todo error
        }
		
		$picture = rawurldecode($data['picture']); //对url解码
		$picture = json_decode($picture, true); //todo
		if(!empty($picture)) { //todo
			$have_picture = 1;
		} else {
			$have_picture = 0;
		}
		
		$comment_ts = date('Y-m-d H:i:s');
        $stmt->execute(array(
            ':id_product' => $data['id_product'],
            ':kkid' => rawurldecode($data['kkid']),
            ':quality_score' => $data['quality_score'],
            ':service_score' => $data['service_score'],
            ':logistics_score' => $data['logistics_score'],
            ':content' => rawurldecode($data['content']),
            ':picture' => $have_picture,
            ':display' => 1,
            ':id_source' => 0,
            ':comment_nature' => $comment_nature,
            ':comment_ts' => $comment_ts
        ));
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
    }

    /**
     * 获取商品详情中的评论信息
     *
     * 一条商品评价是否是好中差评: 几颗星=几分，质量60%+服务30%+物流10%，然后加权计算得分, 4分-5分==好评, 2分-4分==中评, 2分以下==差评
     * 最终质量分4.7: 对该商品质量评分总和 / 评价次数  select avg
     * 好评率89.43: 商品好评数量 / 商品总评价数量
     * @param $id_product
     * @return array
     */
    public function getDetail($id_product) {
        $ret = array();

        //总评论数
        $comment_num = $this->getCommentNum($id_product, 1);
        if($comment_num === 0) {
            return $ret;
        } else {
			$ret['comment_num'] = $comment_num;
		}
		
        //最终质量分, 最终服务分, 最终物流分
        $sql = 'select avg(quality_score) as final_quality_score, avg(service_score) as final_service_score, avg(logistics_score) as final_logistics_score from t_product_comment where id_product = :id_product and display = :display';
        $stmt = $this->pdo->prepare($sql);
		$stmt->execute(array(
			':id_product' => $id_product,
			':display' => 1
		));
        $row = $stmt->fetch();
        if(empty($row)) {
            return $ret;
        } else {
            $ret['final_quality_score'] = number_format($row['final_quality_score'], 1);
            $ret['final_service_score'] = number_format($row['final_service_score'], 1);
            $ret['final_logistics_score'] = number_format($row['final_logistics_score'], 1);
        }

        //好评率
        $sql = 'select count(id_comment) as good_comment_num from t_product_comment where comment_nature = 1 and id_product = :id_product and display = :display';
        $stmt = $this->pdo->prepare($sql);
		$stmt->execute(array(
			':id_product' => $id_product,
			':display' => 1
		));
        $row = $stmt->fetch();
        if(empty($row)) {
            return $ret;
        } else {
			//Logger::info('ok11, good = ' .intval($row['good_comment_num']) . ', num = ' . $ret['comment_num']);
            $ret['favorable_rate'] = number_format(100 * intval($row['good_comment_num']) / $ret['comment_num'], 2);
        }
		
        return $ret;
    }

    /**
     * 获取评论
     * notice 外部评论的名字和头像等直接存在shop库
     * notice 无法跨库查询, 先查询一个库, 再循环查询另一个库并组合信息
	 * todo 自己的评论在前, 这个应该是最新的意义
     * @param $id_product
     * @param $type - 评论类型 1:全部 2:有图 3:最新 4:好评 5:中评 6:差评
     * @param $page_num - 第几页 end = comment_num - (page_num - 1) * page_size   begin = end - page_size
     * @param $page_size - 一个页面中的评论数
     * @return array
     */
    public function get($id_product, $type, $page_num, $page_size) {
        $ret = array();

        $comment_num = $this->getCommentNum($id_product, $type);
        if($comment_num === 0) {
            return $ret;
        } else {
            $ret['comment_num'] = $comment_num;
        }

		$id_begin = ($page_num - 1) * $page_size; //notice: limit 1, 2 : not include 1
		
        switch($type) {
            case 1: //根据索引id_comment查询分页比limit m, n效率高 todo 修正为根据id_comment查询而不是limit0, 5这种
				$sql = 'select id_comment, id_source, kkid, quality_score, service_score, logistics_score, content, comment_ts from t_product_comment 
						where id_product = :id_product and display = :display order by id_comment desc limit ' . $id_begin . ', ' . $page_size;
				//Logger::info('sql = ' . $sql . ', id_begin = ' . $id_begin . ', page_size = ' . $page_size . ', id_product = ' . $id_product);

                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(
                    array(
                        ':id_product' => $id_product,
						':display' => 1
                    )
                );
                break;
			case 2:
				$sql = 'select id_comment, id_source, kkid, quality_score, service_score, logistics_score, content, comment_ts from t_product_comment
						where id_product = :id_product and picture = :picture and display = :display order by id_comment desc limit ' . $id_begin . ', ' . $page_size;

                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(
                    array(
                        ':id_product' => $id_product,
						':picture' => 1,
						':display' => 1
                    )
                );
                break;
            case 3:
				$comment_ts_begin = date('Y-m-d H:i:s', strtotime('-30 days'));
				$comment_ts_end = date('Y-m-d H:i:s');
				
				$sql = 'select id_comment, id_source, kkid, quality_score, service_score, logistics_score, content, comment_ts from t_product_comment
						where id_product = :id_product and comment_ts between :comment_ts_begin and :comment_ts_end and display = :display order by id_comment desc limit ' . $id_begin . ', ' . $page_size;
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(
                    array(
                        ':id_product' => $id_product,
						':comment_ts_begin' => $comment_ts_begin,
						':comment_ts_end' => $comment_ts_end,
						':display' => 1
                    )
                );
                break;
            case 4:
				$sql = 'select id_comment, id_source, kkid, quality_score, service_score, logistics_score, content, comment_ts from t_product_comment
						where id_product = :id_product and comment_nature = :comment_nature and display = :display order by id_comment desc limit ' . $id_begin . ', ' . $page_size;
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(
                    array(
                        ':id_product' => $id_product,
						':comment_nature' => 1,
						':display' => 1
                    )
                );
                break;
            case 5:
				$sql = 'select id_comment, id_source, kkid, quality_score, service_score, logistics_score, content, comment_ts from t_product_comment
						where id_product = :id_product and comment_nature = :comment_nature and display = :display order by id_comment desc limit ' . $id_begin . ', ' . $page_size;
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(
                    array(
                        ':id_product' => $id_product,
						':comment_nature' => 2,
						':display' => 1
                    )
                );
                break;
            case 6:
				$sql = 'select id_comment, id_source, kkid, quality_score, service_score, logistics_score, content, comment_ts from t_product_comment
						where id_product = :id_product and comment_nature = :comment_nature and display = :display order by id_comment desc limit ' . $id_begin . ', ' . $page_size;
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(
                    array(
                        ':id_product' => $id_product,
						':comment_nature' => 3,
						':display' => 1
                    )
                );
                break;
            default:
                //todo error
        }
        $rows = $stmt->fetchAll();
		Logger::info('rows = ' . json_encode($rows));
        if(empty($rows)) {
            return $ret;
        } else {
            foreach($rows as &$row) {
				$row['quality_score'] = number_format($row['quality_score'], 1);
				$row['service_score'] = number_format($row['service_score'], 1);
				$row['logistics_score'] = number_format($row['logistics_score'], 1);
                $kkid = $row['kkid'];
                $pdo_usercenter = APF_DB_Factory::get_instance()->get_pdo("usercenter_master");
				
				if(intval($row['id_source']) === 0) {
					$sql = 'select name, user_photo from t_users where kkid = :kkid';
					$stmt = $pdo_usercenter->prepare($sql);
				} else {
					$sql = 'select name, user_photo from t_pc_ext_user where kkid = :kkid';
					$stmt = $this->pdo->prepare($sql);
				}

                $stmt->execute(array(
                    ':kkid' => $kkid
                ));
                $row_usercenter = $stmt->fetch();
                if(!empty($row_usercenter)) {
                    $row['name'] = empty($row_usercenter['name']) ? '' : $row_usercenter['name'];
                    $row['picture_url'] = empty($row_usercenter['user_photo']) ? '' : $row_usercenter['user_photo']; //todo 具体用哪个字段做头像
                } else {
                    continue;
                }

				$id_comment = intval($row['id_comment']);
				$row['picture'] = $this->get_picture($id_comment);

				unset($row['id_comment']);
				unset($row['id_source']);
            }
        }
		
//		Logger::info('888, rows = ' . json_encode($rows));
		$ret['comment_list'] = $rows;
        return $ret;
    }

    //===============================
    /**
     * 获取某商品的评论数
     * @param $id_product
	 * @param $type
     * @return int
     */
    public function getCommentNum($id_product, $type) {
        $comment_num = 0;

		switch(intval($type)) {
			case 1:
				$sql = 'select count(id_comment) as comment_num from t_product_comment where id_product = :id_product and display = :display';
		        $stmt = $this->pdo->prepare($sql);
        		$stmt->execute(array(
    	    	        ':id_product' => $id_product,
						':display' => 1
				));	
				break;
			case 2:
				$sql = 'select count(id_comment) as comment_num from t_product_comment where id_product = :id_product and picture = :picture and display = :display';
				$stmt = $this->pdo->prepare($sql);
				$stmt->execute(array(
					':id_product' => $id_product,
					':picture' => 1,
					':display' => 1
				));
				break;
			case 3:
				$comment_ts_begin = date('Y-m-d H:i:s', strtotime('-30 days'));
				$comment_ts_end = date('Y-m-d H:i:s');
				$sql = 'select count(id_comment) as comment_num from t_product_comment where id_product = :id_product and comment_ts between :comment_ts_begin and :comment_ts_end and display = :display';
				$stmt = $this->pdo->prepare($sql);
				$stmt->execute(array(
					':id_product' => $id_product,
					':comment_ts_begin' => $comment_ts_begin,
					':comment_ts_end' => $comment_ts_end,
					':display' => 1
				));
				Logger::info('sql = ' . $sql);
				break;
			case 4:
				$sql = 'select count(id_comment) as comment_num from t_product_comment where id_product = :id_product and comment_nature = :comment_nature and display = :display';
				$stmt = $this->pdo->prepare($sql);
				$stmt->execute(array(
					':id_product' => $id_product,
					':comment_nature' => 1,
					':display' => 1
				));
				break;
			case 5:
				$sql = 'select count(id_comment) as comment_num from t_product_comment where id_product = :id_product and comment_nature = :comment_nature and display = :display';
				$stmt = $this->pdo->prepare($sql);
				$stmt->execute(array(
					':id_product' => $id_product,
					':comment_nature' => 2,
					':display' => 1
				));
				break;
			case 6:
				$sql = 'select count(id_comment) as comment_num from t_product_comment where id_product = :id_product and comment_nature = :comment_nature and display = :display';
				$stmt = $this->pdo->prepare($sql);
				$stmt->execute(array(
					':id_product' => $id_product,
					':comment_nature' => 3,
					':display' => 1
				));
				break;
			default:
				Logger::info(__METHOD__ . ' invalid type');
				return $comment_num;
		}
		
		Logger::info('sql = ' . $sql);
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
