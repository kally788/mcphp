<?php
class SystemSet{

    /**
     * |    系统设置
     * +------------------------------------------------------------------------------
     */

    const SESSION_EXPIRE = 0; //登录会话过期时间/秒，0为不过期
    const TABLE_PREFIX = "mc_"; //表前缀
    const CHARSET = "utf-8"; //编码设置
    const SYSTEM_LOG_DIR = "../log/"; //系统日志目录
    const DEBUG = true; //调试模式，正式环境下应设为false
    const OUTPUT_UNICODE = false; //是否unicode编码后发送

}