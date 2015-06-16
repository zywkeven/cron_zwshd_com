<?php
/**
 * 
 * @author keven.zhong
 *
 */

class Spider_NetEase_News_Newsworld extends Spider_NetEase_Base{

    //private $_preLink = 'http://news.163.com/world/';
    private $_jsLink = 'http://news.163.com/special/00014RJU/nationalnews-json-data.js';
    
    public function __construct($detail = array() ){
        $this->_detail = $detail;
    }
    
    public function spider(){
        $startLink = $this->_jsLink;        
        $indexContent = App_Common::getUrlContent($startLink);
        $indexContent = $this->translate($indexContent, 'GBK');
        $pattern = '/\{.*?\}/is';
        preg_match_all($pattern, $indexContent, $aryMatch, PREG_SET_ORDER);
        $weight = 1;
        $links = 0;
        foreach($aryMatch as $eachMatch){
            $titlePattern = '/\"title\":\"([^\"]*?)\"/is';
            preg_match($titlePattern, $eachMatch[0], $aryTitle);
            $linkPattern = '/\"link\":\"([^\"]*?)\"/is';
            preg_match($linkPattern, $eachMatch[0], $aryLink);
            if($aryTitle && $aryLink ){
                $title = $this->removeLabel($aryTitle[1]);
                $linkHref = $aryLink[1];
                $links += 1;
                $content = App_Common::getUrlContent($linkHref);
                $code = $this->getCode($content, 'GBK');
                $content = $this->translate($content, $code);
                $detail = $this->_filterDetail($content);
                $record = new Data_News_Record();
                $record->crypt_title = md5($title);
                $newsRecord = Model_News::getRecord($record, 'id,crypt_detail,weight');
                $record->title = $title;
                $record->detail = $detail;
                $record->crypt_detail = md5($record->detail);
                $record->category_id = $this->_detail['subCat']['id'];
                $record->source_id = $this->_detail['id'];
                $record->source_name = $this->_detail['name'];
                $record->original = $linkHref;
                if(empty($newsRecord) ){
                    $record->date = date('Y-m-d');
                    $record->weight = $weight;
                    $record->create_time = date('Y-m-d H:i:s');
                    Model_News::addRecord($record);
                    $weight += 1;
                }else if ($record->crypt_detail != $newsRecord['crypt_detail']){
                    $record->id = $newsRecord['id'];
                    $weight = $newsRecord['weight'] + 1;
                    Model_News::updateRecord($record);
                }
            }
        }
        if($links === 0){
            $this->addError(__CLASS__, 'part has no link');
        }
        return true;
    }
}