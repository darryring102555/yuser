<?php
/**
 * Created by PhpStorm.
 * User: zhangshiping
 * Date: 15-8-11
 * Time: 下午1:35
 */

namespace Common\Model;


class ShoppingCartModel {
    protected $status=array(
        0 => array('status_code'=>0,'status_message'=>'失败'),
        1 => array('status_code'=>1,'status_message'=>'成功'),
        3 => array('status_code'=>3,'status_message'=>'参数错误')
    );

    /**
     * 添加商品到购物车
     * 参数:array('goods_id'=>'商品id','goods_num'=>'商品数量')
     * 返回:array('status_code'=>'状态码','status_message'=>'错误信息')
     * status_code:0-失败,1-成功,3-参数错误
     */
    public function addToCart($param=array()){
        if(!(intval($param['goods_id']) && $param['goods_id']>0 && intval($param['goods_num']))){
            return $this->status[3];
        }
        setcookie('PHPSESSID',$_COOKIE['PHPSESSID'],time()+3600*24*365,'/');
        $goods = session('goods');
        if($goods[$param['goods_id']]){
            $goods[$param['goods_id']]['goods_num'] = $goods[$param['goods_id']]['goods_num']+$param['goods_num'];
        }else{
            $goods[$param['goods_id']]['goods_num'] = $param['goods_num'];
            $goods[$param['goods_id']]['ctime'] = time();
            $goods[$param['goods_id']]['goods_id'] = $param['goods_id'];
        }
        if($goods[$param['goods_id']]['goods_num'] < 0){
            unset($goods[$param['goods_id']]);
        }
        if(session('goods',$goods)===false){
            return $this->status[0];
        };
        return $this->status[1];
    }


    /**
     * 获取购物车里的商品
     * 返回:array('goods'=>array(),'status_code'=>'状态码','status_message'=>'错误信息')
     * goods:商品列表信息---二维数组('goods_id':商品id,'goods_num':商品数量);
     * status_code:0-失败,1-成功,3-参数错误
     */
    public function getCart(){
        $result = $this->status[1];
        $result['goods'] = session('goods');
        usort($result['goods'],function($a,$b){ //更加创建时间先后排序
            if ($a['ctime'] == $b['ctime']) {
                return 0;
            }
            return ($a['ctime'] < $b['ctime']) ? -1 : 1;
        });
        return $result;
    }


    /**
     * 清空购物车
     * 返回:array('status_code'=>'状态码','status_message'=>'错误信息')
     */
    public function emptyCart(){
        session('goods',null);
        return $this->status[1];
    }


    /**
     *设置商品数量
     * 参数:array('goods_id'=>'商品id','goods_num'=>'商品数量')
     * 返回:array('status_code'=>'状态码','status_message'=>'错误信息')
     * status_code:0-失败,1-成功,3-参数错误
     */
    public function setCart($param=array()){
        if(!intval($param['goods_id']) && $param['goods_id']>0){
            return $this->status[3];
        }
        setcookie('PHPSESSID',$_COOKIE['PHPSESSID'],time()+3600*24*365,'/');
        $goods = session('goods');
        if($param['goods_num']>=0){
            if(!$goods[$param['goods_id']]){
                $goods[$param['goods_id']]['ctime'] = time();
                $goods[$param['goods_id']]['goods_id'] = $param['goods_id'];
            }
            $goods[$param['goods_id']]['goods_num'] = $param['goods_num'];
        }else{
            unset($goods[$param['goods_id']]);
        }
        if(session('goods',$goods)===false){
            return $this->status[0];
        };
        return $this->status[1];
    }


    /**
     *查询购物车某个商品数量
     * 参数:array('goods_id'=>'商品id')
     * 返回:array('status_code'=>'状态码','status_message'=>'错误信息')
     * status_code:0-失败,1-成功,3-参数错误
     */
    public function getGoodsNum($param=array()){
        if(intval($param['goods_id'])<1){
            return $this->status[3];
        }
        $result = $this->status[1];
        $goods = session('goods')[$param['goods_id']];
        $result['goods'] = $goods ? $goods : null;
        return $result;
    }



} 