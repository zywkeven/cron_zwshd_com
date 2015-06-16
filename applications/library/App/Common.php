<?php
/**
 * 公用类
 * @author Keven.Zhong
 * @Version 1.0 At 2014-04-15
 */
class App_Common{
   
    private static $logger = null;
    /**
     * 返回缓存过期时间
     * 
     */
    public static function getCacheTime(
        $maxCacheTime = null, $minCacheTime = null){
         $config = $GLOBALS['config'];
         
         $maxCacheTime = $maxCacheTime
            ? $maxCacheTime : $config['app']['maxCacheTime'];
         $minCacheTime = $minCacheTime
            ? $minCacheTime : $config['app']['minCacheTime'];
         
         $requestTime = $_SERVER['REQUEST_TIME'];
         $result = $maxCacheTime - ($requestTime) % $maxCacheTime;
         return $result > $minCacheTime ? $result : $minCacheTime;
    }
    
    /**
     * 写日志
     * @param string $message
     * @param string $level
     */
    public static function addLog($msg, $level ='info'){
        if(!self::$logger){
            //日志初始化
            if (file_exists($GLOBALS['config']['app']['loggerPath'])) {
                include_once $GLOBALS['config']['app']['loggerPath'];
            }
            try {
                $logConfig = include(APPLICATION_PATH . '/configs/log.ini');
                Core_Log_Logger::$config = $logConfig;
                Core_Log_Writer_Log4php::$config = $logConfig;
                self::$logger = Core_Log_Logger::getInstance(Core_Log_Logger::WRITER_LOG4PHP);
        
            } catch (Exception $ex) {
            }
        
        }
        if (self::$logger == null || get_class(self::$logger)!='Core_Log_Logger') {
            return;
        }
        switch ($level) { 
            case 'debug':
                self::$logger->debug($msg);
                break;
            case 'info':
                self::$logger->info($msg);
                break;
            case 'warn':
                self::$logger->warn($msg);
                break;
            case 'error':
                self::$logger->error($msg);
                break;
            case 'fatal':
                self::$logger->fatal($msg);
                break;
            default:
                self::$logger->info($msg);
        }
    }
    
    public static function getUrlContent($url, $param = array(), $timeout = 10, $method = 'GET'){
        $rs = '';
        if(strpos($url, 'http') !==0){
            return $rs;
        }
        if(strtoupper($method) == 'POST'){
            $curl = new Core_Curl_POST();
        } else {
            $curl = new Core_Curl_Get();
        }
        try{        
            $rs = $curl->socket($url,  array('data' => $param,'timeout'=>$timeout));
        } catch (Exception $ex){
        }
        unset($curl);
        return $rs;
    }
    
    public static function removeRes($content){
        $rs = '';
        $pattern = '/<style[^>]*?>.*?<\/style>/is';
        $rs = preg_replace($pattern, '', $content);
        $pattern = '/<script[^>]*?>.*?<\/script>/is';
        $rs = preg_replace($pattern, '', $rs);
        $pattern = '/<!--.*?-->/is';
        $rs = preg_replace($pattern, '', $rs);
        return $rs;
    }
}