<?php

function toTree($id,$list){
    $res = array();
    $flog = true;
    foreach($list as $key=>$row){
        if($id==$row[0]){
            $flog = false;
            $list1 = array_merge($list,array());
            unset($list1[$key]);
            $result = toTree($row[1],$list1);
            foreach($result as $k=>$v){
                if(!in_array($row[2],$result)){
                    $res[] = array($row[2],$v);
                }else{
                    $res[] = $v;
                }

            }
        }
    }
    if($flog){
        foreach($list as $key=>$row){
            if($id==$row[1]){
                $res[] = $row[2];
            }
        }
    }
    return $res;
}
function treeToLine($root,$list){
    $result = toTree($root,$list);
    foreach($result as $k=>$v){
        global $treeRow;
        $treeRow = array();
        if(is_array($v)){
            array_walk_recursive($v,function($item,$key){global $treeRow; $treeRow[]=$item;});
        }else{
            $treeRow = $v;
        }
        $result[$k] = $treeRow;
    }
    return $result;
}