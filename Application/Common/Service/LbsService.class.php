<?php
/**
 * Created by PhpStorm.
 * User: zhangshiping
 * Date: 16-1-12
 * Time: 上午11:07
 */

namespace Common\Service;


class LbsService {
    private static $pi = 3.14159265358979324;
    private static $a = 6378245.0;
    private static $ee = 0.00669342162296594323;
    private $locationAPI = 'http://api.map.baidu.com/location/ip?coor=bd09ll'; //百度通过ip查询位置接口
    private $locationByPointAPI = 'http://restapi.amap.com/v3/geocode/regeo?'; //高德通过经纬度查询位置信息接口
    private $nearbyCinemaAPI = 'http://yuntuapi.amap.com/datasearch/around?'; //高德查询附近影院信息接口
    private $dataAPI = 'http://yuntuapi.amap.com/datamanage/data/'; //高德地图数据操作接口
    private $locationByAddressAPI = 'http://restapi.amap.com/v3/geocode/geo?'; //高德通过地址获取坐标信息


    /**
     * 添加一条数据影院数据到高德地图
     * 参数:array
     * 返回:array();
     */
    public function add($data){
        if(!$data || !$data['id']) return false;
        $api = $this->dataAPI."create";

        $post = array();
        $post['_name'] = $data['cinema_name'];
        $post['_location'] = $data['lon'].','.$data['lat'];
        $post['_address'] = $data['area1_name'].$data['area2_name'].$data['area3_name'].$data['address'];
        $post['address'] = $data['address'];
        $post['id'] = $data['id'];
        $post['telephone'] = $data['cinema_phone'];
        $post['_image'] = C('IMG_HOST_NET').$data['cinema_logo'];
        $post['type'] = $data['type'];
        $param['tableid'] = C('LBS_TABID_GAODE');
        $param['key'] = C('LBS_KEY_GAODE');
        $param['loctype'] = (floatval($data['lon']) && floatval($data['lat'])) ? 1 : 2;
        $param['data'] = json_encode($post);
        $res = $this->getData($api,$param,'post'); //post数据
        if($res['status']!=1)return false;

        return $res['_id'];
    }


    /**
     * 修改一条数据影院数据到高德地图
     * 参数:array
     * 返回:array();
     */
    public function save($data){
        if($data['id'] && !$data['lbs_id']){ //获取高德地图影院id
            $data['lbs_id'] = D('Common/CinemaAttribute')->where('cinema_id='.$data['id'].' AND `status`=1')->field('lbs_id')->find()['lbs_id'];
        }
        if(!$data || !$data['id'] || !$data['lbs_id']) return false;
        $api = $this->dataAPI."update";
        $post = array();
        $post['_id'] = $data['lbs_id'];
        $post['id'] = $data['id'];

        isset($data['cinema_name']) AND $post['_name'] = $data['cinema_name'];
        if(isset($data['lon']) && isset($data['lat']) && floatval($data['lon']) && $data['lat']){
            $post['_location'] = $data['lon'].','.$data['lat'];
        }
        if(isset($data['area1_name']) && isset($data['area2_name']) && isset($data['area3_name']) && isset($data['address'])){
            $post['_address'] = $data['area1_name'].$data['area2_name'].$data['area3_name'].$data['address'];
            $post['address'] = $data['address'];
        }
        isset($data['cinema_phone']) AND $post['telephone'] = $data['cinema_phone'];
        isset($data['_image']) AND $post['_image'] = C('IMG_HOST_NET').$data['cinema_logo'];
        isset($data['type']) AND $post['type'] = $data['type'];
        $param['tableid'] = C('LBS_TABID_GAODE');
        $param['key'] = C('LBS_KEY_GAODE');
        $param['loctype'] = $post['_location'] ? 1 : 2;
        $param['data'] = json_encode($post);
        $res = $this->getData($api,$param,'post'); //post数据
        if($res['status']!=1)return false;
        return true;
    }


    /**
     * 删除地图上的影院
     * 参数:$ids 数组或数字,或逗号拼接字符串  影院id
     * 返回:array();
     */
    public function delete($ids){
        if(!$ids) return false;
        $api = $this->dataAPI."delete";
        if(is_array($ids)){
            $ids = implode(',',$ids);
        }
        $where['cinema_id'] = array('in',"$ids");
        $where['status'] = array('eq',1);
        $lbsids = D('Common/CinemaAttribute')->where($where)->where('`status`=1')->field('lbs_id')->select();
        foreach($lbsids as $row){
            $lbs_ids[] = $row['lbs_id'];
        }
        $param['tableid'] = C('LBS_TABID_GAODE');
        $param['key'] = C('LBS_KEY_GAODE');
        $param['ids'] = implode(',',$lbs_ids);
        $res = $this->getData($api,$param,'post'); //post数据
        if($res['fail'])return false;
        return $res['success'];
    }


