<?php
/**
 * +------------------------------------------------------------------------------
 * |    MCPHP [ mobile cloud php ]
 * +------------------------------------------------------------------------------
 * |    Copyright (c) 2014 http://mcphp.cn All rights reserved.
 * +------------------------------------------------------------------------------
 */

/**
 * |    Core.class.php
 * +------------------------------------------------------------------------------
 * |    CreateTime:    2014/05/22
 * |    ModifyTime:    2014/05/22
 * |    Author:        zhujili <280000280@qq.com>
 * +------------------------------------------------------------------------------
 * |    Description:框架核心类
 * |        1.文件引入
 * |        2.模块单例工厂
 * +------------------------------------------------------------------------------
 */
class Core
{
    /**
     * 取得类单例对象
     * +----------------------------------------------------------
     * @static
     * @access    public
     * +----------------------------------------------------------
     * @param    string $class_path 路径:'Action.indexAtction'
     * +----------------------------------------------------------
     * @return    object 类对象
     * +----------------------------------------------------------
     * @access    static
     * @author    zhujili <280000280@qq.com>
     * @date    2014/05/22
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    public static function load($class_path, $arr_params = NULL)
    {
        static $_instance = array();
        $tmp = explode(".", $class_path);
        $clsName = end($tmp);
        $file_name = "/" . implode("/", $tmp) . ".class.php";
        if (!isset ($_instance [$clsName])) {
            if (core::import(DIR_SRC . $file_name)) {
                if (!class_exists($clsName)) {
                    error($file_name . "::" . $clsName . " 模块类名错误", ErrCode::SYSTEM_CLASS_ERR);
                }
                if (is_null($arr_params)) {
                    $_instance [$clsName] = new $clsName ();
                } else {
                    $_instance [$clsName] = new $clsName ($arr_params);
                }
            }
        }
        return $_instance [$clsName];
    }

    /**
     * 引入类文件
     * +----------------------------------------------------------
     * @static
     * @access    public
     * +----------------------------------------------------------
     * @param    string $file 文件路径
     * +----------------------------------------------------------
     * @return    bool
     * +----------------------------------------------------------
     * @access    static
     * @author    zhujili <280000280@qq.com>
     * @date    2014/05/22
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    public static function import($file)
    {
        static $_importFiles = array();
        $file_name = realpath($file);
        if (!self::file_exists_case($file_name)) {
            if(strpos($file, "Action.class.php")){
                error(basename($file) . " 接口模块不存在", ErrCode::ACTION_ERR);
            }else{
                error(basename($file) . " 系统模块不存在", ErrCode::SYSTEM_FILE_ERR);
            }
        }
        if (!isset ($_importFiles [$file_name])) {
            require $file_name;
            $_importFiles [$file_name] = 1;
        }
        return $_importFiles [$file_name];
    }

    /**
     * 区分大小写的文件存在判断
     * +----------------------------------------------------------
     * @static
     * @access    public
     * +----------------------------------------------------------
     * @param    string $filename 文件路径
     * +----------------------------------------------------------
     * @return    bool
     * +----------------------------------------------------------
     * @access    static
     * @author    zhujili <280000280@qq.com>
     * @date    2014/05/22
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    public static function file_exists_case($filename)
    {
        if (is_file($filename)) {
            return true;
        }
        return false;
    }
}

?>