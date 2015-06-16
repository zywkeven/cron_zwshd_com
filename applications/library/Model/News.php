<?php
/**
 * 
 * @author keven.zhong
 *
 */
class Model_News extends Model_Base{
    
    private static $_table = 'zwshd_news';
    
    public static function addRecord(Data_News_Record $data){
        $rs = false;
        $InsertStr = parent::getInsertVal($data);
        if(count($InsertStr) > 1){
            $InsertKey = implode(',', $InsertStr[0]);
            $InsertValue = implode(',', $InsertStr[1]);
            $bind = $InsertStr[2];
            if ($InsertKey != ''){
                $local = Core_Pdo::factory('local');
                $sql = 'INSERT INTO '.self::$_table.'(' . $InsertKey . ')VALUES('.$InsertValue.')';
                $dbResult = $local->simplePrepare($sql, $bind);
                if(!$dbResult->isEmpty()){
                    $rs = $dbResult->data;
                }
            }
        }
        return $rs;
        
    }
    
    public static function updateRecord(Data_News_Record $data){
        $rs = false;
        $updateStr = parent::getUpdateVal($data);
        if(count($updateStr) > 1){
            $updateString = implode(',', $updateStr[0]);
            $bind = $updateStr[1];
            if ($updateString != ''){
                $local = Core_Pdo::factory('local');
                $sql = 'UPDATE '.self::$_table.' SET ' . $updateString . ' WHERE id = :bindId';
                $bind[':bindId'] = $data->id;
                $dbResult = $local->simplePrepare($sql, $bind);
                if(!$dbResult->isEmpty()){
                    $rs = $dbResult->data;
                }
            }
        }
        return $rs;
    }
    
    public static function getRecord(Data_News_Record $data, $field = 'id'){
        $rs = array();
        $local = Core_Pdo::factory('slave');
        $sql = 'SELECT '.$field.' FROM '.self::$_table.' WHERE is_deleted = 0';
        $selectStr = parent::getSelectVal($data);
        $sql .= ' AND '. $selectStr[0];
        $bind = $selectStr[1];
        $dbResult = $local->rowPrepare($sql, $bind);
        if(!$dbResult->isEmpty()){
            $rs = $dbResult->data;
        }
        return $rs;
    }
    
    public static function getAllRecord(Data_News_Record $data, $field = 'id'){
        $rs = array();
        $local = Core_Pdo::factory('slave');
        $sql = 'SELECT '.$field.' FROM '.self::$_table.' WHERE is_deleted = 0';
        $selectStr = parent::getSelectVal($data);
        $sql .= ' AND '. $selectStr[0];
        $bind = $selectStr[1];
        $dbResult = $local->allPrepare($sql, $bind);
        if(!$dbResult->isEmpty()){
            $rs = $dbResult->data;
        }
        return $rs;
    }
    
}