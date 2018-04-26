<?php
/**
 * 抽奖活动任务计划
 * Created by PhpStorm.
 * User: zhangshiping
 * Date: 15-9-22
 * Time: 下午4:19
 */

namespace Cron\Model;


class ActivityModel {
    protected $info = array();


    protected function setInfo($str){
        if(is_array($str)){
            $this->info = array_merge($this->info,$str);
        }else{
            $this->info[] = $str;
        }
        return true;
    }


    public function getInfo(){
        if(!$this->info) return false;
        return implode("<br />\r\n",$this->info);
    }


    /* 开启活动->开始抽奖->结束活动 */
    public function getWinList(){
        $this->beginActivity(); //到期活动开启
        $this->runLottery(); //本期活动结束->修改为开奖状态
        $this->runGrantAward(); //发奖
        $this->endActivity(); //结束过期活动
        dump_exit($this->getInfo());
    }

    /* 开启活动 */
    private function beginActivity(){
        $options['where']['stime'] = array('elt',NOW_TIME);
        $options['where']['status'] = array('eq',5); //状态是5,表示刚添加的新活动,开未开始活动
        $options['where']['type'] = array('lt',2);
        $res = D('Admin/Activity')->setStatus($options,1); //修改状态为1,正常开启活动
        $this->setReslutInfo($res,'成功开启一次性活动和永久性活动'.$res.'个!','开启一次性活动和永久性活动失败!');

        //周期性活动未到开始时间,到暂停状态
        $options = array();
        $options['where']['stime'] = array('elt',NOW_TIME);
        $options['where']['type'] = array('gt',1);
        $list = D('Admin/Activity')->getAll($options,array('in',array(2,5)));
        foreach($list as $row){
            if($this->isActivitying($row)){
                $in_ids[] = $row['id']; //在活动中
            }else{
                $row['status']!=2 AND $no_ids[] = $row['id'];
            }
        }
        $options = array();
        $options['where']['id'] = array('in',$in_ids);
        if($in_ids){
            $res = D('Admin/Activity')->setStatus($options,1);//活动开始
            $this->setReslutInfo($res,'成功开启周期性活动'.$res.'个!','开启周期性活动失败!');
        }

        $options = array();
        $options['where']['id'] = array('in',$no_ids);
        if($no_ids){
            $res =  D('Admin/Activity')->setStatus($options,2);//暂停状态
            $this->setReslutInfo($res,'周期性活动开启暂停状态'.$res.'个!','周期性活动开启暂停状态失败!');
        }
    }

    /* 活动进入开奖状态 */
    private function runLottery(){
        //一次性活动,活动结束时开奖
        $options['where']['etime'] = array('lt',NOW_TIME);
        $options['where']['status'] = array('eq',1);
        $options['where']['type'] = array('eq',0);
        $activity = D('Admin/Activity');
        $res = $activity->setStatus($options,3); //到期一次性活动进入抽奖状态
        $this->setReslutInfo($res,'一次性活动开始抽奖' . $res . '个!','一次性活动开始抽奖失败!');

        //周期性活动,根据计算得出是否开奖
        $options = array();
        $options['where']['type'] = array('gt',1);
        $activityList = $activity->getAll($options,1); //获取所有周期性活动,正在开始的
        $ids = array();
        foreach($activityList as $row){
            if(!$this->isActivitying($row)){ //判断当前活动是否处于活动中
                $ids[] = $row['id'];
            }
        }
        if($ids){
            $options = array();
            $options['where'] = array('id'=>array('in',$ids));
            $res = $activity->setStatus($options,3);
            $this->setReslutInfo($res,'周期性活动开始抽奖' . $res . '个!','周期性活动开始抽奖失败!');
        }
    }

    /* 开奖 */
    private function runGrantAward(){
        $activity = D('Admin/Activity');
        $activity_list = $activity->getAll(3); //正在抽奖的活动列表

        foreach ($activity_list as $row) {
            $winList = $this->runWinList($row); //抽奖,获取中奖名单
            if($winList===false){
                $this->setInfo('活动ID是'.$row['id'].'抽奖失败,在获取中奖名单时出错!');
                continue;
            }
            D()->startTrans(); //开启事务
            if($winList && $this->grantAward($winList)===false){
                D()->rollback();
                $this->setInfo('活动ID是'.$row['id'].'抽奖失败,在发奖时出错!');
                continue;
            }else{
                $options['where']['id'] = array('eq',$row['id']);
                if($row['type']>1){ //进入下一个周期
                   $activity->where($options['where'])->setInc('periods');
                }
                if($activity->setStatus($options,4)  AND D()->commit()){
                    $this->setInfo('活动ID是'.$row['id'].'抽奖成功!');
                }else{
                    D()->rollback();
                    $this->setInfo('活动ID是'.$row['id'].'抽奖失败!');
                }
            }

        }
    }

