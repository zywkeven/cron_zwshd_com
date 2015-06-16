<?php
/**
 * 网易叶子处理
 * @author keven.zhong
 * @Version 1.0 At 2015-02-03
 */
class Component_Leaf_NetEase implements Component_Component {
    
    private $_detail;
    
    private $_composite;
    
    public function __construct($detail) {
        $this->_detail = $detail;
    }
    
    public function getComposite() {
        return  $this->_composite;
    }
    
    public function setComposite(Component_Composite $data){
        $this->_composite = $data;
    }
    
    public function operation($param = null) {
        $detail = $this->_detail;
        $cat = App_Category::getCategory();
        foreach($cat as $parents){
            if(isset($parents['detail']) && is_array($parents['detail'])){
                foreach($parents['detail'] as $subCat){
                    $detail['subCat']['key'] = $subCat['key'];
                    $detail['subCat']['id'] = $subCat['id'];
                    $className = 'Spider_'. ucwords($detail['key']). '_' . ucwords($parents['key']).'_'.ucwords($subCat['key']);
                    App_Common::addLog('SPIDER_START:'.$className);
                    $startTime = time();
                    if(class_exists($className)){
                        $cls = new $className($detail);
                        $cls->spider();
                    }
                    App_Common::addLog('SPIDER_END  :'.$className .',time:['.(time()- $startTime).']');
                }
            }
        }
        return true;
    }
    
}
