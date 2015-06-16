<?php
/**
 * 计划任务类
 * @author Keven.Zhong
 * @Version： 1.0 At 2014-04-15
 */
class Model_Task_Records {

    protected static $_tableName = 'zwshd_task_records';
    /**
     * 获取指定需要更新的记录
     * @param Data_Task_Records $data
     * @param int $limit 数据数量
     * @return array $rs
     */
    public static function getNeedRunRecord(Data_Task_Records $data, $limit = null){
        $rs = Array();
        $local = Core_Pdo::factory('slave');
        $sql = 'SELECT * FROM '.self::$_tableName.' WHERE `type` =:type AND last_start_time + life_time <= :time
                AND `state` = :state AND is_deleted=:isDeleted ORDER BY `life_time` ASC,id ASC';
        $bind = array(
            ':type' => $data->type,
            ':state' => $data->state,
            ':time' => time(),
            ':isDeleted' => $data->isDeleted,
        );
        if((!is_null($limit)) && ($limit > 0) ){
            $sql .= ' LIMIT ' . intval($limit);
        }
        $dbResult = $local->rowPrepare($sql,$bind);
        if(!$dbResult->isEmpty() && is_array($dbResult->data)) {
            $rs = $dbResult->data;
        }
        return $rs;
    }

    /**
     * id获取任务记录
     * @param int $id
     * @param string $field
     * @return array $rs
     */
    public static function getRecordsById($id, $field = '*'){
        $rs = Array();
        $local = Core_Pdo::factory('slave');
        $sql = 'SELECT '. $field .' FROM '.self::$_tableName.' WHERE id = :id';
        $bind = array(
                ':id' => $id,
        );
        $dbResult = $local->rowPrepare($sql, $bind);
        if(!$dbResult->isEmpty() && is_array($dbResult->data)) {
            $rs = $dbResult->data;
        }
        return $rs;
    }

    /**
     * 更新指定记录
     * @param Data_Task_Records $data
     * @return boolean $rs
     */
    public static function updateRecords(Data_Task_Records $data){
        $rs = false;
        $local = Core_Pdo::factory('slave');
        $arySet = Array();
        $bind = Array('id' => $data->id);
        if (!is_null($data->state)){
            $arySet[] = 'state = :state';
            $bind[':state'] = $data->state;
        }
        if ($data->lastStartTime > 0){
            $arySet[] = 'last_start_time = :lastStartTime';
            $bind[':lastStartTime'] = $data->lastStartTime;
        }
        if ($data->lastEndTime > 0){
            $arySet[] = 'last_end_time = :lastEndTime';
            $bind[':lastEndTime'] = $data->lastEndTime;
        }
        if (!is_null($data->lastRunTime) && ( $data->lastRunTime>= 0)){
            $arySet[] = 'last_run_time = :lastRunTime';
            $bind[':lastRunTime'] = $data->lastRunTime;
        }
        if ($data->msg != ''){
            $arySet[] = 'msg = :msg';
            $bind[':msg'] = $data->msg;
        }

        $strSet = implode(',', $arySet);
        if ($strSet != ''){
            $sql = 'UPDATE '.self::$_tableName.' SET '.$strSet.' WHERE id = :id';
            $dbResult = $local->simplePrepare($sql, $bind);
            if(!$dbResult->isEmpty()) {
                $rs = $dbResult->data;
            }
        }
        return $rs;
    }
    
    
    /**
     * 更改任务内容
     * @param Array $data
     * @return boolean $rs;
     */
    public static function modifyTask($data,$id){
        $returnval=array('status'=>false,'msg'=>'');
        if(!$data || !$id){
            $returnval['msg']='参数丢失';
            return $returnval;
        }
        $local = Core_Pdo::factory('local');
        $arySet = Array();
        $bind = Array(':id' => $id);
        foreach($data as $key=>$val){
            if(strlen(trim($val))){
                $arySet[]=$key.'=:'.$key;
                $bind[':'.$key]=$val;
            }
        }
        if(!$arySet){
            $returnval['msg']='没有数据';
            return $returnval;
        }
        $strSet = implode(',', $arySet);
        if ($strSet != ''){
            $sql = 'UPDATE '.self::$_tableName.' SET '.$strSet.' WHERE id = :id';
            $dbResult = $local->simplePrepare($sql, $bind);
            if($dbResult->isEmpty()){
                $returnval['msg']='更新失败,请检查字段值与类型是否正确';
                return $returnval;

            }else{
                $returnval['status']=true;
                return $returnval;
            }
        }
        $returnval['msg']='未知错误';
        return $returnval;
    }

