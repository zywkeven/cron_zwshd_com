<?php
/**
 * 网站来源
 * @author Keven.Zhong
 * @Version 1.0 At 2014-04-15
 */
class Model_Source {

    protected static $_tableName = 'zwshd_source';
    
    public static function getOneRecord(Data_Source_Query $data){
        $rs = Array();
        $local = Core_Pdo::factory('slave');
        $sql = 'SELECT '.$data->fields.' FROM '.self::$_tableName.' WHERE 1=1 AND is_deleted=0 ';
        $bind = array();
        if(!is_null($data->id)){
            $sql .= ' AND id= :id';
            $bind[':id'] = $data->id;
        }        
        $dbResult = $local->rowPrepare($sql, $bind);
        if(!$dbResult->isEmpty() && is_array($dbResult->data)) {
            $rs = $dbResult->data;
        }
        return $rs;
    }


}