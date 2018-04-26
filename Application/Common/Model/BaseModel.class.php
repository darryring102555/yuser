<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 14-11-24
 * Time: 下午10:39
 */

namespace Common\Model;


use Think\Model;

class BaseModel extends Model\RelationModel{
    protected $_line = array(); //关系线条
    protected $_link = array();//表关系

    public function __construct(){
        $this->setAuto();
        parent::__construct();
    }


    public function setAuto(){
        $common_auto = array (
            array('status','1'),                                          // 新增的时候把status字段设置为1
            array('utime','time',3,'function'),                           // 对update_time字段在更新的时候写入当前时间戳
            array('ctime','time',1,'function'),                           //创建时间
        );
        $common_validate = array(
            array('id','number','id必须是数字',2,'',2)                     //验证id
        );
        $this->_auto = array_merge($common_auto,$this->_auto);
        $this->_validate = array_merge($common_validate,$this->_validate);
    }


    public function add($data=array()){ /* 添加数据 */
       $data or $data = $this->data;
       return $this->create($data,self::MODEL_INSERT) ? parent::add() : false; //添加时验证
    }

    public function save($data=array()){ /* 保存数据 */
        $data or $data = $this->data;
        return $this->create($data,self::MODEL_UPDATE) ? parent::save() : false; //保存时验证这段
    }


    public function getOne($options = array(),$status = false){ /* 获取一条数据 */
        $options = $this->handleOptions($options,$status);
        $options['whereStr'] AND $options['where']['_string'] = $options['whereStr'];
        $result = isset($options['sql'])? $this->buildSql($options) : $this->find($options);
        if($result===null)return array();
        return $result;
    }


    public function getAll($options = array(),$status = false){ /* 查询所有数据 */
        if(is_numeric($options)){$status = $options; $options = array();}
        $options = $this->handleOptions($options,$status);
        $options['whereStr'] AND $options['where']['_string'] = $options['whereStr'];
        $result = isset($options['sql'])? $this->fetchSql(true)->select($options) : $this->select($options);
        if($result===null)return array();
        return $result;
    }


    public function getLimit($page = 1,$options = array(),$status = false){ /* 查询列表数据 */
        is_array($page) and $status = $options and $options = $page and $page = 1;
        $options = $this->handleOptions($options,$status);
        $options['whereStr'] AND $options['where']['_string'] = $options['whereStr'];
        $this->options  = $options;
        if($this->options['group']){
            $result['count'] = $this->cache(true,60)->query('SELECT COUNT(a.`id`) AS `count` FROM '.$this->buildSql().' AS a');
            $result['count'] = $result['count'][0]['count'];
        }else{
            $result['count'] = $this->cache(true,60)->count();
        }
        $pageCount = ceil($result['count']/C('PAGE_SIZE'));
        $pageCount = $pageCount ? $pageCount :1;
        $page = $page > $pageCount ? $pageCount: $page;
        $options['limit'] = ($page-1)*C('PAGE_SIZE').','.C('PAGE_SIZE');

        $result['list'] = isset($options['sql'])? $this->fetchSql(true)->select($options) : $this->select($options);
        if($result['list']===false)return false;
        $result['list'] = $result['list'] ? $result['list'] : array();
        return $result;
    }

    private function handleLink($options = array()){ //关联操作
        if (!$options) return array();
        is_string($options['field']) and $field[] = $options['field'];
        foreach ($options['field'] as $key => $row) {
            $upperKey = upper($key);
            $link = $this->_link[$upperKey];
            $alias = $link['relation_alias'];
            $alias = $alias ? '`'.$alias.'`' : '`obj`';
            if (is_array($row)) {
                array_walk($row, function (&$v, $k, $key) {
                    if (!strrpos($v, ')') && !strrpos($v, 'AS')) { //如果sql没有函数处理字段
                        $v = $v == '*' ? $key . '.' . $v . '' : $key . '.`' . str_ireplace(' as ','` AS `',$v) . '`';
                    }
                }, $alias);
                $field[$upperKey] = implode(',', $row);
            } else {
                $field[$upperKey] = strrpos($row, '.')===false ? $alias.'.'.$row : $row;
            }
            if ($link || strtolower($key) == 'obj') {

                switch ($link['mapping_type']) {
                    case self::MANY_TO_MANY:
                        $whereStr = '`obj`.`id`=' . $link['middle_alias'] .'.`' . $link['main_field'] . '` AND `'
                            .$link['middle_alias'].'`.`'.$link['relation_field'].'`=`'. $link['relation_alias'].'`.`id`';
                        $options['whereStr'] = $options['whereStr'] ? $options['whereStr'] . ' AND ' . $whereStr : $whereStr;
                        $options['table'] = $options['table'] ? $options['table'] : array();
                        $options['table'] = array_merge($options['table'], array(trueTab($link['middle_table'])=>$link['middle_alias'],trueTab($key) => $link['relation_alias']));
                        $options['field_alias'] = 1;
                        $status = $options['where']['status'][$link['relation_alias']];
                        $options['where']['status'][$link['relation_alias']] = $status ? $status : array('gt',0);
                        break;
                    case self::HAS_ONE:
                    case self::HAS_MANY:
                    case self::BELONGS_TO:
                        $options['join'] = $options['join'] ? $options['join'] :array();
                        $options['join'] = array_merge($options['join'],array('LEFT JOIN `'.trueTab($key).'` `'. $link['relation_alias'].'` ON `obj`.`'.$link['main_field'] .'` = `'.$link['relation_alias'].'`.`'.$link['relation_field'] .'`'));
                        $options['field_alias'] = 1;
                        $status = $options['where']['status'][$link['relation_alias']];
                        $options['where']['status'][$link['relation_alias']] = $status ? $status : array('gt',0);
                        break;
                    default:
                        break;
                }
            }
        }

        $options['field'] = implode(',', $field);

        return $options;
    }