    /**
     * 添加任务内容
     * @param Array $data
     * @return boolean $rs;
     */
    public static function addTask($data){
        $returnval=array('status'=>false,'msg'=>'');
        if(!$data){
            $returnval['msg']='参数丢失';
            return $returnval;
        }
        
        $local = Core_Pdo::factory('local');
        $arySet = Array();
        foreach($data as $key=>$val){
            if(strlen(trim($val))){
                $arySet[]=$key.'=:'.$key;
                $bind[':'.$key]=$val;
            }
        }
        if(!$arySet){
            $returnval['msg']='没有数据';
            return $returnval;
        }
        $strSet = implode(',', $arySet);
        if ($strSet != ''){
            $sql = 'INSERT INTO  '.self::$_tableName.' SET '.$strSet;
            $dbResult = $local->simplePrepare($sql, $bind);
            
            if($dbResult->isEmpty()){
                
               $returnval['msg']='更新失败,请检查字段值与类型是否正确';
                return $returnval;
            }else{
                $returnval['status']=true;
                return $returnval;
            }
        }
        $returnval['msg']='未知错误';
        return $returnval;
    }
    
    /**
     * 更新记录状态
     * @param Data_Task_Records $data
     * @return boolean $rs;
     */
    public static function fixRecords(Data_Task_Records $data){
        $rs = false;
        $local = Core_Pdo::factory('local');
        $sql = 'UPDATE '.self::$_tableName.' SET state= :state ,msg=:msg  WHERE
                `state` = 1 AND last_start_time < :lastStartTime';
        $bind = array(
                ':state' => $data->state,
                ':msg' => $data->msg,
                ':lastStartTime' => $data->lastStartTime,
        );
        $dbResult = $local->simplePrepare($sql, $bind);
        if(!$dbResult->isEmpty()) {
            $rs = $dbResult->data;
        }
        return $rs;
    }
    
    /**
     * 根据类，方法记录状态
     * @param Data_Task_Records $data
     * @return boolean $rs;
     */
    public static function getRecordByMethod($class, $method, $field = '*'){
        $rs = Array();
        $local = Core_Pdo::factory('slave');
        $sql = 'SELECT '.$field.' FROM '.self::$_tableName.' WHERE class=:class AND method = :method';
        $bind = array(
                ':class' => $class,
                ':method' => $method,
        );
        $dbResult = $local->rowPrepare($sql, $bind);
        if(!$dbResult->isEmpty() && is_array($dbResult->data)) {
            $rs = $dbResult->data;
        }
        return $rs;
    }
    
    public static function getOneRecord(Data_Task_Query $data){
        $rs = Array();
        $local = Core_Pdo::factory('slave');
        $sql = 'SELECT '.$data->fields.' FROM '.self::$_tableName.' WHERE 1=1 ';
        $bind = array();
        if(!is_null($data->taskKey)){
            $sql .= ' AND task_key= :taskKey';
            $bind[':taskKey'] = $data->taskKey;
        }
        if(!is_null($data->class)){
            $sql .= ' AND class= :class';
            $bind[':class'] = $data->class;
        }
        if(!is_null($data->method)){
            $sql .= ' AND method= :method';
            $bind[':method'] = $data->method;
        }
        if(!is_null($data->sourceId)){
            $sql .= ' AND source_id= :sourceId';
            $bind[':sourceId'] = $data->sourceId;
        }
        if(!is_null($data->id)){
            $sql .= ' AND id= :id';
            $bind[':id'] = $data->id;
        }
        if(!is_null($data->neqId)){
            $sql .= ' AND id != :neqId';
            $bind[':neqId'] = $data->neqId;
        }
        if(!is_null($data->isDeleted)){
            $sql .= ' AND is_deleted = :isDeleted';
            $bind[':isDeleted'] = $data->isDeleted;
        }
        $dbResult = $local->rowPrepare($sql, $bind);
        if(!$dbResult->isEmpty() && is_array($dbResult->data)) {
            $rs = $dbResult->data;
        }
        return $rs;
    }

