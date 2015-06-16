<?php
/**
 * 
 * @author keven.zhong
 *
 */
abstract class Model_Base {
    
    /**
     * 通过类获取的updata sql
     * @param obj $bindArrParams
     * @return array $rs
     */
    public static function getUpdateVal (&$bindArrParams) {
        $rs = array();
        $clsName = get_class($bindArrParams);
        if($clsName != ''){
            $allVars = get_class_vars($clsName);
            $aryRs = Array();
            $aryBind = Array();
            foreach ($allVars as $key => $val) {
                if (NULL !== $bindArrParams->$key) {
                    $aryRs[] = " `$key` = :$key";
                    $aryBind[':' . $key] = $bindArrParams->$key;
                }
            }
            $rs = Array($aryRs , $aryBind);
        }        
        return $rs;
    }
    
    /**
     * 通过类获取的insert sql
     * @param obj $bindArrParams
     * @return array $rs
     */
    public static function getInsertVal (&$bindArrParams) {
        $rs = array();
        $clsName = get_class($bindArrParams);
        if($clsName != ''){
            $allVars = get_class_vars($clsName);
            $aryRsKey = Array();
            $aryRsValue = Array();
            $aryBind = Array();
            foreach ($allVars as $key => $val) {
                if (NULL !== $bindArrParams->$key) {
                    $aryRsKey[] = '`' . $key . '`';
                    $aryRsValue[] = ':' . $key;
                    $aryBind[':' . $key] = $bindArrParams->$key;
                }
            }
            $rs = Array($aryRsKey , $aryRsValue, $aryBind);
        }
        return $rs;
    }
    
    /**
     * 过滤需要的sql
     * @param obj $bindArrParams
     * @return array $rs
     */
    public static function getSelectVal(&$bindArrParams){
        $rs = array();
        $clsName = get_class($bindArrParams);
        if($clsName != ''){
            $allVars = get_class_vars($clsName);
            $aryRs = Array();
            $aryBind = Array();
            foreach ($allVars as $key => $val) {
                if (NULL !== $bindArrParams->$key) {
                    $aryRs[] = " `$key` = :$key";
                    $aryBind[':' . $key] = $bindArrParams->$key;
                }
            }
            $rs = Array(implode(' AND ',$aryRs) , $aryBind);
        }        
        return $rs;
    }
    
}