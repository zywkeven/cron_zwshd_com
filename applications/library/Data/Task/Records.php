<?php 
/**
 * task_records表结构
 * @author keven.zhong
 * @Version： 1.0 At 2014-04-29
 */

class Data_Task_Records {
    
    //唯一id 
    public $id = 0;
    //任务类型
    public $type = 1;
    //最后开始时间
    public $lastStartTime = 0;
    //最后结束时间
    public $lastEndTime = 0;
    //上次运行时间
    public $lastRunTime = null;
    //状态
    public $state = 0; 
    //运行间隔
    public $lifeTime = 0;
    //任务key
    public $taskKey = null;
    //记录信息  
    public $msg = '';
    //删除标志
    public $isDeleted = 0 ;
    
}