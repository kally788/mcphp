<?php
/**
 * +------------------------------------------------------------------------------
 * |    MCPHP [ mobile cloud php ]
 * +------------------------------------------------------------------------------
 * |    Copyright (c) 2014 http://mcphp.cn All rights reserved.
 * +------------------------------------------------------------------------------
 * |    Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * +------------------------------------------------------------------------------
 */

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
    logs($_msg . " ErrCode: " . $_err, LogRecord::FATAL);
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
    return core::load("Lib.LogRecord", DriveConf::$LOGRECORD)->put($_msg, $_level);
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
    if (is_null($_value)) {
        return false;
    }
    $_cache [$_key] = $_value;
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
 * @param    string $_name 要编码的字符串
 * +----------------------------------------------------------
 * @return    string
 * +----------------------------------------------------------
 * @author   zhujili <280000280@qq.com>
 * @date     2014/06/06
 * @version 1.0.0.0
 * +----------------------------------------------------------
 */
function unicode_encode($_name)
{
    $_name = iconv("UTF-8", "UCS-2", $_name);
    $len = strlen($_name);
    $str = "";
    for ($i = 0; $i < $len - 1; $i = $i + 2)
    {
        $c = $_name[$i];
        $c2 = $_name[$i + 1];
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
 * @param    string $_name unicode字符串
 * +----------------------------------------------------------
 * @return    string
 * +----------------------------------------------------------
 * @author   zhujili <280000280@qq.com>
 * @date     2014/06/06
 * @version 1.0.0.0
 * +----------------------------------------------------------
 */
function unicode_decode($_name)
{
    $pattern = "/([\w]+)|(\\\u([\w]{4}))/i";
    preg_match_all($pattern, $_name, $matches);
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
                $_name = str_ireplace($str, $c, $_name);
            }
        }
    }
    return $_name;
}

/**
 * 判断是否为数字
 * +----------------------------------------------------------
 * @param    string $_v 要判断的字符串
 * +----------------------------------------------------------
 * @return    boot
 * +----------------------------------------------------------
 * @author   zhujili <280000280@qq.com>
 * @date     2014/06/25
 * @version 1.0.0.0
 * +----------------------------------------------------------
 */
function isNum($_v)
{
    if(!isset($_v) || !preg_match(RE::CHECK_NUMBER, $_v)){
        return false;
    }
    return true;
}

/**
 * 计算2个经纬度之间的距离
 * +----------------------------------------------------------
 * @param    double $_lat1 开始经度Y
 * @param    double $_lng1 开始纬度X
 * @param    double $_lat2 结束经度Y
 * @param    double $_lng2 结束纬度X
 * +----------------------------------------------------------
 * @return   int 距离/米
 * +----------------------------------------------------------
 * @author   zhujili <280000280@qq.com>
 * @date     2014/07/08
 * @version 1.0.0.0
 * +----------------------------------------------------------
 */
function getDistance($_lat1, $_lng1, $_lat2, $_lng2)
{
    $rad =  3.1415926535898 / 180.0;
    $EARTH_RADIUS = 6378.137;
    $radLat1 = $_lat1 * $rad;
    //echo $radLat1;
    $radLat2 = $_lat2 * $rad;
    $a = $radLat1 - $radLat2;
    $b = $_lng1 * $rad - $_lng2 * $rad;
    $s = 2 * asin(sqrt(pow(sin($a/2),2) + cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)));
    $s = $s *$EARTH_RADIUS;
    $s = round($s * 10000) / 10000;
    return round ($s * 1000);
}

/**
 * 计算一个坐标半径范围内的最大/最小坐标值
 * +----------------------------------------------------------
 * @param    double $_lat 经度Y
 * @param    double $_lng 纬度X
 * @param    int $_distance 半径/米
 * +----------------------------------------------------------
 * @return   array 4个范围坐标
 * +----------------------------------------------------------
 * @author   zhujili <280000280@qq.com>
 * @date     2014/07/08
 * @version 1.0.0.0
 * +----------------------------------------------------------
 */
function getAround($_lat, $_lng, $_distance){
    $distance = $_distance*0.7072/1000;//不知道为什么会差40%
    $PI = 3.1415926535898;
    $radius = 6378.137;
    $lat = $_lat * $PI /180;
    $lng = $_lng * $PI /180;  //先换算成弧度
    $rad_dist = $distance / $radius;  //计算X公里在地球圆周上的弧度
    $lat_min = $lat - $rad_dist;
    $lat_max = $lat + $rad_dist;   //计算纬度范围
    //因为纬度在-90度到90度之间，如果超过这个范围，按情况进行赋值
    if($lat_min > -$PI/2 && $lat_max < $PI/2){
        //开始计算经度范围
        $lng_t = asin( sin($rad_dist) / cos($lat) );
        $lng_min = $lng - $lng_t;
        //同理，经度的范围在-180度到180度之间
        if ( $lng_min < -$PI ) $lng_min += 2 * $PI;
        $lng_max = $lng + $lng_t;
        if ( $lng_max > $PI) $lng_max -= 2 * $PI;
    } else {
        $lat_min = max ($lat_min , -$PI/2);
        $lat_max = min ($lat_max, $PI/2);
        $lng_min = -$PI;
        $lng_max = $PI;
    }
    //最后置换成角度进行输出
    $lat_min = $lat_min * 180 / $PI;
    $lat_max = $lat_max * 180 / $PI;
    $lng_min = $lng_min * 180 / $PI;
    $lng_max = $lng_max * 180 / $PI;
    return array(
        "lat_max"=>round($lat_max, 6),
        "lat_min"=>round($lat_min, 6),
        "lng_max"=>round($lng_max, 6),
        "lng_min"=>round($lng_min, 6)
    );
}

/**
 * 转换火星坐标
 * +----------------------------------------------------------
 * @param    double $_lat 经度Y
 * @param    double $_lng 纬度X
 * @param    int $_precision 精度，默认小数点后3位
 * +----------------------------------------------------------
 * @return   array 转换后坐标
 * +----------------------------------------------------------
 * @author   zhujili <280000280@qq.com>
 * @date     2014/07/10
 * @version 1.0.0.0
 * +----------------------------------------------------------
 */
function gpsLatLng($_lat, $_lng, $_precision = 3){
    $kv = "LAT_LNG_" . round($_lat, $_precision) . "_" . round($_lng, $_precision);
    $ll = cache($kv);
    if(false === $ll){
        $tmp = core::load("Lib.GpsOffset")->revise($_lat, $_lng);
        $ll = array(
            "lat" => round($tmp["lat"], $_precision),
            "lng" => round($tmp["lng"], $_precision)
        );
        cache($kv, $ll);
    }
    return $ll;
}

?>