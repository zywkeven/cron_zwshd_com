<?php
/**
 * Api 接口
 * @author Keven.Zhong
 * @Version 1.0 At 2013-10-09
 */
class Model_Baidu {
    
    private static $_instance = null; //实例对象     
   
    private $_url = ''; //请求接口地址url
    private $_timeout = 10;
        
    private function __construct() {
        $this->_url = $GLOBALS['config']['app']['baidu']['url'];
        $this->_timeout = $GLOBALS['config']['app']['baidu']['timeout'];
        
    }
        
    /**
     * 获取单例实例
     * @return Model_OneApi
     */    
    public static function getInstance() {
        if (self::$_instance ===null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
   /**
    * 调用接口返回所需值
    * @param array $parameter 参数
    * @return mixed
    */
    public function getApi($parameter){
        $totalTimes = 2;
        if ($totalTimes < 1){
            //配置小于1时强制成1
            $totalTimes = 1;
        }
        $rs = null;
        while($totalTimes > 0){
            $rs = $this->getApiOnce($parameter);
            if($rs !== null){
                break;
            }
            $totalTimes -= 1;
        }
        return $rs;
    }
      
    /**
     * 调用一次接口返回所需值
     * @param array $parameter
     * @return mixed
     */
    public function getApiOnce( $parameter){
        try{
           $ch = curl_init();
            $options =  array(
                CURLOPT_URL => $this->_url,
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => $parameter,
                CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
            );
            curl_setopt_array($ch, $options);
            $rs = curl_exec($ch);
            return $rs;
        } catch (Exception $e){
            App_Common::addLog('OneApi Exception:'.$allUrl . '  '. $e->getMessage().'code:'.$e->getCode(), 'error');
            return null;
        }
    }
    
    
}
