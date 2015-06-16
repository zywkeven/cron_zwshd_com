<?php
/**
 * 初始化
 * @author Keven.Zhong
 * @Version： 1.0 At 2014-04-15
 */
require dirname(__FILE__).'/../init/init.php';
set_time_limit(0);
ini_set('memory_limit','512M');
try {
    if(isset($argv[1]) && isset($argv[2])){
        $class = $argv[1];
        $method = $argv[2];
        if(method_exists($class, $method)){
            $obj = new $class();
            if(isset($argv[3])){
                $rs = $obj->$method($argv[3]);
            } else {
                $rs = $obj->$method();
            }
            echo 'run:'.$class.':' . $method . "\n";
            echo 'result:' . $rs. "\n";
        }else {
            echo 'Method not exist.' . "\n";
        }
    }else {
        echo 'Please enter param class and method.' ."\n";
    }
} catch (Exception $e) {
    echo $e->getMessage() . "\n";
    die;
}

echo 'done!'."\n";