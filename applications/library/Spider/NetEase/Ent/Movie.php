<?php
/**
 * 
 * @author keven.zhong
 *
 */

class Spider_NetEase_Ent_Movie extends Spider_NetEase_Base{
      
    private $_preLink = 'http://ent.163.com/movie/';
    
    public function __construct($detail = array() ){
        $this->_detail = $detail;
    }
    
    public function spider(){
        $startLink = $this->_preLink;
        $data = new Data_Spider_Netease_Base();
        $data->class = __CLASS__;
        $data->link = $startLink;
        $data->filterKeyWorld = 'div[class=focusContent],div[class=newInfo],div[class=box_pf]';
        $this->getIndexLinks($data);
        return true;
    }
}