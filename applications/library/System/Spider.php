<?php
/**
 * 爬一下网站
 * @author keven.zhong
 * @Version 1.0 At 2014-02-04
 */
class System_Spider {
    
    /**
     * @param number $id
     */
    public function index($id = 0, $param = null) {
        $data = new Data_Source_Query();
        $data->id = $id;
        $data->fields = ' `id`,`key`,`name` ';
        $rs = Model_Source::getOneRecord($data);
        $composite = new Component_Composite();
        if(!empty($rs)){
            $className = 'Component_Leaf_'.$rs['key'];
            if(class_exists($className)){
                $leaf = new $className($rs);
                $composite->add($leaf);
            }
        }
        $composite->operation($param);
        return true;
    }
    
}