<?php
/**
 * 首页
 * @author Keven.Zhong
 * @Version 1.0 At 2014-05-28
 */

require dirname(__FILE__).'/../init/init.php';
set_time_limit(0);
//分类
$category = App_Category::getCategory();
$catLink = App_Category::transformLink($category);
$sepLink = array_chunk ($catLink, 10);
$api = Model_Baidu::getInstance();
foreach($sepLink as $eachAry){
    $data = implode("\n", $eachAry);
    $rs = $api->getApi($data);
    App_Common::addLog('BAIDU SITE:'.$rs);
}

//新闻页面 有数量限制，暂时不推
/*
$newsLink = App_Category::getCategoryLink();print_r($newsLink);
$data = implode("\n", $catLink);
$rs = $api->getApi($data);
var_dump($rs);*/