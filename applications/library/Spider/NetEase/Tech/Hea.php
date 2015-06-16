<?php
/**
 * 
 * @author keven.zhong
 *
 */

class Spider_NetEase_Tech_Hea extends Spider_NetEase_Base{
      
    private $_preLink = 'http://hea.163.com/';
    private $_partLink = 'http://hea.163.com/special/jiadian_data/';
    
    public function __construct($detail = array() ){
        $this->_detail = $detail;
    }
    
    public function spider(){
        $startLink = $this->_preLink;
        $data = new Data_Spider_Netease_Base();
        $data->class = __CLASS__;
        $data->link = $startLink;
        $data->filterKeyWorld = 'div[class=focus_panel]';
        $this->getIndexLinks($data);
       
        $data->link = $this->_partLink;
        $data->defaultCode = 'GBK';
        $data->filterKeyWorld = 'div[class=titleBar]';        
        $this->getIndexLinks($data);
        
        return true;
    }
}