<?php
apf_require_class("APF_DB_Factory");

class Dao_Trend_Info {

	private $pdo;

	public function __construct() {
		$this->pdo = APF_DB_Factory::get_instance()->get_pdo("master");
	}
        
        public function get_trend_list($u_kkid, $date_range, $limit, $offset, $d_name, $h_name)
        {
            $search = "";
            $search1 = "";
            if(!empty($d_name)){
              $r = explode(' ', $d_name);
              $d_name = isset($r[0]) ? $r[0] : $d_name;
              $search = "and name like '%$d_name%'";
            }

            if(!empty($h_name)){
              $r = explode(' ', $h_name);
              $h_name = isset($r[0]) ? $r[0] : $h_name;
              $search1 = "and buyer_hospital like '%$h_name%'";
            }
            $condition = "";
            //上月、本季度、上季度、本年。
            // 全部 == 本年
            //0:全部 1:上月 2: 本季度 3:上季度
            $season = self::get_season();
            switch ($date_range) {
                case 0:
                    $condition = "";
                    break;
                case 1:
                    $condition = "and substr(trans_date, 1,7) = substr(CURRENT_DATE() - INTERVAL 1 MONTH, 1,7)";
                    break;
                case 2:
                    $condition = "and trans_date between '".$season['0']."' and '".$season['1']."' ";
                    break;
                case 3:
                    $condition = "and trans_date between '".$season['2']."' and '".$season['3']."' ";
                    break;
            }
            $cond1 = "";
            if(!empty($u_kkid)){
                   $cond1 = " and h_kkid in (select h_kkid from t_serve_scope where status = 1 and u_kkid = :u_kkid ) and d_kkid in (select d_kkid from t_serve_scope where status = 1 and u_kkid = :u_kkid) ";
            }

            $sql = "select name, t_num, trans_date, buyer_hospital from t_transaction where email like '%@leapfrogchina.com' and name!='' and status=1 $search $search1 $condition $cond1 order by trans_date desc LIMIT :limit OFFSET :offset  ;";
            //Logger::info(__FILE__, __CLASS__, __LINE__, var_export($backup, true));
            Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
            //Logger::info(__FILE__, __CLASS__, __LINE__, 'limit: '.$limit);
            //Logger::info(__FILE__, __CLASS__, __LINE__, 'offset: '.$offset);
            //Logger::info(__FILE__, __CLASS__, __LINE__, 'u_kkid: '.$u_kkid);
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            if(!empty($u_kkid)){
                $stmt->bindParam(':u_kkid', $u_kkid, PDO::PARAM_STR);
            }
            $stmt->execute();
            $jobs = $stmt->fetchAll();
            $job = array();
            foreach($jobs as $k=>$j){
              $r = explode('  ', $j['name']);
              $j['name'] = isset($r[0]) ? $r[0] : $j['name'];
              $j['specs'] = isset($r[1]) ? $r[1] : '';
              $job[$k] = $j;
            }

            return $job;
        }

        public function get_trend_count($u_kkid, $date_range, $d_name, $h_name)
        {
            $c = 0;
            $search = "";
            $search1 = "";
            if(!empty($d_name)){
              $r = explode(' ', $d_name);
              $d_name = isset($r[0]) ? $r[0] : $d_name;
              $search = "and name like '%$d_name%'";
            }

            if(!empty($h_name)){
              $r = explode(' ', $h_name);
              $h_name = isset($r[0]) ? $r[0] : $h_name;
              $search1 = "and buyer_hospital like '%$h_name%'";
            }

            $condition = "";
            //上月、本季度、上季度、本年。
            // 全部 == 本年
            //0:全部 1:上月 2: 本季度 3:上季度
            $season = self::get_season();
            switch ($date_range) {
                case 0:
                    $condition = "";
                    break;
                case 1:
                    $condition = "and substr(trans_date, 1,7) = substr(CURRENT_DATE() - INTERVAL 1 MONTH, 1,7)";
                    break;
                case 2:
                    $condition = "and trans_date between '".$season['0']."' and '".$season['1']."' ";
                    break;
                case 3:
                    $condition = "and trans_date between '".$season['2']."' and '".$season['3']."' ";
                    break;
            }
            $cond1 = "";
            if(!empty($u_kkid)){
                   $cond1 = " and h_kkid in (select h_kkid from t_serve_scope where status = 1 and u_kkid = :u_kkid ) and d_kkid in (select d_kkid from t_serve_scope where status = 1 and u_kkid = :u_kkid) ";
            }
            $get_count_sql = "select count(*) c from t_transaction where email like '%@leapfrogchina.com' and name!='' and status=1 $search $search1 $condition $cond1 ;";
            Logger::info(__FILE__, __CLASS__, __LINE__, $get_count_sql);
            $stmt = $this->pdo->prepare($get_count_sql);
            if(!empty($u_kkid)){
                $stmt->bindParam(':u_kkid', $u_kkid, PDO::PARAM_STR);
            }
            $stmt->execute();
            $c = $stmt->fetchColumn();
            return $c;
        }

