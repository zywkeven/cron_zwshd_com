<?php
/**
 * 分类处理
 * @author keven.zhong
 *
 */

class App_Category {
    
    private static $category = null; 
    /**
     * 
     * @param Data_Category_Query $data
     * @return Ambigous <NULL, multitype:, mixed, unknown>
     */
    public static function getCategory(){
        if(is_null(self::$category)){
            $category = Model_Category::getAllCategory();
            self::$category = self::transformRelation($category);
        }
        return self::$category;
    }
    
    public static function transformRelation($category){
        $rs = Array();
        foreach ($category as $records){
            if($records['p_id'] == 0){
                $rs[$records['id']]['key'] = $records['key'];
                $rs[$records['id']]['name'] = $records['name'];
            }else {
                $rs[$records['p_id']]['detail'][] = array(
                        'id' => $records['id'],
                        'key' => $records['key'],
                        'name' => $records['name']
                );
            }
        }
        return $rs;
    }
    
    public static function transformLink($category){
        $rs = Array();
        foreach ($category as $parent){
            foreach ($parent['detail'] as $sub){
                $rs[] = 'http://www.zwshd.com/'. $parent['key'].'/'.$sub['key'];
            }
        }
        return $rs;
    }
    
    public static function getCategoryLink(){
        $rs = array();
        if(is_null(self::$category)){
            $category = Model_Category::getAllCategory();
            self::$category = self::transformRelation($category);
        }
        $category = self::$category;
        foreach ($category as $parent){
            foreach ($parent['detail'] as $sub){
                $dataNews = new Data_News_Record();
                $dataNews->category_id = $sub['id'];
                $dataNews->date = date('Y-m-d');
                $news = Model_News::getAllRecord($dataNews);
                foreach($news as $eachNews){
                    $rs[] = 'http://www.zwshd.com/'. $parent['key'].'/'.$sub['key'] .'/' .App_Des::encrypt($eachNews['id']).'.html';
                }
            }
        }
        return $rs;
    }
    
}