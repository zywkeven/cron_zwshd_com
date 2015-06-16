<?php
/**
 * 爬虫公用类
 * @author keven.zhong
 *
 */
class Spider_NetEase_Base{
    
    public $_detail = array();
    
    public function getIndexLinks(Data_Spider_Netease_Base $data){
        $rs = array();
        $indexContent = App_Common::getUrlContent($data->link);
        $indexContent = App_Common::removeRes($indexContent);
        if(!$indexContent){
            $this->addError($data->class, 'index has no content');
            return $rs;
        }       
        $code = $this->getCode($indexContent, $data->defaultCode);
        $indexContent = $this->translate($indexContent, $code);
        $indexHtml = new Simple_Html_Dom();
        $indexHtml->load($indexContent);
        $section = $indexHtml->find($data->filterKeyWorld);
        if(empty($section)){
            $this->addError($data->class, 'section has no content');
            return $rs;
        }
        $weight = 1;
        $linkCount = 0;
        foreach($section as $part){
            $href = $part->find('a');
            if($href){
                foreach($href as $links){
                    $linkCount +=1;
                    if($links->href && $links->plaintext ) {
                        $linkTitle = $links->plaintext;
                        $linkHref = $links->href;
                        $content = App_Common::getUrlContent($linkHref);
                        $code = $this->getCode($content, $data->defaultCode);
                        $content = $this->translate($content, $code);
                        $detail = $this->_filterDetail($content);
                        $title  = $this->removeLabel($linkTitle);
                        if($detail && $title) {
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
                }
            }
        }
        if($linkCount === 0){
            $this->addError($data->class, 'part has no link');
        }
        $indexHtml->clear();
        unset($indexHtml);
        return $rs;
    }
    
    /**
     *
     * @param unknown $content
     */
    protected function _filterDetail(&$content){
        $content = App_Common::removeRes($content);
        $html = new Simple_Html_Dom();
        $html->load($content);
        $str = '';
        $aryDetail = $html->find('div[id=endText]');
        if($aryDetail && is_array($aryDetail)){
            foreach($aryDetail as $detail) {
                //过滤掉图片
                $ele = $detail->find('img');
                foreach($ele as $eachEle){
                    $eachEle->parent->outertext = '';
                }
                //过滤掉广告
                $ele = $detail->find('iframe');
                foreach($ele as $eachEle){
                    $eachEle->outertext = '';
                }
                //过滤掉相关链接
                $ele = $detail->find('a');
                foreach($ele as $eachEle){
                    $eachEle->outertext = $eachEle->plaintext;
                }
                //过滤掉视频
                $ele = $detail->find('div[class=video-wrapper]');
                foreach($ele as $eachEle){
                    $eachEle->outertext = $eachEle->plaintext;
                }
                //过滤房源
                $ele = $detail->find('div[class=post_vhouse_relation_box]');
                foreach($ele as $eachEle){
                    $eachEle->outertext = $eachEle->plaintext;
                }
                //过滤多媒体
                $ele = $detail->find('object');
                foreach($ele as $eachEle){
                    $eachEle->outertext = $eachEle->plaintext;
                }
                $str .= $detail->__toString();
            }
            //视频页面则不保存
            if($html->find('div[class=video-movie]')){
                $str = '';
            }
        }
        $aryDetail = $html->find('div[class=con_box]');
        if($aryDetail && is_array($aryDetail)){
            foreach($aryDetail as $detail) {
                //过滤掉图片
                $ele = $detail->find('img');
                foreach($ele as $eachEle){
                    $eachEle->parent->outertext = '';
                }
                //过滤掉广告
                $ele = $detail->find('iframe');
                foreach($ele as $eachEle){
                    $eachEle->outertext = '';
                }
                //过滤掉相关链接
                $ele = $detail->find('a');
                foreach($ele as $eachEle){
                    $eachEle->outertext = $eachEle->plaintext;
                }
                //过滤掉视频
                $ele = $detail->find('div[class=video-wrapper]');
                foreach($ele as $eachEle){
                    $eachEle->outertext = $eachEle->plaintext;
                }
                //过滤房源
                $ele = $detail->find('div[class=post_vhouse_relation_box]');
                foreach($ele as $eachEle){
                    $eachEle->outertext = $eachEle->plaintext;
                }
                //过滤多媒体
                $ele = $detail->find('object');
                foreach($ele as $eachEle){
                    $eachEle->outertext = $eachEle->plaintext;
                }
                $str .= $detail->__toString();
            }
            //视频页面则不保存
            if($html->find('div[class=video-movie]')){
                $str = '';
            }
        }
        $html->clear();
        unset($html);
        return $str;
    }
    /**
     * 记录错误信息
     * @param unknown $cls
     * @param unknown $msg
     */
    public function addError($cls, $msg){
        App_Common::addLog('Class:'.$cls.', ' . $msg, 'error');
    } 
    
    /**
     * 编码转换
     * @param unknown $str
     * @param unknown $code
     * @return string
     */
    public function translate(&$str, $default= 'UTF-8'){
        $code = strtoupper($default);
        if($code != 'UTF-8'){
            $gbkAry = array('GBK','GB2312');
            $inGbk = false;
            foreach($gbkAry as $eachVal){
                if(strpos($code, $eachVal) !== false){
                    $code = 'GBK';
                    $inGbk = true;
                }
            }
            if(!$inGbk){
                $code = $default;
            }
            $str = mb_convert_encoding($str, 'UTF-8', $code);
        }
        return $str;
    }
    
    /**
     * 获取编码
     * @param unknown $content
     * @return string
     */
    public function getCode(&$content, $defult = 'UTF-8'){
        if(is_null($defult)){
            $defult = 'UTF-8';
        }
        $pattern ='/<meta[^>]*?charset[\s]*=[\s"\']*([^>"]*)[^>]*?>/is';
        $codeStr = Array();
        preg_match($pattern, $content, $codeStr);
        $code = isset($codeStr[1]) && $codeStr[1] ? $codeStr[1] : $defult;
        return $code;
    }
    
    /**
     * 去除所有标签
     * @param unknown $str
     * @return mixed
     */
    public function removeLabel($str){
        $pattern ='/<\/[\w]+>|<[\w]+[^>]*?>/is';
        $str= preg_replace($pattern, '', $str);
        $aryRemove = Array('[详细]','[阅读更多]','详细&gt;&gt;');
        if(in_array($str, $aryRemove)){
            $str = '';
        }
        return $str;
    }
}