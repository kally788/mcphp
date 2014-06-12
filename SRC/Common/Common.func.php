<?php
/**
 * |    Common.func.php
 * +------------------------------------------------------------------------------
 * |    CreateTime:    2014/05/22
 * |    ModifyTime:    2014/06/10
 * |    Author:        zhujili <280000280@qq.com>
 * +------------------------------------------------------------------------------
 * |    Description:公共函数库
 * |        1.命名规范：全小写字母，字母开头，单词之间用_隔开
 * +------------------------------------------------------------------------------
 */

/**
 * 抛出错误
 * +----------------------------------------------------------
 * @param string $_msg 错误消息
 * @param int $_err 错误码
 * +----------------------------------------------------------
 * @return void
 * +----------------------------------------------------------
 * @author   zhujili <280000280@qq.com>
 * @date     2014/06/10
 * @version 1.0.0.0
 * +----------------------------------------------------------
 */
function error($_msg, $_err){
    logs($_msg . " ErrCode: " . $_err, Logger::FATAL);
    throw new Exception($_msg, $_err);
}

/**
 * 记录日志
 * +----------------------------------------------------------
 * @param string $_msg 记录内容
 * @param int $_level 日志级别
 * +----------------------------------------------------------
 * @return string 日志记录
 * +----------------------------------------------------------
 * @author   zhujili <280000280@qq.com>
 * @date     2014/05/22
 * @version 1.0.0.0
 * +----------------------------------------------------------
 */
function logs($_msg, $_level)
{
    return core::load("Lib.Logger", DriveConf::$LOGGER)->put($_msg, $_level);
}

/**
 * 缓存设置和读取及清除
 * +----------------------------------------------------------
 * @param    string $_key null为清除所有缓存
 * @param    string $_value null为删除指定缓存，''取得缓存,否则为写入缓存，默认为''
 * @param    int $_expire 缓存时间(秒)，null时为全局设置的缓存时间，0时永不过期
 * +----------------------------------------------------------
 * @author   zhujili <280000280@qq.com>
 * @date     2014/05/22
 * @version 1.0.0.0
 * +----------------------------------------------------------
 */
function cache($_key = null, $_value = "", $_expire = null)
{

    static $_cache = array();

    //取得缓存对象实例
    $cache = core::load("Lib.memcached", DriveConf::$MEMCACHED);
    //清除所有缓存
    if (is_null($_key)) {
        return $cache->clear();
    }
    if ('' !== $_value) {
        if (is_null($_value)) {
            //删除指定缓存
            $cache->del($_key);
            if (isset ($_cache [$_key])) {
                unset ($_cache [$_key]);
            }
        } else { //写入缓存
            $cache->set($_key, $_value, $_expire);
            $_cache [$_key] = $_value;
        }
        return;
    }
    //读取缓存数据
    if (isset ($_cache [$_key])) {
        return $_cache [$_key];
    }
    $_value = $cache->get($_key);
    if (!is_null($_value)) {
        $_cache [$_key] = $_value;
    }
    return $_value;
}

/**
 * 多个键值取缓存值
 * +----------------------------------------------------------
 * @param    array $_keys array(键值,...)
 * +----------------------------------------------------------
 * @return  array()
 * +----------------------------------------------------------
 * @author   zhujili <280000280@qq.com>
 * @date     2014/05/22
 * @version 1.0.0.0
 * +----------------------------------------------------------
 */
function cache_more($_arr_key = array())
{
    $arr_value = NULL;
    if (!empty($_arr_key)) {
        foreach ($_arr_key as $key) {
            $arr_value[$key] = cache($key);
        }
    }
    return $arr_value;
}

/**
 * 对象转化为数组
 * +----------------------------------------------------------
 * @param    array /object
 * +----------------------------------------------------------
 * @return    array N维数组
 * +----------------------------------------------------------
 * @author   zhujili <280000280@qq.com>
 * @date     2014/05/22
 * @version 1.0.0.0
 * +----------------------------------------------------------
 */
