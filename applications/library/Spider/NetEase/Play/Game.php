<?php
/**
 * 
 * @author keven.zhong
 *
 */

class Spider_NetEase_Play_Game extends Spider_NetEase_Base{
      
    private $_preLink = 'http://play.163.com/';
    
    public function __construct($detail = array() ){
        $this->_detail = $detail;
    }
    
    public function spider(){
        $startLink = $this->_preLink;
        $data = new Data_Spider_Netease_Base();
        $data->class = __CLASS__;
        $data->link = $startLink;
        $data->defaultCode = 'GBK';
        $data->filterKeyWorld = 'div[class=m-topFocus],div[class=content]';
        $this->getIndexLinks($data);
        return true;
    }
}