    /**
     * 通过类跟方法获取记录
     * @param Data_Task_Records $data
     * @return multitype:
     */
    public static function getRecordsByTaskKey(Data_Task_Records $data){
        $rs = Array();
        $local = Core_Pdo::factory('slave');
        $sql = 'SELECT * FROM '.self::$_tableName.' WHERE type=:type AND task_key = :task_key';
        $bind = array(
                ':type' => $data->type,
                ':task_key' => $data->taskKey,
        );
        $dbResult = $local->rowPrepare($sql, $bind);
        if(!$dbResult->isEmpty() && is_array($dbResult->data)) {
            $rs = $dbResult->data;
        }
        return $rs;
    }
    
    /**
     * 获取所有记录
     * @param string $field
     * @return multitype:
     */
    public static function getAllRecords($field = '*',$where=''){
        $rs = Array();
        $local = Core_Pdo::factory('slave');
        $sql = 'SELECT '.$field.' FROM '.self::$_tableName;
        if($where){
            if(is_array($where)){
                $sql.=" WHERE ".implode(' AND ',$where);    
            }elseif(is_string($where)){
                $sql.=" WHERE ".$where;
            }
        }
        $dbResult = $local->allPrepare($sql);
        if(!$dbResult->isEmpty() && is_array($dbResult->data)) {
            $rs = $dbResult->data;
        }
        return $rs;
    }
    
    /**
     * 重置任务
     * @param int $id
     */
    public static function resetTask($id){
        $rs = false;
        $local = Core_Pdo::factory('local');
        $sql = 'UPDATE '.self::$_tableName.' SET state= :state ,last_start_time=:lastStartTime  WHERE
                `id` = :id';
        $bind = array(
                ':state' => 0,                
                ':lastStartTime' => 0,
                ':id' => $id,
        );
        $dbResult = $local->simplePrepare($sql, $bind);
        if(!$dbResult->isEmpty()) {
            $rs = $dbResult->data;
        }
        return $rs;
    }
    
    /**
     * 清空指定记录的message
     * @param integer $id
     * @return boolean $rs
     */
    public static function clearMsg($id){
        $rs = false;
        $local = Core_Pdo::factory('local');
        $sql = 'UPDATE '.self::$_tableName.' SET msg = ""  WHERE
                `id` = :id';
        $bind = array(               
                ':id' => $id,
        );
        $dbResult = $local->simplePrepare($sql, $bind);
        if(!$dbResult->isEmpty()) {
            $rs = $dbResult->data;
        }
        return $rs;
    }
    
    /**
     * 获取多个记录
     * @param Data_Task_Query $data
     * @return multitype:
     */
    public static function getQueryRecords(Data_Task_Query $data){
        $rs = Array();
        $local = Core_Pdo::factory('slave');
        $sql = 'SELECT '.$data->fields.' FROM '.self::$_tableName;
        $param = Array();
        if(!is_null($data->isDeleted)){
            $sql .= ' AND is_deleted = :isDeleted';
            $param[':isDeleted'] = $data->isDeleted;
        }
        if(!is_null($data->order) ){
            if($data->order != '') {
                $sql .= ' ORDER BY '. $data->order;
            }
        }
        $dbResult = $local->allPrepare($sql, $param);
        if(!$dbResult->isEmpty() && is_array($dbResult->data)) {
            $rs = $dbResult->data;
        }
        return $rs;
    }
    
