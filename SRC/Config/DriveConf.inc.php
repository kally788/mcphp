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
class DriveConf{
    /**
     * |    数据库配置
     * +------------------------------------------------------------------------------
     */
    static $MYSQL = array(
        "M" => array(
            "NAME" => "mcphp", //主数据库名称
            "USER" => "root", //主数据库用户
            "PASSWORD" => "111111", //主数据库密码
            "HOST" => "127.0.0.1", //主机
            "PORT" => "3306" //端口
        ),
        "S" => array(
            "NAME" => "mcphp", //从数据库名称
            "USER" => "root", //从数据库用户
            "PASSWORD" => "111111", //从数据库密码
            "HOST" => "127.0.0.1", //主机
            "PORT" => "3306" //端口
        ),
        "DB_CHARSET" => "utf8" //数据库编码
    );

    /**
     * |    缓存库配置
     * +------------------------------------------------------------------------------
     */
    static $MEMCACHED = array(
        "CLUSTER" => array(array("127.0.0.1", 11211, 1)), //分布式memcached
        "EXPIRE" => 0 //缓存有效时间 (秒)，0为永不过期
    );

    /**
     * |    日志配置
     * +------------------------------------------------------------------------------
     * DEBUG Level指出细粒度信息事件对调试应用程序是非常有帮助的。
     * INFO level表明 消息在粗粒度级别上突出强调应用程序的运行过程。
     * WARN level表明会出现潜在错误的情形。
     * ERROR level指出虽然发生错误事件，但仍然不影响系统的继续运行。
     * FATAL level指出每个严重的错误事件将会导致应用程序的退出。
     *
     * 由上至下，如设置为DEBUG时，那么5种类型的日志都会被记录
     * +------------------------------------------------------------------------------
     */
    static $LOGRECORD = array(
        "LEVEL" => 0, //日志级别 0-4，正式环境下设为2
        "DIR" => "../_logs/", //保存日志的目录
        "PREFIX" => "debug"//日志文件前缀
    );

}
?>
