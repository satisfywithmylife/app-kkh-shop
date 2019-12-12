<?php
apf_require_class("APF_DB_Factory");

class Dao_User_Sign {

    private $pdo;

    public function __construct() {
        $this->pdo = APF_DB_Factory::get_instance()->get_pdo("master");
    }

    public function write_session_record($data) {
        # clear current user's session
        //$sql = "delete from t_user_session where kkid = ? ;";
        //$stmt = $this->pdo->prepare($sql);
        //$stmt->execute(array($data['kkid']));
        #
        $sql = "insert into t_user_session (uid, kkid, sid, client_ip, status, created, login_from) values(:uid, :kkid, :sid, :client_ip, :status, :created, :login_from);";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    public function write_captcha_record($data) {
        #
        $sql = "insert into t_user_captcha (id, captval, client_ip, status, created) values(:id, :captval, :client_ip, :status, :created);";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    public function get_record_by_sid($kkid, $sid) {
        $sql = "select uid, kkid, sid, client_ip, status, created, login_from from t_user_session where kkid = ? and sid = ? limit 1;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array($kkid, $sid));
        return $stmt->fetch();
    }

    public function get_captcha_by_val($captval, $intval = 600) {
        $sql = "select id from t_user_captcha where captval = ? and created > (unix_timestamp()-$intval) limit 1;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array($captval));
        return $stmt->fetchColumn();
    }

    public function remove_record_by_sid($kkid, $sid) {
        $sql = "delete from t_user_session where kkid = ? and sid = ? ;";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(array($kkid, $sid));
    }

}
