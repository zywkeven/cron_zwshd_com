<?php
/**
 * 首页
 * @author Keven.Zhong
 * @Version 1.0 At 2014-05-28
 */

require dirname(__FILE__).'/../init/init.php';
set_time_limit(0);

$config = $GLOBALS['config'];
set_time_limit(0);
try {    
    $task = new System_Task(System_Task::TASK_AI);
    $task->fixTask(); 
    $task->runCircle();
} catch (Exception $e) {
    //出错 
    echo $e->getMessage()."\n";
    App_Common::addLog('RunTask error!' .$e->getMessage(),'error');
}