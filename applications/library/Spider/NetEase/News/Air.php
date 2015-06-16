<?php
/**
 * 
 * @author keven.zhong
 *
 */

class Spider_NetEase_News_Air extends Spider_NetEase_Base{
    
    private $_preLink = 'http://news.163.com/air/';
    
    public function __construct($detail = array() ){
        $this->_detail = $detail;
    }
    
    public function spider(){
        $startLink = $this->_preLink;
        $data = new Data_Spider_Netease_Base();
        $data->class = __CLASS__;
        $data->link = $startLink;
        $data->filterKeyWorld = 'div[class=focus_panel_text],div[class=top_news_list],div[class=news-list-box],ul[class=mod-list]';
        $this->getIndexLinks($data);
        return true;
    }
}