    /**
     * 通过高德经纬度坐标获取附近影厅列表
     * 参数:$location 经纬度,$radius 范围(米为单位)
     * 返回:array();
     */
    public function getNearCinema($location,$radius,$page,$limit=false){
        $limit = $limit ? $limit : C('LIST_ROWS');
        $api = $this->nearbyCinemaAPI.'key='.C('LBS_KEY_GAODE').'&tableid='.C('LBS_TABID_GAODE').'&limit='.$limit;
        $api = "$api&center=$location&radius=$radius&page=$page";
        $data = $this->getData($api);
        if($data['status']!=1) return false;
        $result['count'] = intval($data['count']);
        $result['data'] = $data['datas'];
        return $result;
    }


    /**
     * 通过地址获取坐标信息
     */
    public function getPointByLoc($address){
        if(!$address)return false;
        $api = $this->locationByAddressAPI.'key='.C('LBS_KEY_GAODE');
        $api = "$api&address=$address";
        $data = $this->getData($api);
        if($data['status']!=1)return false;
        return $data['geocodes'][0]['location'];
    }


    /**
     * 通过高德经纬度坐标获取位置信息
     * 参数:$location 经纬度
     * 返回:array();
     */
    public function getLocByPoint($location){
        $api = $this->locationByPointAPI.'key='.C('LBS_KEY_GAODE');
        $api = "$api&location=$location";
        $data = $this->getData($api);
        if($data['status']!=1) return false;
        if(!$data['regeocode']['addressComponent']['city'] && !$data['regeocode']['addressComponent']['province'])return false;
        $city = $data['regeocode']['addressComponent']['city'] ? $data['regeocode']['addressComponent']['city'] : $data['regeocode']['addressComponent']['province'];
        $data['regeocode']['addressComponent']['city'] = $city;
        $where['name'] = array('eq',$city);
        $cityid = D('Area')->where($where)->field('area_id')->find();
        $result['address'] = $data['regeocode']['formatted_address']; //详细地址
        $result['city'] = (string)$data['regeocode']['addressComponent']['city']; //城市名字
        $result['id'] =  $result['cityid'] = (string)$cityid['area_id']; //城市ID
        $result['business'] = $data['regeocode']['addressComponent']['businessAreas'];
        unset($data['regeocode']['addressComponent']['citycode']);
        unset($data['regeocode']['addressComponent']['adcode']);
        unset($data['regeocode']['addressComponent']['building']);
        unset($data['regeocode']['addressComponent']['businessAreas']);
        $result['address_info'] = $data['regeocode']['addressComponent'];
        return $result;
    }


    /**
     * 通过ip获取高德经纬度坐标
     * 参数:$ip ip地址
     * 返回:array('city'=>'城市',"address"=>'地址','point'=>array());
     */
    public function getPoints($ip=null){
        $ip = $ip ? $ip : get_client_ip();
        if(!isIp($ip)) return false;
        //$ip = '192.168.3.89';
        $locationAPI = $this->locationAPI.'&ak='.C('LBS_KEY_BAIDU');
        $data = $this->getData($locationAPI.'&ip='.$ip);
        $location = array();
        if($data && !$data['status']){
            $location['city'] = explode('|',$data['address'])[2];
            $location['area_id'] = D('Area')->where('`name`="'.$location['city'].'市" OR '.'`name`="'.$location['city'].'"')->field('area_id')->find()['area_id'];
            $location['address'] = $data['address'];
            $location['id'] = $location['area_id'];
            $location['name'] = $location['city'];
            $location['point'] = $this->bd_decrypt($data['content']['point']['x'],$data['content']['point']['y']); //将百度坐标转成高德坐标
            $location['lbs'] = $location['point']['lon'].','.$location['point']['lat'];
        }else{
            $city = explode('省',getIpPlace($ip));
            $location['city'] = $city[1] ? $city[1] : $city[0];
            $location['area_id'] = $location['city']!='局域网' ? D('Area')->where('`name`="'.$location['city'].'" OR '.'`name`="'.$location['city'].'市"')->field('area_id')->find()['area_id'] : '';
            $location['address'] = '';
            $location['id'] = $location['area_id'];
            $location['name'] = $location['city'];
            $location['point'] = array(
                "lon" => '0',
                "lat" => '0'
            );
            $location['lbs'] = $location['point']['lon'].','.$location['point']['lat'];
        }
        return $location;
    }


