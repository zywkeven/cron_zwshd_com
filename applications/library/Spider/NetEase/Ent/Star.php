<?php
/**
 * 
 * @author keven.zhong
 *
 */

class Spider_NetEase_Ent_Star extends Spider_NetEase_Base{
      
    private $_preLink = 'http://ent.163.com/star/';
    
    public function __construct($detail = array() ){
        $this->_detail = $detail;
    }
    
    public function spider(){
        $startLink = $this->_preLink;
        $data = new Data_Spider_Netease_Base();
        $data->class = __CLASS__;
        $data->link = $startLink;
        $data->filterKeyWorld = 'div[class=focus_panel],div[class=focus_small],div[class=item-Text]';
        $this->getIndexLinks($data);
        return true;
    }
}