    /**
     * 更新相应记录
     * @param Data_Task_Query $data
     * @return boolean
     */
    public static function updataRecords(Data_Task_Query $data) {
        $rs = false;
        $sql = 'UPDATE '.self::$_tableName.' SET id =id ';
        $param = Array();
        if(!is_null($data->isDeleted)){
            $sql .= ' ,is_deleted = :isDeleted';
            $param[':isDeleted'] = $data->isDeleted;
        }
        if(!is_null($data->lastStartTime)){
            $sql .= ' ,last_start_time = :lastStartTime';
            $param[':lastStartTime'] = $data->lastStartTime;
        }
        if(!is_null($data->type)){
            $sql .= ' ,type = :type';
            $param[':type'] = $data->type;
        }
        if(!is_null($data->taskKey)){
            $sql .= ' ,task_key = :taskKey';
            $param[':taskKey'] = $data->taskKey;
        }
        if(!is_null($data->class)){
            $sql .= ' ,class = :class';
            $param[':class'] = $data->class;
        }
        if(!is_null($data->method)){
            $sql .= ' ,method = :method';
            $param[':method'] = $data->method;
        }
        if(!is_null($data->sourceId)){
            $sql .= ' ,source_id = :sourceId';
            $param[':sourceId'] = $data->sourceId;
        }
        if(!is_null($data->lifeTime)){
            $sql .= ' ,life_time = :lifeTime';
            $param[':lifeTime'] = $data->lifeTime;
        }
        if(!is_null($data->remark)){
            $sql .= ' ,remark = :remark';
            $param[':remark'] = $data->remark;
        }
        $sql .= ' WHERE 1=1 ';
        if(!is_null($data->inIds)){
            $sql .= '  AND id IN ('.$data->inIds.')';
        }
        if(!is_null($data->id)){
            $sql .= '  AND id  = :id';
            $param[':id'] = $data->id;
        }
        $local = Core_Pdo::factory('local');
        $dbResult = $local->simplePrepare($sql, $param);
        if($dbResult->data == 1){
            $rs = true;
        }
        return $rs;        
    }
    
    /**
     * 添加记录
     * @param Data_Task_Query $data
     * @return boolean
     */
    public static function addRecord(Data_Task_Query $data) {
        $rs = false;
        $aryField = Array();
        $aryValues = Array();
        $param = Array();
        if(!is_null($data->isDeleted)){
            $aryField[] = 'is_deleted';
            $aryValues[] = ':isDeleted';
            $param[':isDeleted'] = $data->isDeleted;
        }
        if(!is_null($data->type)){
            $aryField[] = 'type';
            $aryValues[] = ':type';
            $param[':type'] = $data->type;
        }
        if(!is_null($data->taskKey)){
            $aryField[] = 'task_key';
            $aryValues[] = ':taskKey';
            $param[':taskKey'] = $data->taskKey;
        }
        if(!is_null($data->class)){
            $aryField[] = 'class';
            $aryValues[] = ':class';
            $param[':class'] = $data->class;
        }
        if(!is_null($data->method)){
            $aryField[] = 'method';
            $aryValues[] = ':method';
            $param[':method'] = $data->method;
        }
        if(!is_null($data->rosurceId)){
            $aryField[] = 'resource_id';
            $aryValues[] = ':rosurceId';
            $param[':rosurceId'] = $data->rosurceId;
        }
        if(!is_null($data->lifeTime)){
            $aryField[] = 'life_time';
            $aryValues[] = ':lifeTime';
            $param[':lifeTime'] = $data->lifeTime;
        }
        if(!is_null($data->remark)){
            $aryField[] = 'remark';
            $aryValues[] = ':remark';
            $param[':remark'] = $data->remark;
        }
        if(!is_null($data->createTime)){
            $aryField[] = 'create_time';
            $aryValues[] = ':createTime';
            $param[':createTime'] = $data->createTime;
        }        
        if(!empty($aryField)){
            $sql = 'INSERT INTO '.self::$_tableName.' ('.implode(',', $aryField).') 
                    VALUES('.implode(',', $aryValues).')';
        }
        $local = Core_Pdo::factory('local');
        $dbResult = $local->simplePrepare($sql, $param);
        if($dbResult->data == 1){
            $rs = true;
        }
        return $rs;
    }

}