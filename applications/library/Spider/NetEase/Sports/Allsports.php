<?php
/**
 * 
 * @author keven.zhong
 *
 */

class Spider_NetEase_Sports_Allsports extends Spider_NetEase_Base{

    private $_preLink = 'http://sports.163.com/allsports/';
    
    public function __construct($detail = array() ){
        $this->_detail = $detail;
    }
    
    public function spider(){        
        $startLink = $this->_preLink;
        $data = new Data_Spider_Netease_Base();
        $data->class = __CLASS__;
        $data->link = $startLink;
        $data->filterKeyWorld = 'div[class=col_l]';
        $this->getIndexLinks($data);
        return true;
    }

}