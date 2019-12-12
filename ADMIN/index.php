<?php
define('APP_NAME', 'admin');
chdir(dirname(__FILE__).'/app-kkh-'.APP_NAME);

define('APP_PATH', getcwd().'/');
define('SYS_PATH', APP_PATH."../../../system/");
define('CORE_PATH', APP_PATH.'../app-kkh-core/');

if (!defined('E_DEPRECATED')) {
    define('E_DEPRECATED',0);
}

error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING & ~E_STRICT);

$G_LOAD_PATH = array(
    CORE_PATH,
    APP_PATH,
    SYS_PATH
);
$G_CONF_PATH = array(
    CORE_PATH."config/",
    APP_PATH."config/",
    APP_PATH."../../../config/"
);

header('Content-Type:text/html;Charset=utf-8');

require_once(SYS_PATH."functions.php");
require APP_PATH.'vendor/autoload.php';
spl_autoload_register('apf_autoload');
apf_require_class("APF");
APF::get_instance()->run();
