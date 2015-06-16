<?php
/**
 * 
 * @author keven.zhong
 *
 */

class Spider_NetEase_Ent_Music extends Spider_NetEase_Base{
      
    private $_preLink = 'http://ent.163.com/music/';
    
    public function __construct($detail = array() ){
        $this->_detail = $detail;
    }
    
    public function spider(){
        $startLink = $this->_preLink;
        $data = new Data_Spider_Netease_Base();
        $data->class = __CLASS__;
        $data->link = $startLink;
        $data->filterKeyWorld = 'div[class=focusLeft],div[class=focusImg],div[class=collm_bg]';
        $this->getIndexLinks($data);
        return true;
    }
}