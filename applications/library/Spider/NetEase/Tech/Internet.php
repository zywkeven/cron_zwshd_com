<?php
/**
 * 
 * @author keven.zhong
 *
 */

class Spider_NetEase_Tech_Internet extends Spider_NetEase_Base{
      
    private $_preLink = 'http://tech.163.com/internet/';
    
    public function __construct($detail = array() ){
        $this->_detail = $detail;
    }
    
    public function spider(){
        $startLink = $this->_preLink;
        $data = new Data_Spider_Netease_Base();
        $data->class = __CLASS__;
        $data->link = $startLink;
        $data->filterKeyWorld = 'ul[class=newsList]';
        $this->getIndexLinks($data);
        return true;
    }
}