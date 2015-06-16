<?php
/**
 * 
 * @author keven.zhong
 *
 */

class Spider_NetEase_Tech_Mobile extends Spider_NetEase_Base{
      
    private $_preLink = 'http://mobile.163.com/';
    
    public function __construct($detail = array() ){
        $this->_detail = $detail;
    }
    
    public function spider(){
        $startLink = $this->_preLink;
        $data = new Data_Spider_Netease_Base();
        $data->class = __CLASS__;
        $data->link = $startLink;
        $data->filterKeyWorld = 'div[class=grid-u-9]';
        $this->getIndexLinks($data);
        return true;
    }
}