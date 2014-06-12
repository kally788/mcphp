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
 * |    Logger.class.php
 * +------------------------------------------------------------------------------
 * |    CreateTime:    2014/05/22
 * |    ModifyTime:    2014/06/10
 * |    Author:        zhujili <280000280@qq.com>
 * +------------------------------------------------------------------------------
 * |    Description:日志驱动
 * +------------------------------------------------------------------------------
 */
class Logger
{
    const DEBUG = 0;
    const INFO = 1;
    const WARN = 2;
    const ERROR = 3;
    const FATAL = 4;

    private $__type = array("DEBUG", "INFO", "WARN", "ERROR", "FATAL");

    private $__config = array(
        "LEVEL" => self::DEBUG, //日志级别
        "DIR" => "./log/", //保存日志的目录
        "PREFIX" => "Logger_" //日志文件前缀
    );

    /**
     * 构造函数
     * +----------------------------------------------------------
     * @public
     * +----------------------------------------------------------
     * @param object $_config 配置文件，array("LEVEL" => 日志级别0-4, "DIR" => "保存日志的目录", "PREFIX" => "日志文件前缀")
     *
     * 日志级别说明：
     * 0 DEBUG 指出细粒度信息事件对调试应用程序是非常有帮助的。
     * 1 INFO  表明 消息在粗粒度级别上突出强调应用程序的运行过程。
     * 2 WARN  表明会出现潜在错误的情形。
     * 3 ERROR 指出虽然发生错误事件，但仍然不影响系统的继续运行。
     * 4 FATAL 指出每个严重的错误事件将会导致应用程序的退出。
     * +----------------------------------------------------------
     * @return void
     * +----------------------------------------------------------
     * @author zhujili <280000280@qq.com>
     * @date 2014/05/22
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    public function __construct($_config = null)
    {
        if ($_config && is_array($_config)) {
            $this->__config = array_merge($this->__config, $_config);
        }
    }

    /**
     * 记录日志
     * +----------------------------------------------------------
     * @public
     * +----------------------------------------------------------
     * @param string $_msg 记录内容
     * @param int $_level 日志级别
     * +----------------------------------------------------------
     * @return string 日志记录
     * +----------------------------------------------------------
     * @author zhujili <280000280@qq.com>
     * @date 2014/05/22
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    public function put($_msg, $_level)
    {
        if (
            !is_int($_level) ||
            ($_level > self::FATAL && $_level < self::DEBUG) ||
            ($this->__config["LEVEL"] == self::FATAL && $_level < self::FATAL) ||
            ($this->__config["LEVEL"] == self::ERROR && $_level < self::ERROR) ||
            ($this->__config["LEVEL"] == self::WARN && $_level < self::WARN) ||
            ($this->__config["LEVEL"] == self::INFO && $_level < self::INFO)
        ) {
            return null;
        }

        $date = date("Y-m-d h:i:s");
        $time = microtime($date);
        $micro = explode(".", $time);
        $tree = debug_backtrace();
        $rs = $date . " " . $micro[1] . " " . $this->__type[$_level] . " - " . $_msg;
        $len = count($tree);
        for($i = 0; $i < $len; $i++){
            $rs .= PHP_EOL . "    -> " . $tree[$i]["file"] . " in " . $tree[$i]["line"] . " [ " . $tree[$i]["function"] . " ]";
        }
        $rs .= PHP_EOL;
        $file = $this->__config["DIR"] . $this->__config["PREFIX"] . date("Y-m-d") . ".log";
        $log = @file_get_contents($file);
        if ($log) {
            $log .= PHP_EOL . $rs;
        } else {
            $log .= $rs;
        }
        file_put_contents($file, $log);
        return $rs;
    }
} 