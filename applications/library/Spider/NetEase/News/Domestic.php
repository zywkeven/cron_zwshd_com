<?php
/**
 * 
 * @author keven.zhong
 *
 */

class Spider_NetEase_News_Domestic extends Spider_NetEase_Base{

    private $_preLink = 'http://news.163.com/domestic/';
    
    public function __construct($detail = array() ){
        $this->_detail = $detail;
    }
    
    public function spider(){
        $startLink = $this->_preLink;
        $data = new Data_Spider_Netease_Base();
        $data->class = __CLASS__;
        $data->link = $startLink;
        $data->filterKeyWorld = 'div[class=bigpic],div[class=list-item],div[class=item-top],ul[class=main-list]';
        $this->getIndexLinks($data);
        return true;
    }
}