    /**
     *  BD-09(百度) 坐标转换成  GCJ-02(火星，高德) 坐标
     *  参数: bd_lon 百度经度 bd_lat 百度纬度
     *  返回: array
     */
    public function bd_decrypt($bd_lon,$bd_lat){
        $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
        $x = $bd_lon - 0.0065;
        $y = $bd_lat - 0.006;
        $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $x_pi);
        $theta = atan2($y, $x) - 0.000003 * cos($x * $x_pi);
        $data['lon'] = $z * cos($theta);
        $data['lat'] = $z * sin($theta);
        return $data;
    }


    /**
     *  GCJ-02(火星，高德) 坐标转换成 BD-09(百度) 坐标
     *  参数: gg_lon 高德经度 gg_lat 高德纬度
     *  返回: array
     */
    public function bd_encrypt($gg_lon,$gg_lat){
        $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
        $x = $gg_lon;
        $y = $gg_lat;
        $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $x_pi);
        $theta = atan2($y, $x) - 0.000003 * cos($x * $x_pi);
        $data['lon'] = $z * cos($theta) + 0.0065;
        $data['lat'] = $z * sin($theta) + 0.006;
        return $data;
    }


    /**
     * 计算两组经纬度坐标 之间的距离
     * params ：lat1 纬度1； lng1 经度1； lat2 纬度2； lng2 经度2； len_type （0:m or 1:km);decimal 保留小数位数
     * return m or km
     */
    public function getDistance($lat1,$lng1,$lat2,$lng2,$len_type=0,$decimal=1){
        $R = 6378137;//地球半径(米)
        /* 将角度转为狐度 */
        $radLat1 = deg2rad($lat1);
        $radLat2 = deg2rad($lat2);
        $radLng1 = deg2rad($lng1);
        $radLng2 = deg2rad($lng2);
        $s = acos(cos($radLat1)*cos($radLat2)*cos($radLng1-$radLng2)+sin($radLat1)*sin($radLat2))*$R; //结果
        $s = round($s* 10000)/10000; //精度
        $len_type AND $s = $s/1000;
        return round($s,$decimal);
    }

    // WGS-84(GPS坐标，谷歌地球坐标) 到 GCJ-02(火星坐标) 的转换
    public static function wgsTOgcj($wgLat,$wgLon){
        if (self::outOfChina($wgLat, $wgLon)){
            return array('lat'=>0,'lon'=>0);
        }

        $dLat = self::transformLat($wgLon - 105.0, $wgLat - 35.0);
        $dLon = self::transformLon($wgLon - 105.0, $wgLat - 35.0);
        $radLat = $wgLat / 180.0 * self::$pi;
        $magic = sin($radLat);
        $magic = 1 - self::$ee * $magic * $magic;
        $sqrtMagic = sqrt($magic);
        $dLat = ($dLat * 180.0) / ((self::$a * (1 - self::$ee)) / ($magic * $sqrtMagic) * self::$pi);
        $dLon = ($dLon * 180.0) / (self::$a / $sqrtMagic * cos($radLat) * self::$pi);

        $data['lat'] = $wgLat + $dLat;
        $data['lon'] = $wgLon + $dLon;

        return $data;
    }

    private static function transformLat($x,$y)
    {
        $ret = -100.0 + 2.0 * $x + 3.0 * $y + 0.2 * $y * $y + 0.1 * $x * $y + 0.2 * sqrt(abs($x));
        $ret += (20.0 * sin(6.0 * $x * self::$pi) + 20.0 * sin(2.0 * $x * self::$pi)) * 2.0 / 3.0;
        $ret += (20.0 * sin($y * self::$pi) + 40.0 * sin($y / 3.0 * self::$pi)) * 2.0 / 3.0;
        $ret += (160.0 * sin($y / 12.0 * self::$pi) + 320 * sin($y * self::$pi / 30.0)) * 2.0 / 3.0;
        return $ret;
    }

    private static function transformLon($x, $y)
    {
        $ret = 300.0 + $x + 2.0 * $y + 0.1 * $x * $x + 0.1 * $x * $y + 0.1 * sqrt(abs($x));
        $ret += (20.0 * sin(6.0 * $x * self::$pi) + 20.0 * sin(2.0 * $x * self::$pi)) * 2.0 / 3.0;
        $ret += (20.0 * sin($x * self::$pi) + 40.0 * sin($x / 3.0 * self::$pi)) * 2.0 / 3.0;
        $ret += (150.0 * sin($x / 12.0 * self::$pi) + 300.0 * sin($x / 30.0 * self::$pi)) * 2.0 / 3.0;
        return $ret;
    }

    private static function outOfChina($lat,$lon){
        if ($lon < 72.004 || $lon > 137.8347)
            return true;
        if ($lat < 0.8293 || $lat > 55.8271)
            return true;

        return false;
    }


    /**
     * 远程获取数据
     * 参数:$url 请求地址
     * 返回:array();
     */
    private function getData($url,$param=null,$type='get',$timeout=15){
        foreach($param as $key => &$value){
            $value = $key."=".$value;
        }
        $ch = curl_init($url) ;
        curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ;
        if($type=='get'){
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ;
        }else{
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,implode('&',$param));
        }
        $data = json_decode(curl_exec($ch),true);
        curl_close($ch);
        return $data;
    }

} 