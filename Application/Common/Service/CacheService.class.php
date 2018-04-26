<?php
/**
 * 数据缓存层
 * 需要缓存是数据调用逻辑
 * Created by PhpStorm.
 * User: zhangshiping
 * Date: 15-10-13
 * Time: 上午11:10
 */

namespace Common\Service;


class CacheService {
    private static $redis = false;
    private $updateStatus = false; //更新redis缓存数据标志


    public function __construct(){
        self::getRedis();
        $this->updateStatus = C('REDIS_KEYS.update');
    }


    /**
     * 获取redis对象
     */
    public static function getRedis(){
        if(!self::$redis){
            self::$redis = \Think\Cache::getInstance('Redis');
        }
        return self::$redis;
    }


    /**
     * 获取缓存数据
     */
    public static function getData($key){
        $call = explode(':',$key);
        $args = func_get_args();
        unset($args[0]);
        $key = $key.':|'.implode(':',$args);
        $no = substr($call[0],0,1);
        //解决同时段里的sql查询并发
        if(S(C('DATA_KEY.R:ChacheCreate').':'.md5($key))){
            for($i=0;$i<400;++$i){ //20秒等待时间
                usleep(50);//每0.05秒查看一次前面是否构建好缓存数据
                if(!S(C('DATA_KEY.R:ChacheCreate').':'.md5($key))){ //已经构建好数据
                    break;
                }
            }
        };
        $data = S($key);
        //$data = false;
        if($data===false){
            S(C('DATA_KEY.R:ChacheCreate').':'.md5($key),1,60); //标记正在进入构建数据
            if($no=='n'){
                $call[0] = substr($call[0],1);
            }
            if($call[1]=='Model'){
                $obj = D($call[0].'/'.$call[2]);
            }else{
                $obj = D($call[0].'/'.$call[2],$call[1]);
            }
            $data = call_user_func_array(array($obj,$call[3]),$args);
            //if(!$data)return $data; //无数据不缓存
            if($call[4]){ //有过期时间设置
                S($key,$data,$call[4]);
            }else{
                S($key,$data);
            }
            S(C('DATA_KEY.R:ChacheCreate').':'.md5($key),null); //清除构建数据标记
        }
        return $data;
    }


    /**
     *清除缓存
     */
    public function clearData($key){
        $keys = $this->getKeys($key);
        $result = array();
        foreach ($keys as $value) {
            self::$redis->del($value) AND $result[] = $value;
        }
        return $result;
    }


    /**
     * 更新缓存
     */
    public function updateData($key){
        if(!S($this->updateStatus))return array();
        S($this->updateStatus,0); //进入更新
        $keys = $this->getKeys($key);
        $result = array();
        foreach ($keys as $value) {
            if(self::$redis->del($value)){
                $key = substr($value,strlen(C('DATA_CACHE_PREFIX')));
                $obj = explode(':|',$key);
                $call = explode(':',$obj[0]);
                $args = explode(':',$obj[1]);;
                if($call[1]=='Model'){
                    $obj = D($call[0].'/'.$call[2]);
                }else{
                    $obj = '\\'.$call[0].'\\'.$call[1].'\\'.$call[2].$call[1];
                    $obj = new $obj();
                }
                $data = call_user_func_array(array($obj,$call[3]),$args);
                S($key,$data) AND $result[] = $value;
            }
        }

        return $result;
    }

    /**
     * 计划更新缓存
     */
    public function planUpdate(){
          return S($this->updateStatus,1);
    }


    /**
     * 获取所有关联key
     */
    public function getKeys($key){
        return self::$redis->keys(C('DATA_CACHE_PREFIX').$key.'*');
    }


    /**
     * 魔术方法,调用
     */
    public static function __callStatic($name, $arguments){
        if(!self::$redis){
            self::getRedis();
        }
        isset($arguments[0]) AND $arguments[0] = C('DATA_CACHE_PREFIX').$arguments[0];//key前缀拼接
        return call_user_func_array(array(self::$redis,$name),$arguments);
    }




} 