function object_to_arr($_object)
{
    $arr_return = array();

    if (!empty ($_object)) {

        foreach ($_object as $k => $obj) {

            if (is_object($obj) || is_array($obj)) {
                if (!empty ($obj)) {
                    $arr_return [$k] = objectToArr($obj); //判断类型是不是object
                } else {
                    $arr_return [$k] = $obj;
                }
            } else {
                $arr_return [$k] = $obj;
            }
        }

    }
    return $arr_return;
}

/**
 * 数组转化为对象
 * +----------------------------------------------------------
 * @param    array /object
 * +----------------------------------------------------------
 * @return    array N维数组
 * +----------------------------------------------------------
 * @author   zhujili <280000280@qq.com>
 * @date     2014/05/22
 * @version 1.0.0.0
 * +----------------------------------------------------------
 */
function arr_to_object($_arr)
{
    $arr_return = NULL;
    if (!empty ($_arr) && is_array($_arr)) {
        $arr_return = new ArrayObject ($_arr);
    }
    return $arr_return;
}

/**
 * 二维数组排序
 * +----------------------------------------------------------
 * @param    array  要排序数组
 * @param    string 排序字段
 * @param    string SORT_ASC:升序,SORT_DESC:降序
 * +----------------------------------------------------------
 * @return    array()
 * +----------------------------------------------------------
 * @author   zhujili <280000280@qq.com>
 * @date     2014/05/22
 * @version 1.0.0.0
 * +----------------------------------------------------------
 */
function array_sort($_array, $_on, $_order = 'SORT_ASC')
{
    $new_array = array();
    $sortable_array = array();

    if (is_array($_array) && count($_array) > 0) {
        foreach ($_array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $_on) {
                        $sortable_array [$k] = $v2;
                    }
                }
            } else {
                $sortable_array [$k] = $v;
            }
        }

        switch ($_order) {
            case 'SORT_ASC' :
                asort($sortable_array);
                break;
            case 'SORT_DESC' :
                arsort($sortable_array);
                break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array [] = $_array [$k];
        }
    }
    return $new_array;
}

/**
 * 获取微秒浮点数，1毫秒=1000微秒
 * +----------------------------------------------------------
 * @return    float
 * +----------------------------------------------------------
 * @author   zhujili <280000280@qq.com>
 * @date     2014/06/06
 * @version 1.0.0.0
 * +----------------------------------------------------------
 */
function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

/**
 * unicode编码
 * +----------------------------------------------------------
 * @param    string $name 要编码的字符串
 * +----------------------------------------------------------
 * @return    string
 * +----------------------------------------------------------
 * @author   zhujili <280000280@qq.com>
 * @date     2014/06/06
 * @version 1.0.0.0
 * +----------------------------------------------------------
 */
function unicode_encode($name)
{
    $name = iconv("UTF-8", "UCS-2", $name);
    $len = strlen($name);
    $str = "";
    for ($i = 0; $i < $len - 1; $i = $i + 2)
    {
        $c = $name[$i];
        $c2 = $name[$i + 1];
        if (ord($c) > 0)
        {    // 两个字节的文字
            $str .= "\u".base_convert(ord($c), 10, 16).base_convert(ord($c2), 10, 16);
        }
        else
        {
            $str .= $c2;
        }
    }
    return $str;
}

/**
 * unicode解码
 * +----------------------------------------------------------
 * @param    string $name unicode字符串
 * +----------------------------------------------------------
 * @return    string
 * +----------------------------------------------------------
 * @author   zhujili <280000280@qq.com>
 * @date     2014/06/06
 * @version 1.0.0.0
 * +----------------------------------------------------------
 */
function unicode_decode($name)
{
    $pattern = "/([\w]+)|(\\\u([\w]{4}))/i";
    preg_match_all($pattern, $name, $matches);
    if (!empty($matches))
    {
        $len = count($matches[0]);
        for ($j = 0; $j < $len; $j++)
        {
            $str = $matches[0][$j];
            if (strpos($str, "\\u") === 0)
            {
                $code = base_convert(substr($str, 2, 2), 16, 10);
                $code2 = base_convert(substr($str, 4), 16, 10);
                $c = chr($code).chr($code2);
                $c = iconv("UCS-2", "UTF-8", $c);
                $name = str_ireplace($str, $c, $name);
            }
        }
    }
    return $name;
}
?>