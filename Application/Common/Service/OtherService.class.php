<?php
/**
 * Created by PhpStorm.
 * User: zhangshiping
 * Date: 16-1-12
 * Time: 上午10:57
 * 三方组建功能类
 */

namespace Common\Service;


class OtherService {

    /**
     * 通过IP获取位置信息
     * 参数:ip ip地址,more 获取更多信息
     * 返回:ip所在城市
     */
    public function getIpPlace($ip,$more=false){
        if(!isIp($ip)) return false; //不是IP返回false;
        $iplocation = D('Common/IpLocation');
        $ipresult = $iplocation->getlocation($ip);
        $ipresult = $more ? $ipresult:$ipresult['country'];
        return $ipresult;
    }


    /**
     * 通过headers匹配手机型号
     * 参数:headers 数组,userAgent $_SERVER['userAgent']
     * 返回:手机型号信息
     */
    public function mobileDetect(array $headers = null,$userAgent = null){ /* 移动端检测 */

        import("Vendor.MobileDetect.MobileDetect");
        $obj = new \MobileDetect($headers,$userAgent);
        $deviceType = ($obj->isMobile() ? ($obj->isTablet() ? 'tablet' : 'phone') : 'PC');//判断是否是移动端
        if($deviceType=='PC')return $deviceType; //pc端返回
        preg_match('/; ([A-Za-z0-9_ \-]{0,}) Build/',$obj->getUserAgent(),$res); //获取移动端型号
        $model = $res[1]; //手机型号
        foreach($obj->getRules() as $name => $regex){
            if($obj->{'is'.$name}()){
                if(array_key_exists($name,$obj->getOperatingSystems())) return $name; //没有找到品牌,直接返回操作系统名称
                return $model ? $name.' '.$model : $name; //检测到品牌,且有对应的机型返回手机型号
            }
        }
        return '未知';
    }


    /**
     * 将列表数据excel输出
     * 参数: $sheets = array('title'=>'aa', //数据
     *                      'data'=>array(
     *                                 array('name'=>'李四','age'=>4,2,3),
     *                                 array('name'=>'张三','age'=>4,2,3))
     *                      )
     *      $file_name 文件名 ;
     * 返回: excel输出
     * */
   public function outPutExcel($sheets,$file_name='phpexcel'){ /* 将数据excel输出 */
        if(isset($sheets['data'])) $sheets = array($sheets); //仅一个数据列表
        if(!isset($sheets[0]['data'])) return false;//检验数据是否正确结构
        ob_clean();//保证输出格式正确
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"'); //文件名
        header('Cache-Control: max-age=0');

        import('Vendor.PHPExcel.Classes.PHPExcel');
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()
            ->setTitle('Office 2007 XLSX Document')
            ->setSubject('Office 2007 XLSX Document')
            ->setDescription('Document for Office 2007 XLSX, generated using PHP classes.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Result file');
        $i = $j = $m = 0;
        foreach($sheets as &$sheet){
            $objPHPExcel->createSheet();
            $objsheet = $objPHPExcel->setActiveSheetIndex($i)->setTitle($sheet['title']);
            foreach($sheet['data'][0] as $k=>$v){ //设置第一排标题
                $objsheet->setCellValue(numToLetter($j).'1',$k);
                ++$j;
            }
            foreach($sheet['data'] as $k=>$row){ //设置数据
                foreach($row as $v){
                    $objsheet->setCellValue(numToLetter($m).($k+2),$v);
                    ++$m;
                }
                $m = 0;
            }
            ++$i;
        }
        $objWriter=\PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
        $objWriter->save('php://output');
    }