    /* 过期活动结束 */
    private function endActivity(){
        $options['where']['etime'] = array('lt',NOW_TIME);
        $options['where']['type'] = array('eq',0);
        $options['where']['status'] = array('eq',4);
        $res = D('Admin/Activity')->setStatus($options,6);
        $this->setReslutInfo($res,'一次性活动结束'.$res.'个!','一次性活动结束失败!');

        //周期性活动结束
        $options['where']['type'] = array('gt',1);
        $res = D('Admin/Activity')->setStatus($options,6);
        $this->setReslutInfo($res,'周期性活动结束'.$res.'个!','周期性活动结束失败!');
        $options['where']['etime'] = array('egt',NOW_TIME);
        $res = D('Admin/Activity')->setStatus($options,2);
        $this->setReslutInfo($res,'周期性活动暂停'.$res.'个!','周期性活动暂停失败!');


    }



    private function yanzheng($periods,$type){ //验证
        $result = false;
        if(!$type)return $result;
        foreach($periods as $value){
            $time1 = date('d',strtotime($value[0]));
            $time2 = date('d',strtotime($value[1]));
            $flog1 = $flog2 = false;
            $value[0] = date($type['fomat1'],strtotime($value[0]));
            $value[1] = date($type['fomat1'],strtotime($value[1]));
            $v1 = strtotime(date($type['fomat2'],NOW_TIME).$value[0]);//开始时间
            if($type['flog']){
                if(date($type['fomat1'],$v1)!=$value[0]){ //不对等,取当月最后一天此时
                    $v1=strtotime(lastDayInMonth($v1-24*3600*5).date($type['fomat3'],$v1));
                    $flog1 = true;
                }
            }
            $v2 = strtotime(date($type['fomat2'],NOW_TIME).$value[1]);//暂停时间
            if($type['flog']){
                if(date($type['fomat1'],$v2)!=$value[1]){ //不对等,取当月最后一天此时
                    $v2=strtotime(lastDayInMonth($v2-24*3600*5).date($type['fomat3'],$v2));
                    $flog2 = true;
                }
            }
            if($flog1 && !$flog2){
                $v1 = strtotime(lastDayInMonth($v1).' 23:59:59');
            }elseif(!$flog1 && $flog2){
                $v2 = strtotime(lastDayInMonth($v2).' 23:59:59');
            }elseif($flog1 && $flog2){
              if($time1>$time2){
                 $v1 = strtotime(date('Y-m',$v1).'-01 00:00:01');
              }elseif($time1<$time2){
                  $v2 = strtotime(lastDayInMonth($v2).' 23:59:59');
              }
            }
            if($v1>$v2){
                $result = ($v1<=NOW_TIME || $v2>=NOW_TIME);
            }else{
                $result = ($v1<=NOW_TIME && $v2>=NOW_TIME);
            }
            if($result)return $result;
        }
        return $result;
    }

    private function isActivitying($activity){
        $periods = explode(',',$activity['time_frame']); //周期内的活动阶段
        array_walk($periods,function(&$value){
            $value = explode('->',$value);
        });
        if($activity['type']==5){
            $result = false;
            foreach($periods as $value){
                $week1 = date('N',strtotime($value[0]));
                $week2 = date('N',strtotime($value[1]));
                $week3 = date('N',NOW_TIME);
                $v1 = strtotime(date('Y-m-d ',NOW_TIME).date('H:i:s',strtotime($value[0]))); //今天的开始时间
                $v2 = strtotime(date('Y-m-d ',NOW_TIME).date('H:i:s',strtotime($value[1]))); //今天的结束时间
                if($week1>$week2 || ($week1==$week2 && $v1>$v2)){
                    if($week3>$week1 || $week3<$week2){
                        $result = true;
                    }elseif($week3==$week1 || $week3==$week2){
                        $result = ($v1<=NOW_TIME || $v2>=NOW_TIME);
                    }
                }else{
                    if($week3>$week1 && $week3<$week2){
                        $result = true;
                    }elseif($week3==$week1 || $week3==$week2){
                        $result = ($v1<=NOW_TIME && $v2>=NOW_TIME);
                    }
                }
                if($result)return $result;
            }
        }else{
            $map = array(
                2=>array('fomat1'=>'s','fomat2'=>'Y-m-d H:i:','fomat3'=>'','flog'=>0),
                3=>array('fomat1'=>'i:s','fomat2'=>'Y-m-d H:','fomat3'=>'','flog'=>0),
                4=>array('fomat1'=>'H:i:s','fomat2'=>'Y-m-d ','fomat3'=>'','flog'=>0),
                6=>array('fomat1'=>'d H:i:s','fomat2'=>'Y-m-','fomat3'=>' H:i:s','flog'=>1),
                7=>array('fomat1'=>'m-d H:i:s','fomat2'=>'Y-','fomat3'=>' H:i:s','flog'=>1)
            );
            $result = $this->yanzheng($periods,$map[$activity['type']]);
        }

        return $result;
    }




    private function winListBySum($options,$rules){ //根据ActivityUser表中的num排名
        C('PAGE_SIZE',array_sum($rules)); //中奖总人数
        $options['order'] = array('num' => 'desc', 'ctime' => 'asc');
        $options['field'] = 'u_id';
        $data['list'] = D('Admin/ActivityUser')->getLimit(1,$options);
        $data['list'] = $data['list']===false ? false : $data['list']['data']; //中奖人员排名
        $data['rules'] = $rules;
        return $data;
    }

