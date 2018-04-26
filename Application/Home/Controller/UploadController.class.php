<?php
namespace Home\Controller;

class UploadController extends BaseController {
    protected $mustVerify = false; //是否需要登录
   public function img(){
       $this->setAjax();
       $dir = I('request.dir','other');
       $config = array(
           'maxSize'       =>  1024*1024*2,
           'exts'          =>  array('jpg', 'gif', 'png', 'jpeg','bmp'),
           'rootPath'      => './Public/img/upload/',
           'savePath'      =>  $dir.'/'.date('Y',time()).'/'.date('m',time()).'/'.date('d',time()).'/',
           'subName'       => false,
           'saveName'      =>    array('uniqid','') //保存文件名唯一
       );
       if($dir=='head_portrait'){
           $uid = I('request.uid','default');
           $config['savePath'] = $dir.'/'.intval($uid/1000000).'/'.intval($uid/10000).'/'.intval($uid/100).'/';
           $config['saveExt'] = 'jpg';
           $config['saveName'] = "$uid";
           $config['replace'] = true;
       }
       $uploader = new \Think\Upload($config);
       $info = $uploader->uploadOne($_FILES['Filedata']);
       if($info){
           $data['pic'] = 'upload/'.$info['savepath'].$info['savename'];  //得到上传后的路径
           $this->outPut($data,'上传成功');
       }
       $data['content'] = $uploader->getError();
       $this->outPut($data,'上传失败');
   }






    /**
     * 百度编辑器文件上传入口
     */
    public function ueditor(){
        $this->setAjax();
        set_time_limit(0);
        $CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents("./Public/tooljs/ueditor/config.json")), true);
        $action = $_GET['action'];
        $action!='config' AND import('Vendor.Ueditor.Uploader');
        switch ($action) {
            case 'config':
                $result = $CONFIG;
                break;

            /* 上传图片 */
            case 'uploadimage':
                /* 上传涂鸦 */
            case 'uploadscrawl':
                /* 上传视频 */
            case 'uploadvideo':
                /* 上传文件 */
            case 'uploadfile':
            $result =  $this->upload($CONFIG);
                break;

            /* 列出图片 */
            case 'listimage':
                $result =  $this->uplist($CONFIG);
                break;
            /* 列出文件 */
            case 'listfile':
                $result =  $this->uplist($CONFIG);
                break;

            /* 抓取远程文件 */
            case 'catchimage':
                $result =  $this->craw($CONFIG);
                break;

            default:
                $result = array(
                    'state'=> '请求地址出错'
                );
                break;
        }
        $this->outPut($result);
    }

    private function craw($CONFIG){
        /* 上传配置 */
        $config = array(
            "pathFormat" => $CONFIG['catcherPathFormat'],
            "maxSize" => $CONFIG['catcherMaxSize'],
            "allowFiles" => $CONFIG['catcherAllowFiles'],
            "oriName" => "remote.png"
        );
        $fieldName = $CONFIG['catcherFieldName'];

        /* 抓取远程图片 */
        $list = array();
        if (isset($_POST[$fieldName])) {
            $source = $_POST[$fieldName];
        } else {
            $source = $_GET[$fieldName];
        }
        foreach ($source as $imgUrl) {
            $item = new Uploader($imgUrl, $config, "remote");
            $info = $item->getFileInfo();
            array_push($list, array(
                "state" => $info["state"],
                "url" => '/'.$info["url"],
                "size" => $info["size"],
                "title" => htmlspecialchars($info["title"]),
                "original" => htmlspecialchars($info["original"]),
                "source" => htmlspecialchars($imgUrl)
            ));
        }

        /* 返回抓取数据 */
        return array(
            'state'=> count($list) ? 'SUCCESS':'ERROR',
            'list'=> $list
        );
    }

    private function uplist($CONFIG){
        /* 判断类型 */
        switch ($_GET['action']) {
            /* 列出文件 */
            case 'listfile':
                $allowFiles = $CONFIG['fileManagerAllowFiles'];
                $listSize = $CONFIG['fileManagerListSize'];
                $path = $CONFIG['fileManagerListPath'];
                break;
            /* 列出图片 */
            case 'listimage':
            default:
                $allowFiles = $CONFIG['imageManagerAllowFiles'];
                $listSize = $CONFIG['imageManagerListSize'];
                $path = $CONFIG['imageManagerListPath'];
        }
        $allowFiles = substr(str_replace(".", "|", join("", $allowFiles)), 1);

        /* 获取参数 */
        $size = isset($_GET['size']) ? htmlspecialchars($_GET['size']) : $listSize;
        $start = isset($_GET['start']) ? htmlspecialchars($_GET['start']) : 0;
        $end = $start + $size;

        /* 获取文件列表 */
        $path = $_SERVER['DOCUMENT_ROOT'] . (substr($path, 0, 1) == "/" ? "":"/") . $path;
        $files = $this->getfiles($path, $allowFiles);
        if (!count($files)) {
            return json_encode(array(
                "state" => "no match file",
                "list" => array(),
                "start" => $start,
                "total" => count($files)
            ));
        }

        /* 获取指定范围的列表 */
        $len = count($files);
        for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--){
            $list[] = $files[$i];
        }