    /**
     * 将excel文件转换成数据输出
     * 参数:excel 上传的excel文件,key 将第一排数据当成数据的key
     * 返回:数组
     */
    function excelToData($excel,$key=false){
        if($excel['error']) return '上传文件错误'; //上传文件错误
        $postfix = strrchr($excel['name'],'.');
        if(!in_array($postfix,array('.xls','.xlsx','.csv','.xlsm','.xltx','.xltm','.xlt','.ods','.ots','.slk','.xml','.gnumeric'))) return '文件类型错误'; //文件类型错误
        /* 缓存上传的文件 */
        $file = ROOT_PATH.'Upload/Excel/'.getMicrotime.randStr(4).$postfix; //拼接暂存文件名
        if(file_exists($file)) return '暂存文件已经存在'; //暂存文件存在
        if(!move_uploaded_file($excel['tmp_name'],$file)) return '文件移动失败';//上传文件暂放
        Vendor('PHPExcel.Classes.PHPExcel.IOFactory');
        $objPHPExcel = \PHPExcel_IOFactory::load($file);
        @unlink($file); //删除缓存文件
        $sheets = $objPHPExcel->getAllSheets(); //获取所有工作薄
        $excelData = array(); //存放excel数据
        foreach($sheets as $k=>$sheet){
            $excelData[$k]['title'] = $sheet->getTitle();
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
            if($key){
                for ($row = 2; $row <= $highestRow; ++$row) {
                    for ($col = 0; $col < $highestColumnIndex; $col++) {
                        $keys = (string)$sheet->getCellByColumnAndRow($col, 1)->getValue();
                        $excelData[$k]['data'][$row-2][$keys] = (string)$sheet->getCellByColumnAndRow($col, $row)->getValue();
                    }
                }
            }else{
                for ($row = 1; $row <= $highestRow; ++$row) {
                    for ($col = 0; $col < $highestColumnIndex; $col++) {
                        $excelData[$k]['data'][$row-1][] = (string)$sheet->getCellByColumnAndRow($col, $row)->getValue();
                    }
                }
            }
            if(!$excelData[$k]['data']) unset($excelData[$k]); //没有数据,删除该工作薄
        }
        if(count($excelData)==1)return $excelData[0];
        return $excelData;
    }

    /**
     * 生成二维码
     * 参数:url 访问地址,logo 图像
     * 返回:二维码图形
     */
    public function qrCode($url){
        ob_clean();//保证输出格式正确
        import('Vendor.phpqrcode.phpqrcode');
        \QRcode::png($url);exit;
    }

    /**
     * 邮件推送
     * 参数:sendto_email 接受人邮箱,user_name 接受人姓名,subject 邮件标题,body 邮件内容页面
     * 返回:布尔值
     */
    public function sendEmail($sendto_email, $user_name, $subject, $html){
        Vendor('PHPMailer.PHPMailerAutoload');
        $mail = new \PHPMailer();
        $mail->IsSMTP();                  // send via SMTP
        $mail->CharSet = "utf-8";   // 这里指定字符集！
        $mail->Encoding = "base64";
        $mail->SMTPAuth = true;           // turn on SMTP authentication

        $mail->Host = C('SMTP_SERVERS');   // SMTP servers
        $mail->Username = C('SMTP_USERNAME');     // SMTP username  注意：普通邮件认证不需要加 @域名
        $mail->Password = C('SMTP_PASSWORD'); // SMTP password
        $mail->From = C('SMTP_EMAIL');      // 发件人邮箱
        $mail->FromName = C('SMTP_NAME');  // 发件人
        foreach(C('EMAIL_REPLY') as $row){
            $mail->AddReplyTo($row['email'], $row['name']);
        }


        $mail->AddAddress($sendto_email, $user_name);  // 收件人邮箱和姓名
        $mail->SetFrom($mail->From, $mail->FromName);
        $mail->IsHTML(true);  // send as HTML
        $mail->AltBody = "text/html";
        $mail->Subject = $subject;  // 邮件主题
        //$mail->WordWrap = 50; // set word wrap 换行字数
        //$mail->AddAttachment("/var/tmp/file.tar.gz"); // attachment 附件
        //$mail->AddAttachment("/tmp/image.jpg", "new.jpg");
        $mail->Body = $html;   // 邮件内容

        return $mail->Send();
    }

    /**
     * 短信发送
     * 参数:phone 手机号码,conetent 短信内容
     * 返回:布尔值
     */
    public function sendSms($phone,$content){
        $url = C('SMS_URL');
        $curlPost = 'account='.C('SMS_UNAME').'&password='.C('SMS_PASSWORD')."&mobile={$phone}&content=".rawurlencode($content);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
        $return_str = curl_exec($curl);
        curl_close($curl);
        return $return_str==100;
    }


}

