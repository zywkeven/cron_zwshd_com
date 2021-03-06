<?php
/**
 * 
 * @author keven.zhong
 *
 */

class Spider_NetEase_Tech_Cnstock extends Spider_NetEase_Base{
      
    private $_preLink = 'http://tech.163.com/cnstock';
    
    public function __construct($detail = array() ){
        $this->_detail = $detail;
    }
    
    public function spider(){
        $startLink = $this->_preLink;
        $data = new Data_Spider_Netease_Base();
        $data->class = __CLASS__;
        $data->link = $startLink;
        $data->filterKeyWorld = 'div[class=tList14px],div[class=content]';
        $this->getIndexLinks($data);
        return true;
    }
}