    private function winListByRandom($options,$rules,$flog=false){ //随机排名
        $options['field'] = 'u_id,win_probability,win_probability>0 AS precedence';
        $options['order'] = array(
            'precedence' => 'desc', //有概率的排前面
            'win_probability'=>'desc', //值越大概率越大
            'ctime' => 'asc'
        );
        $allList = D('Admin/ActivityUser')->getAll($options); //获取所有本期活动参与人员
        $count = count($allList); //参加活动总人数
        if($flog){
            foreach($rules as &$value){ //将概率转换成人数
                $value = explode('/',$value);
                $value = ceil($count*$value[0]/$value[1]);
            }
        }
        $winCount = array_sum($rules); //中奖人数
        $winList = $allList===false ? false : array(); //中奖名单
        foreach ($allList as $key => $row) {
            if (!(count($winList) < $winCount)) break; //中奖名单已满退出循环
            if ($row['win_probability']) { //根据概率选取中奖人员
                if (rand(1, 100) <= $row['win_probability']) {
                    $winList[] = $row;
                    unset($allList[$key]);
                }
            } else { //将剩余人员随机抽取
                shuffle($allList);
                $winList = array_merge($winList, array_slice($allList, 0, $winCount - count($winList)));
                break;
            }
        }
        $data['list'] = $winList;
        $data['rules'] = $rules;
        return $data;
    }

    private function runWinList($activity){ //获取中奖人员
        $rule = explode(',', $activity['rule']); //处理中奖规则
        foreach ($rule as $value) {
            $value = explode('-', $value);
            $rules[$value[0]] = $value[1]; //中奖等级,1等奖N人(或概率),2等奖N人(或概率)
        }
        $options['where']['a_id'] = array('eq', $activity['id']);
        $options['where']['periods'] = array('eq', $activity['periods']);
        $obj = D('Admin/ActivityUser');
        if ($activity['win_type'] == 0) { //根据指定中奖人数抽奖
            if ($activity['ranking_type'] == 0) { //根据ActivityUser表中的num排名
                $result = $this->winListBySum($options,$rules);
            } else { //随机排名
                $result = $this->winListByRandom($options,$rules);
            }
        } elseif ($activity['win_type'] == 1) { //根据概率中奖
            if($activity['ranking_type'] == 0){ //根据ActivityUser表中的num排名
                $where = $options['where'];
                $where['status'] = array('gt',0);
                $count = $obj->where($where)->count(); //参与活动总人数
                foreach($rules as &$value){ //将概率转换成人数
                    $value = explode('/',$value);
                    $value = ceil($count*$value[0]/$value[1]);
                }
                $result = $this->winListBySum($options,$rules);
            }else{ //随机排名
                $result = $this->winListByRandom($options,$rules,1);
            }
        }

        //根据规则->对应中奖等级
        if($result['list']===false)return false; //抽奖名单获取失败
        foreach($result['list'] as $key=>&$row){
            $row['a_id'] = $activity['id']; //活动ID
            $row['scale'] = $this->getScale($key,$result['rules']); //中奖等级
            $row['ranking'] = $key + 1; //中奖排名
            $row['periods'] = $activity['periods']; //活动期数
            $row['countent'] = ''; //奖品内容
            $row['ctime'] = NOW_TIME;
            $row['utime'] = NOW_TIME;
            $row['status'] = 1; //2表示已领取奖品,1为领导奖品
        }
        return $result['list'];
    }

    private function grantAward($winList){ //给中奖人员发放奖品

        return D('Admin/WinList')->addAll($winList);
    }

    
    private function setReslutInfo($res,$info,$error=''){
        if($res===false){
            $this->setInfo($error);
        }else{
            $res && $this->setInfo($info);
        }
    }





    private function getScale($key,$arr){ //判断排名等级
        $ranking = 0;
        foreach($arr as $k=>$value){
            $ranking += $value;
            if($key<$ranking) return $k;
        }
        return count($arr)+1;
    }

    private function getAward($activityid,$scale){ //获取奖品
        $award = D('Admin/Award');
        $options['where']['status'] = array('eq',1); //一次性商品,修改为已兑换
        $options['where']['a_id'] = $activityid;
        $options['where']['scale'] = $scale;
        $options['limit'] = 1;
        $award->setStatus($options,4); //兑换中
        $options = array();
        $options['where']['a_id'] = $activityid;
        $options['where']['scale'] = $scale;
        $options['field'] = 'id';
        $award_id = $award->getOne($options,4) OR $award_id = $award->getOne($options,2); //获取奖品ID
        if($award_id['id']){
            $options = array();
            $options['where']['id'] = $award_id['id'];
            $options['where']['status'] = 4;
            $award->setStatus($options,3); //产品已经兑换
        }
        return $award_id['id'] ? $award_id['id'] : 0;//开启事务;

    }


} 