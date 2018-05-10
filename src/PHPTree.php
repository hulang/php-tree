<?php
namespace hulang;
/**
 * 无限分级类
 *
 */
class PHPTree
{
    /** 子孙树
     * @param $data array  数据
     * @param $parent  string 父级元素的名称 如 pid
     * @param $son     string 子级元素的名称 如 id
     * @param $pid     int    父级元素的id 实际上传递元素的主键
     * @param $lv      int    级别
     * @return array 
     */
    public static function getSubTree($data, $parent = 'pid', $son = 'id', $pid = 0, $lv = 0)
    {
        $tmp = [];
        foreach ($data as $k => $v) {
            if ($v[$parent] == $pid) {
                $v['lv'] = $lv;
                $tmp[] = $v;
                $tmp = array_merge($tmp, self::getSubTree($data, $parent, $son, $v[$son], $lv + 1));
            }
        }
        return $tmp;
    }
    /**
     * @param $data array  数据
     * @param $parent  string 父级元素的名称 如 pid
     * @param $son     string 子级元素的名称 如 id
     * @param $pid     int    父级元素的id 实际上传递元素的主键
     * @return array 
     */
    public static function getSubTreeList($data, $parent = 'pid', $son = 'id', $pid = 0)
    {
        $tmp = [];
        foreach ($data as $k => $v) {
            if ($v[$parent] == $pid) {
                $v['child'] = self::getSubTreeList($data, $parent, $son, $v[$son]);
                $tmp[] = $v;
            }
        }
        return $tmp;
    }
    //组合一维数组
    public static function unlimitForLevel($data, $html = '├─', $pid = 0, $level = 0, $parent = 'pid', $son = 'id')
    {
        $arr = [];
        foreach ($data as $k => $v) {
            if ($v[$parent] == $pid) {
                $v['level'] = $level + 1;
                $v['html'] = str_repeat($html, $level);
                $arr[] = $v;
                $arr = array_merge($arr, self::unlimitForLevel($data, $html, $v[$son], $level + 1, $parent, $son));
            }
        }
        return $arr;
    }
    //组合多维数组
    public static function unlimitForLayer($data, $pid = 0, $name = 'child')
    {
        $arr = [];
        foreach ($data as $k => $v) {
            if ($v['pid'] == $pid) {
                $v[$name] = self::unlimitForLayer($data, $v['id'], $name);
                $arr[] = $v;
            }
        }
        return $arr;
    }
    // 合并成父子树
    public static function getTree($data, $parent = 'pid', $son = 'id', $name = 'child')
    {
        $tmp = [];
        if (!empty($data)) {
            $fu = [];
            $zi = [];
            foreach ($data as $k => $v) {
                if ($v[$parent] == 0) {
                    $fu[] = $v;
                } else {
                    $zi[] = $v;
                }
            }
            $arr = array_column($zi, $son);
            foreach ($zi as $k => $v) {
                $key = array_search($v[$parent], $arr);
                if ($key !== false) {
                    $zi[$k][$parent] = $zi[$key][$parent];
                }
            }
            $array = array_column($fu, $son);
            foreach ($zi as $k => $v) {
                $key = array_search($v[$parent], $array);
                if ($key !== false) {
                    $fu[$key][$name][] = $v;
                }
            }
            $tmp = $fu;
        }
        return $tmp;
    }
    //传递子分类的id返回所有的父级分类
    public static function getParents($data, $id, $parent = 'pid', $son = 'id')
    {
        $arr = [];
        foreach ($data as $k => $v) {
            if ($v[$son] == $id) {
                $arr[] = $v;
                $arr = array_merge(self::getParents($data, $v[$parent], $parent, $son), $arr);
            }
        }
        return $arr;
    }
    //传递子分类的id返回所有的父级分类
    public static function getParentsIds($data, $id, $parent = 'pid', $son = 'id')
    {
        $arr = [];
        foreach ($data as $k => $v) {
            if ($v[$son] == $id && $v[$parent] != 0) {
                $arr[] = $v[$parent];
                $arr = array_merge(self::getParentsIds($data, $v[$parent], $parent, $son), $arr);
            }
        }
        return $arr;
    }
    //传递父级id返回所有子级id
    public static function getChildsId($data, $pid, $parent = 'pid', $son = 'id')
    {
        $arr = [];
        foreach ($data as $k => $v) {
            if ($v[$parent] == $pid) {
                $arr[] = $v[$son];
                $arr = array_merge($arr, self::getChildsId($data, $v[$son], $parent, $son));
            }
        }
        return $arr;
    }
    //传递父级id返回所有子级分类
    public static function getChilds($data, $pid, $parent = 'pid', $son = 'id')
    {
        $arr = [];
        foreach ($data as $k => $v) {
            if ($v[$parent] == $pid) {
                $arr[] = $v;
                $arr = array_merge($arr, self::getChilds($data, $v[$son], $parent, $son));
            }
        }
        return $arr;
    }
}