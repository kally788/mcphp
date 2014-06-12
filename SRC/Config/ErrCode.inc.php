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

class ErrCode{

    /**
     * |    自定义错误码列表
     * +------------------------------------------------------------------------------
     */
    const LOGIN_ERR = 101010;//登录失败
    const GET_CONDITION_ERR = 101011;//查询条件错误

    /**
     * |    系统错误码列表
     * +------------------------------------------------------------------------------
     */

    const SUCCESS = 200;//成功
    const ACTION_ERR = 1;//接口模块错误
    const ACTION_METHOD_ERR = 2;//接口方法错误
    const SYSTEM_FILE_ERR = 3;//系统文件错误
    const SYSTEM_CLASS_ERR = 4;//系统类名错误
    const TOKEN_AUTH_FAIL = 5;//token验证失败
    const MYSQL_M_CONNECT_ERR = 6;//主数据库连接错误
    const MYSQL_S_CONNECT_ERR = 7;//从数据库连接错误
    const MEMCACHED_CONNECT_ERR = 8;//缓存库连接错误
}