    private function handleOptions($options=array(),$status = false){ /* 处理查询参数 */
        $this->alias('');
        if($options['field_alias']){ //存在多表查询处理
            $options['where']['status']['obj'] = $status===false ? array('gt',0) : array('eq',$status);
            $options = $this->handleLink($options);
            $whereStr = isset($options['whereStr']) ? $options['whereStr'] : '1';
            $options['table'][trueTab($this->name)] = '`obj`';
            unset($options['whereStr']);
            $options['where'] = $this->jointSql($whereStr,$options['where']);
        }else{
            foreach($options['where'] as $key=>$value){ //模糊匹配
                $value[0]=='like' AND $options['where'][$key][1]  = '%'.preg_replace('/([_%])/','\\\$1',$value[1]).'%';
            }
            $options['where']['status'] = $status===false ? array('gt',0) : $status;
        }
        return $options;
    }



    private function jointSql($sql,$where){ /* 多表查询sql拼接 */
        $exp = array('eq'=>'=','neq'=>'<>','gt'=>'>','egt'=>'>=','lt'=>'<','elt'=>'<=','like'=>'LIKE','between'=>'BETWEEN','in'=>'IN');
        $sqls[] = $sql;
        $alis='obj';
        foreach($where as $k=>$row){
            if(!is_array(current($row))){
                $expr = $exp[strtolower($row[0])];
                $sql = '`'.$alis.'`.`'.$k.'` '.$exp[strtolower($row[0])];
                if($expr=='BETWEEN'){
                    $sqls[] = $sql.' '.$row[1][0].' AND '.$row[1][1];
                }elseif($expr=='IN'){


                    $sqls[] = $sql.' ('.implode(',',$row[1]).')';
                }elseif($expr=='LIKE'){
                    $sqls[] = $sql.' "%'.preg_replace('/([_%])/','\\\$1',$row[1]).'%"';
                }else{
                    $sqls[] = $sql.' "'.$row[1].'"';
                }
            }else{
                foreach($row as $alias => $v){
                    $expr = $exp[strtolower($v[0])];
                    if(isset($this->_link[$alias]))$alias = $this->_link[$alias]['relation_alias'];
                    $sql = '`'.$alias.'`.`'.$k.'` '.$exp[strtolower($v[0])];
                    if($expr=='BETWEEN'){
                        $sqls[] = $sql.' '.$v[1][0].' AND '.$v[1][1];
                    }elseif($expr=='IN'){
                        $sqls[] = $sql.' ('.implode(',',$v[1]).')';
                    }elseif($expr=='LIKE'){
                        $sqls[] = $sql.' "%'.preg_replace('/([_%])/','\\\$1',$v[1]).'%"';
                    }else{
                        $sqls[] = $sql.' "'.$v[1].'"';
                    }
                }
            }
        }
        return implode(' AND ',$sqls);
    }


    public function setStatus($options = array(),$status = 0){ /* 设置状态 */
        $data['status'] = $status ? $status : 0;
        $this->options = $options;
        return  $this->data($data)->save();
    }

    public function removeData($options = array()){ /* 删除数据 */
        $options AND $this->options = $options;
        if(!$this->options['where']){ //条件判断
            return false;
        }
        $this->options['where']['status'] = array('gt',0);
        return $this->oppNum('status'); //状态取反
    }

    public function recycleData($options){ /* 回收数据 */
        $options AND $this->options = $options;
        if(!$this->options['where']){
            return false;
        }
        $this->options['where']['status'] = array('lt',0);
        return $this->oppNum('status'); //状态取反
    }



    public function getError(){ /* 返回自动验证的验证错误信息*/
        $error = $this->error;
        is_array($error) and $error = implode('<br/>　',$this->error);
        return $error;
    }

    /**
     * 字段值相反数
     * @access public
     * @param string $field  字段名
     * @param integer $step  定值减少
     * @param integer $lazyTime  延时时间(s)
     * @return boolean
     */
    public function oppNum($field,$step=0,$lazyTime=0) {
        if($lazyTime>0) {// 延迟写入
            $condition   =  $this->options['where'];
            $guid =  md5($this->name.'_'.$field.'_'.serialize($condition));
            $step = $this->lazyWrite($guid,$step,$lazyTime);
            if(false === $step ) return true; // 等待下次写入
        }
        return $this->setField($field,array('exp',$step.'-'.$field));
    }


} 