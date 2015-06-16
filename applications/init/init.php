<?php
/**
 * 应用初始化
 * @author Keven.Zhong
 * @Version 1.0 At 2014-01-01
 */

//定义应用路径
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', dirname(__FILE__) . '/..');
$config = array();
//加载配置
$config += include(APPLICATION_PATH . '/configs/sys.ini');
$config += include(APPLICATION_PATH . '/configs/app.ini');
$config += include(APPLICATION_PATH . '/configs/db.ini');

//加载timezone
if (!empty($config['common']['timezone'])) {
    date_default_timezone_set($config['common']['timezone']);
}
//Autoloader
require_once $config['library']['coreLibrary'] . '/Core/Loader.php';
$saveLib = Array();
foreach ($config['library'] as $lib){
    $saveLib[] = $lib;
}
Core_Loader::setBasePath($saveLib);

//初始化Db配置
Core_Pdo::$config = $config['db'];
