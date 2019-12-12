<?php
apf_require_class("APF_Controller");

class Payment_StatusReceiveController extends APF_Controller
{
    public function __construct() {
    }


    public function handle_request()
    {
        header("Access-Control-Allow-Origin:*.kangkanghui.com");
        header("Access-Control-Allow-Methods:GET, POST, OPTIONS, DELETE");
        header("Access-Control-Allow-Headers:SIG,DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type, Accept-Language, Origin, Accept-Encoding");
        header("Content-type: application/json; charset=utf-8");
        
        $req = APF::get_instance()->get_request();
        $params = $req->get_parameters();

        Logger::info(__FILE__, __CLASS__, __LINE__, 'StatusReceive: Ping++');
        Logger::info(__FILE__, __CLASS__, __LINE__, var_export($params, true));

        $res = $params;
        Util_Json::render(200, null, $msg, $res);
        return ;
    }

/*
####################################################################
Insert Statement
####################################################################
insert into `t_payment_charge` (`pid`, `kkid`, `r_kkid`, `charge_id`, `charge_created`, `channel`, `order_no`, `client_ip_pp`, `amount`, `currency`, `subject`, `body`, `time_paid`, `time_expire`, `payment_status`, `status`, `created`, `update_date`, `client_ip`) values(:pid, :kkid, :r_kkid, :charge_id, :charge_created, :channel, :order_no, :client_ip_pp, :amount, :currency, :subject, :body, :time_paid, :time_expire, :payment_status, :status, :created, :update_date, :client_ip);
####################################################################
Update Statement
####################################################################
update `t_payment_charge` set `pid` = :pid, `kkid` = :kkid, `r_kkid` = :r_kkid, `charge_id` = :charge_id, `charge_created` = :charge_created, `channel` = :channel, `order_no` = :order_no, `client_ip_pp` = :client_ip_pp, `amount` = :amount, `currency` = :currency, `subject` = :subject, `body` = :body, `time_paid` = :time_paid, `time_expire` = :time_expire, `payment_status` = :payment_status, `status` = :status, `created` = :created, `update_date` = :update_date, `client_ip` = :client_ip where `pid` = :pid ;
####################################################################
Select Statement
####################################################################
select `pid`, `kkid`, `r_kkid`, `charge_id`, `charge_created`, `channel`, `order_no`, `client_ip_pp`, `amount`, `currency`, `subject`, `body`, `time_paid`, `time_expire`, `payment_status`, `status`, `created`, `update_date`, `client_ip` from `t_payment_charge` where `pid` = ? ;
####################################################################
PHP PDO Statement
####################################################################

  $stmt = $this->pdo->prepare($sql);
  $stmt->execute($res);
  
  $last_id = $this->pdo->lastInsertId();
  $cols = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $stmt = $this->pdo->prepare($sql);
    $stmt->bindParam(':uid', $uid, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

Help Document:
https://secure.php.net/manual/zh/class.pdostatement.php
*/

}
