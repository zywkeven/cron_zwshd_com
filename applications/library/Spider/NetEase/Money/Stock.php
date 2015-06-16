<?php
/**
 * 
 * @author keven.zhong
 *
 */

class Spider_NetEase_Money_Stock extends Spider_NetEase_Base{
      
    private $_preLink = 'http://money.163.com/stock/';
    
    public function __construct($detail = array() ){
        $this->_detail = $detail;
    }
    
    public function spider(){
        $startLink = $this->_preLink;
        $data = new Data_Spider_Netease_Base();
        $data->class = __CLASS__;
        $data->link = $startLink;
        $data->filterKeyWorld = 'div[class=news_importent],div[class=news_struct]';      
        $this->getIndexLinks($data);
        return true;
    }
}