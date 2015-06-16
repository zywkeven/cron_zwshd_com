<?php
/**
 * 
 * @author keven.zhong
 *
 */

class Spider_NetEase_Auto_Car extends Spider_NetEase_Base{
      
    private $_preLink = 'http://auto.163.com/';
    
    public function __construct($detail = array() ){
        $this->_detail = $detail;
    }
    
    public function spider(){
        $startLink = $this->_preLink;
        $data = new Data_Spider_Netease_Base();
        $data->class = __CLASS__;
        $data->link = $startLink;
        $data->filterKeyWorld = 'div[class=am_bd]';
        $this->getIndexLinks($data);
        return true;
    }
}