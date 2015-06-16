<?php
/**
 * 计划任务类
 * @author Keven.Zhong
 * @Version： 1.0 At 2014-04-15
 */
class System_Task {
    
    const TASK_I = 1 ; //主动任务
    
    const TASK_P = 2 ; //被动任务
    
    const TASK_MQ = 3 ; //队列任务
    
    const TASK_AI = 4 ; //API主动任务
    
    /**
     * 任务类型 1主动 2被动 3MQ 4API主动
     * 
     * @var int
     * @access private
     */
    private $_type = 1;
    
    /**
     * 当前任务
     * 
     * @var array
     * @access private
     */
    private $_record = null;
       
    /**
     * 运行时间
     *
     * @var
     * @access private
     */
    private $_passTime = 0;
    
    /**
     * @return the $_record
     */
    public function getRecord () {
        return $this->_record;
    }

    /**
     * @param number $_type
     */
    public function setType ($_type) {
        $this->_type = intval($_type);
    }

    /**
     * 构造函数
     * 
     */
    public function __construct($type = self::TASK_I) {
        $this->_type = $type ;       
    }
    
    /**
     * 修复计划任务
     * @param string|boolen 结果信息
     */
    public function fixTask(){
    	$nowTime = time();
        $fixTime = $nowTime - 5*3600;        
        $dataTaskRecords = new Data_Task_Records();
        $dataTaskRecords->msg = 'fix';
        $dataTaskRecords->state = 0;        
        $dataTaskRecords->lastStartTime = $fixTime;
        return Model_Task_Records::fixRecords($dataTaskRecords);     
    }
    
    /**
     * 循环执行
     * @return boolean
     */
    public function runCircle(){
        $i = 0;
        $this->getWaitingRecord();
        while ($this->_record) {
            $i += 1;
            $this->run();
            $this->getWaitingRecord();
        }
        return $i;
    }
    
    /**
     * 执行一条记录
     * @return string|boolen 结果信息
     */
    public function run($data=null) {
        
        //实在找不到可用的任务，返回
        if (!$this->_record) return false;
        
        $result = false;
        $dataTaskRecords = new Data_Task_Records();
        $dataTaskRecords->id = $this->_record['id'];
        $dataTaskRecords->state = 0;
        $dataTaskRecords->lastEndTime = time();
        if (class_exists($this->_record['class'])) {
            $object = new $this->_record['class'];
            if (method_exists($object, $this->_record['method'])) {
                $method = $this->_record['method'];
                $startTime = microtime(true);
                if ($data) {
                    $result = $object->$method($data);
                } else {
                    $ruleId = intval($this->_record['source_id']);
                    if($ruleId > 0){
                        $result = $object->$method($ruleId);
                    }else {
                        $result = $object->$method();
                    }
                }
                $endTime = microtime(true);
                $dataTaskRecords->lastEndTime = time();     
                $this->_passTime = $endTime - $startTime ; 
                $dataTaskRecords->lastRunTime = intval($this->_passTime);
                if (true === $result) {
                    Model_Task_Records::updateRecords($dataTaskRecords);                   
                } else {
                    $dataTaskRecords->msg = 'RESULT:' . json_encode($result);
                    Model_Task_Records::updateRecords($dataTaskRecords);                   
                }
            } else {
                $dataTaskRecords->msg = 'METHOD NOT EXIST';
                Model_Task_Records::updateRecords($dataTaskRecords);               
            }
            unset($object);
        } else {
            $dataTaskRecords->msg = 'CLASS NOT EXIST';
            Model_Task_Records::updateRecords($dataTaskRecords);           
        }
        
        if ($this->_record['type'] != 3) {
            $msg = 'TASK_KEY: [' .$this->_record['task_key'] . 
                    '] result[' . $result . "]" .
                    " time [" .$this->_passTime. "]";
            App_Common::addLog($msg);
        }
        
        return $result;
    }
    
    /**
     * 获取所有需要跑的任务
     * @return array
     */
    public function getAllRecords(){
        $dataTaskRecords = new Data_Task_Records();
        $dataTaskRecords->type = $this->_type;
        $dataTaskRecords->lifeTime = time();
        $dataTaskRecords->state = 0;        
        return Model_Task_Records::getNeedRunRecord($dataTaskRecords);
    }
    
    /**
     * 获取需要跑的一条记录
     * @return System_Task
     */
    public function getWaitingRecord(){
        $dataTaskRecords = new Data_Task_Records();
        $dataTaskRecords->type = $this->_type;
        $dataTaskRecords->lifeTime = time();
        $dataTaskRecords->state = 0;
        $limit =1;
        $record = Model_Task_Records::getNeedRunRecord($dataTaskRecords, $limit);
        $this->_record = $record;
        if (isset($record['id'])){
            $dataUpdateRecords = new Data_Task_Records();
            $dataUpdateRecords->id = $record['id'];
            $dataUpdateRecords->lastStartTime = time();
            $dataUpdateRecords->state = 1;
            Model_Task_Records::updateRecords($dataUpdateRecords);
        }
        return $this;
    }
    
    /**
     * 执行跑一次的任务
     * @param int $id
     */
    public function runOne($id){
        $this->validTask($id);
        if($this->_record){
            $this->run();
        }
    }
    
    /**
     * 重置任务
     * @param int $id
     */
    public function resetTask($id){
        return Model_Task_Records::resetTask($id);
    }
    
    /**
     * 查看指定任务id是否可跑
     * @param int $id
     */
    public function validTask($id){
    	$reBool = false;    	
    	$record = Model_Task_Records::getRecordsById($id);
    	if (isset($record['id']) && $record['type'] == $this->_type && $record['state'] == '0') {
    		$life = $record['last_start_time'] + $record['life_time'];
    		if ($life < time()) {
    		    $dataTaskRecords = new Data_Task_Records();
    		    $dataTaskRecords->id = $record['id'];
    		    $dataTaskRecords->lastStartTime = time();
    		    $dataTaskRecords->state = 1;
    			Model_Task_Records::updateRecords($dataTaskRecords);
    			$this->_record = $record;
    			$reBool = true;
    		}
    	}
    	return $reBool;
    }    
    
    /**
     * 通过key获取task记录
     * @param string $taskKey
     * @param string $lock
     * @return System_Task
     */
    public function getRecordByKey($taskKey, $lock = false){
        $dataTaskRecords = new Data_Task_Records();
        $dataTaskRecords->type = $this->_type;
        $dataTaskRecords->taskKey = $taskKey;
        $record = Model_Task_Records::getRecordsByTaskKey($dataTaskRecords);
        $this->_record = $record;
        return $this;
    }    
       
}