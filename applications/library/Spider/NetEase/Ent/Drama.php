<?php
/**
 * 
 * @author keven.zhong
 *
 */

class Spider_NetEase_Ent_Drama extends Spider_NetEase_Base{
      
    private $_preLink = 'http://ent.163.com/drama/';
    
    public function __construct($detail = array() ){
        $this->_detail = $detail;
    }
    
    public function spider(){
        $startLink = $this->_preLink;
        $data = new Data_Spider_Netease_Base();
        $data->class = __CLASS__;
        $data->link = $startLink;
        $data->filterKeyWorld = 'div[class=focus-panel],div[class=w-img],div[class=bigsize],
                div[class=m-list],div[class=w-tab]';
        $this->getIndexLinks($data);
        return true;
    }
}