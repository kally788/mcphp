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
 * |    框架工作目录
 * +------------------------------------------------------------------------------
 */
define ("DIR_ROOT", dirname(__FILE__) . "/../.."); //根目录
define ("DIR_SRC", DIR_ROOT . "/SRC"); //源码目录
define ("DIR_STATIC", DIR_ROOT . "/STATIC"); //静态文件目录
define ("DIR_CORE", DIR_SRC . "/Core"); //框架核心类库目录
define ("DIR_CONFIG", DIR_SRC . "/Config"); //配置目录
define ("DIR_COMMON", DIR_SRC . "/Common"); //公共方法目录
define ("DIR_LIB", DIR_SRC . "/Lib"); //公共类库目录
define ("DIR_ACTION", DIR_SRC . "/Action"); //动作逻辑目录
define ("DIR_MODEL", DIR_SRC . "/Model"); //数据层目录

/**
 * |    载入配置和框架文件
 * +------------------------------------------------------------------------------
 */
require(DIR_CORE . "/Core.class.php"); //核心类
core::import(DIR_CORE . "/Model.class.php"); //数据模型基类
core::import(DIR_CORE . "/Action.class.php"); //控制器基类
core::import(DIR_COMMON . "/Common.func.php"); //公共函数
core::import(DIR_LIB . "/Logger.class.php"); //日志类库
core::import(DIR_CONFIG . "/ErrCode.inc.php"); //错误码列表
core::import(DIR_CONFIG . "/DriveConf.inc.php"); //驱动库配置
core::import(DIR_CONFIG . "/SystemSet.inc.php"); //系统设置

/**
 * |    设置编码和返回格式
 * +------------------------------------------------------------------------------
 */
header("Content-Type:application/json;charset=" . SystemSet::CHARSET);

/**
 * |    系统日志，非调试模式时，不输出错误到页面
 * +------------------------------------------------------------------------------
 */
ini_set("error_reprorting", SystemSet::DEBUG ? "E_ALL" : "E_ERROR | E_WARNING | E_PARSE");
ini_set("display_errors", SystemSet::DEBUG ? "On" : "Off");
ini_set("log_errors", "On");
ini_set("error_log", SystemSet::SYSTEM_LOG_DIR . "system.log");

/**
 * |    初始化东八区时区
 * +------------------------------------------------------------------------------
 */
if (function_exists("date_default_timezone_set")) {
    date_default_timezone_set("PRC");
}

/**
 * |    Facade.class.php
 * +------------------------------------------------------------------------------
 * |    CreateTime:    2014/05/22
 * |    ModifyTime:    2014/05/22
 * |    Author:    zhujili <280000280@qq.com>
 * +------------------------------------------------------------------------------
 * |    Description:框架门面
 * |        1.定义运行时环境目录结构常量
 * |        2.加载相关配置和框架文件
 * |        3.启动框架
 * +------------------------------------------------------------------------------
 */
class Facade
{
    private static $_instance = null;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }
        return self::$_instance;

    }

    /**
     * 启动框架
     * +----------------------------------------------------------
     * @access    public
     * +----------------------------------------------------------
     * @return    void
     * +----------------------------------------------------------
     * @access    public
     * @author    zhujili <280000280@qq.com>
     * @date      2014/06/05
     * @version   1.0.0.0
     * +----------------------------------------------------------
     */
    public function startup()
    {
        define ("TIME_START", microtime_float());
        $action = isset($_REQUEST["action"]) ? $_REQUEST["action"] : null;
        $method = isset($_REQUEST["method"]) ? $_REQUEST["method"] : null;

        $reqId = isset($_REQUEST["reqId"]) ? $_REQUEST["reqId"] : null;
        $callback = isset($_REQUEST["callback"]) ? $_REQUEST["callback"] : null;
        $token = isset($_REQUEST["token"]) ? $_REQUEST["token"] : null;
        $param = isset($_REQUEST["param"]) ? $_REQUEST["param"] : null;
        logs("输入：action=" . $action . ",method=" . $method . ",reqId=" . $reqId . ",callback=" . $callback . ",token=" . $token . ",param=" . $param, logger::DEBUG);
        try {
            $actionObj = core::load("Action.{$action}Action", array($reqId, $callback, $token));
            if ("__construct" == $method ||
                "__call" == $method ||
                "input" == $method ||
                "output" == $method ||
                "beforeStartup" == $method ||
                ! method_exists($actionObj, $method)) {
                error($action . "::" . $method . " 调用的接口错误", ErrCode::ACTION_METHOD_ERR);
            }
            $paramObj = $actionObj->input($param);
            $actionObj->beforeStartup($paramObj);
            $actionObj->$method($paramObj);
        } catch (Exception $e) {
            //产生异常时，返回错误码到前端
            if (isset($actionObj)) {
                $actionObj->output(SystemSet::DEBUG == false ? null : $e->getMessage(), $e->getCode());
            }
            $actionObj = new Action();
            $actionObj->output(SystemSet::DEBUG == false ? null : $e->getMessage(), $e->getCode());
        }
    }

    private function __clone()
    {
    }

}

?>