        public function get_season()
        {
            $season = ceil((date('n'))/3);//当月是第几季度
            
            //echo '<br>本季度起始时间:<br>' . "\n";
            $s1 = date('Y-m-d H:i:s', mktime(0, 0, 0,$season*3-3+1,1,date('Y')));
            $s2 = date('Y-m-d H:i:s', mktime(23,59,59,$season*3,date('t',mktime(0, 0 , 0,$season*3,1,date("Y"))),date('Y')));
            
            $season = ceil((date('n'))/3)-1;//上季度是第几季度
            
            //echo '<br>上季度起始时间:<br>' . "\n";
            $s3 = date('Y-m-d H:i:s', mktime(0, 0, 0,$season*3-3+1,1,date('Y')));
            $s4 = date('Y-m-d H:i:s', mktime(23,59,59,$season*3,date('t',mktime(0, 0 , 0,$season*3,1,date("Y"))),date('Y')));
            $data = array(
                        '0'=> $s1,
                        '1'=> $s2,
                        '2'=> $s3,
                        '3'=> $s4,
                       );
            return $data;
        }

        public function get_trend_drug_sum($u_kkid, $date_range, $drug_name, $h_name)
        {
            $c = 0;
            $search = "";
            $search1 = "";
            $r = explode(' ', $drug_name);
            $drug_name = isset($r[0]) ? $r[0] : $drug_name;
            if(!empty($drug_name)){
              $search = "and name like '%$drug_name%'";
            }
            else{
              return $c;
            }

            if(!empty($h_name)){
              $r = explode(' ', $h_name);
              $h_name = isset($r[0]) ? $r[0] : $h_name;
              $search1 = "and buyer_hospital like '%$h_name%'";
            }

            $condition = "";
            //上月、本季度、上季度、本年。
            // 全部 == 本年
            //0:全部 1:上月 2: 本季度 3:上季度
            $season = self::get_season();
            switch ($date_range) {
                case 0:
                    $condition = "";
                    break;
                case 1:
                    $condition = "and substr(trans_date, 1,7) = substr(CURRENT_DATE() - INTERVAL 1 MONTH, 1,7)";
                    break;
                case 2:
                    $condition = "and trans_date between '".$season['0']."' and '".$season['1']."' ";
                    break;
                case 3:
                    $condition = "and trans_date between '".$season['2']."' and '".$season['3']."' ";
                    break;
            }
            $cond1 = "";
            if(!empty($u_kkid)){
                   $cond1 = " and h_kkid in (select h_kkid from t_serve_scope where status = 1 and u_kkid = :u_kkid ) and d_kkid in (select d_kkid from t_serve_scope where status = 1 and u_kkid = :u_kkid) ";
            }
            $get_count_sql = "select sum(t_num) c from t_transaction where email like '%@leapfrogchina.com' and name!='' and status=1 $search $search1 $condition $cond1 ;";
            Logger::info(__FILE__, __CLASS__, __LINE__, $get_count_sql);
            $stmt = $this->pdo->prepare($get_count_sql);
            if(!empty($u_kkid)){
                $stmt->bindParam(':u_kkid', $u_kkid, PDO::PARAM_STR);
            }
            $stmt->execute();
            $c = $stmt->fetchColumn();
            return $c;
        }



}
