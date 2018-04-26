<?php
/**
 *任务计划
 */
namespace Cron\Controller;
use Think\Controller;

class ActivityController extends Controller {

    /* 每秒钟执行一次 */
    public function perSec(){
        D('Activity')->getWinList(); //活动抽奖

    }

    /* 每秒钟执行一次 */
    public function everyMinute(){

    }







}