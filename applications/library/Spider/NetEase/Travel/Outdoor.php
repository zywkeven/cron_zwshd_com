<?php
/**
 * 
 * @author keven.zhong
 *
 */

class Spider_NetEase_Travel_Outdoor extends Spider_NetEase_Base{
      
    private $_preLink = 'http://travel.163.com/special/outdoor163/';
    
    public function __construct($detail = array() ){
        $this->_detail = $detail;
    }
    
    public function spider(){
        $startLink = $this->_preLink;
        $data = new Data_Spider_Netease_Base();
        $data->class = __CLASS__;
        $data->link = $startLink;
        $data->filterKeyWorld = 'ul[class=main_news_list]';
        $this->getIndexLinks($data);
        return true;
    }
}