//倒序
//for ($i = $end, $list = array(); $i < $len && $i < $end; $i++){
//    $list[] = $files[$i];
//}

        /* 返回数据 */
        return array(
            "state" => "SUCCESS",
            "list" => $list,
            "start" => $start,
            "total" => count($files)
        );

    }

    private function upload($CONFIG){
        /* 上传配置 */
        $base64 = "upload";
        switch (htmlspecialchars($_GET['action'])) {
            case 'uploadimage':
                $config = array(
                    "pathFormat" => $CONFIG['imagePathFormat'],
                    "maxSize" => $CONFIG['imageMaxSize'],
                    "allowFiles" => $CONFIG['imageAllowFiles']
                );
                $fieldName = $CONFIG['imageFieldName'];
                break;
            case 'uploadscrawl':
                $config = array(
                    "pathFormat" => $CONFIG['scrawlPathFormat'],
                    "maxSize" => $CONFIG['scrawlMaxSize'],
                    "allowFiles" => $CONFIG['scrawlAllowFiles'],
                    "oriName" => "scrawl.png"
                );
                $fieldName = $CONFIG['scrawlFieldName'];
                $base64 = "base64";
                break;
            case 'uploadvideo':
                $config = array(
                    "pathFormat" => $CONFIG['videoPathFormat'],
                    "maxSize" => $CONFIG['videoMaxSize'],
                    "allowFiles" => $CONFIG['videoAllowFiles']
                );
                $fieldName = $CONFIG['videoFieldName'];
                break;
            case 'uploadfile':
            default:
                $config = array(
                    "pathFormat" => $CONFIG['filePathFormat'],
                    "maxSize" => $CONFIG['fileMaxSize'],
                    "allowFiles" => $CONFIG['fileAllowFiles']
                );
                $fieldName = $CONFIG['fileFieldName'];
                break;
        }

        /* 生成上传实例对象并完成上传 */
        $up = new \Uploader($fieldName, $config, $base64);
        return $up->getFileInfo();

    }

    /**
     * 遍历获取目录下的指定类型的文件
     * @param $path
     * @param array $files
     * @return array
     */
    private function getfiles($path, $allowFiles, &$files = array()){
        if (!is_dir($path)) return null;
        if(substr($path, strlen($path) - 1) != '/') $path .= '/';
        $handle = opendir($path);
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                $path2 = $path . $file;
                if (is_dir($path2)) {
                    getfiles($path2, $allowFiles, $files);
                } else {
                    if (preg_match("/\.(".$allowFiles.")$/i", $file)) {
                        $files[] = array(
                            'url'=> substr($path2, strlen($_SERVER['DOCUMENT_ROOT'])),
                            'mtime'=> filemtime($path2)
                        );
                    }
                }
            }
        }
        return $files;
    }





}