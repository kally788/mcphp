<?php
/**
 * +------------------------------------------------------------------------------
 * |    MCPHP [ mobile cloud php ]
 * +------------------------------------------------------------------------------
 * |    Copyright (c) 2014 http://mcphp.cn All rights reserved.
 * +------------------------------------------------------------------------------
 */

/**
 * |    Action.class.php
 * +------------------------------------------------------------------------------
 * |    CreateTime:    2014/06/06
 * |    ModifyTime:    2014/06/010
 * |    Author:        zhujili <280000280@qq.com>
 * +------------------------------------------------------------------------------
 * |    Description:控制器基类
 * |        1.输入、输出封装
 * |        2.输入、输出预处理接口
 * |        3.分布式缓存session、登录TOKEN认证
 * |        4.获取Model引用接口
 * |        5.Action之间不允许依赖，重用的逻辑一律用继承和抽象来实现
 * |        6.只有对外的接口才允许采用public，其它一律采用protected和private
 * +------------------------------------------------------------------------------
 */

class Action
{

    //请求编号
    private $__reqId = 0;
    //JSONP回调方法
    private $__callback = null;
    //登录验证
    private $__token = null;
    //新TOKEN
    private $__newToken = null;
    //服务器变量
    private $__session = null;

    //构造方法
    public function __construct($_params = array())
    {
        if(!empty($_params)){
            $this->__reqId = $_params[0] ? $_params[0] : 0;
            $this->__callback = $_params[1];
            $this->__token = $_params[2];
        }
    }

    //防止私有方法被调用而产生异常
    public function __call($_name, $_arguments){
        error("调用的接口是私有或保护的", ErrCode::ACTION_METHOD_ERR);
    }

    /**
     * 格式化前端输入的参数
     * +----------------------------------------------------------
     * @param array $_params 前端传递的字符串参数
     * +----------------------------------------------------------
     * @return string|int|array
     * +----------------------------------------------------------
     * @access public
     * @author zhujili <280000280@qq.com>
     * @date    2014/06/06
     * @mdate 2014/06/06
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    public function input($_params)
    {
        $json = json_decode($_params, true);
        if (is_null($json)) {
            return $_params;
        }
        return $json;
    }

    /**
     * 输出到前端，一旦调用该方法，代码将中止继续执行
     * +----------------------------------------------------------
     * @param    mixed $_retBody 返回给前端的数据体
     * @param    int $_retCode 返回码，默认200
     * +----------------------------------------------------------
     * @return void
     * +----------------------------------------------------------
     * @access public
     * @author    zhujili <280000280@qq.com>
     * @date    2014/06/06
     * @mdate   2014/06/06
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    public function output($_retBody, $_retCode = ErrCode::SUCCESS)
    {
        if ($this->__newToken) {
            $retMsg = array("retCode" => $_retCode, "serTime" => time(), "reqId" => $this->__reqId, "token" => $this->__newToken);
        } else {
            $retMsg = array("retCode" => $_retCode, "serTime" => time(), "reqId" => $this->__reqId);
        }
        if (!empty($_retBody) || 0 === $_retBody) {
            $retMsg["retBody"] = $_retBody;
        }
        if ($this->__callback) {
            $ret = $this->__callback . "(" . (SystemSet::OUTPUT_UNICODE ? json_encode($retMsg) : unicode_decode(json_encode($retMsg))) . ")";
        } else {
            $ret = SystemSet::OUTPUT_UNICODE ? json_encode($retMsg) : unicode_decode(json_encode($retMsg));
        }
        echo $ret;
        $this->_afterStartup($retMsg);
        logs("输出：" . (SystemSet::OUTPUT_UNICODE ? unicode_decode($ret) : $ret) . " 耗时：(" . round((microtime_float() - TIME_START) * 1000) . ")毫秒", logger::DEBUG);
        exit();
    }

    /**
     * 接口启动前调用的方法
     * +----------------------------------------------------------
     * @param    mixed $_param 接口输入扩展参数
     * +----------------------------------------------------------
     * @access protected
     * @author    zhujili <280000280@qq.com>
     * @date    2014/06/07
     * @mdate   2014/06/07
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    public function beforeStartup($_param){
    }

    /**
     * TOKEN验证/续期
     * +----------------------------------------------------------
     * @return void
     * +----------------------------------------------------------
     * @access protected
     * @author zhujili <280000280@qq.com>
     * @date    2014/06/06
     * @mdate 2014/06/06
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    protected function _tokenAuth()
    {
        if (!$this->__token || is_null($this->_getSession())) {
            $this->output(null, ErrCode::TOKEN_AUTH_FAIL);
        }
        $time = time();
        $duration = $time - $this->__session["createTime"];
        if (($this->__session["expire"] > 0 && $duration > $this->__session["expire"] / 2) || $duration > 3600) {
            cache("token_" . $this->__token, null);
            $this->__token = $this->__newToken = md5(microtime() . rand(0, 100000));
            $this->__session["createTime"] = $time;
            cache("token_" . $this->__token, $this->__session, SystemSet::SESSION_EXPIRE);
        }
    }

    /**
     * 获取会话对象
     * +----------------------------------------------------------
     * @return mixed
     * +----------------------------------------------------------
     * @access protected
     * @author zhujili <280000280@qq.com>
     * @date    2014/06/06
     * @mdate 2014/06/06
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    protected function _getSession()
    {
        if (!$this->__token) {
            return null;
        }
        if (is_null($this->__session)) {
            $this->__session = cache("token_" . $this->__token);
        }
        if (is_array($this->__session)) {
            return $this->__session["data"];
        }
        return null;
    }

    /**
     * 设置会话对象
     * +----------------------------------------------------------
     * @param    mixed $_data 会话数据对象
     * +----------------------------------------------------------
     * @return void
     * +----------------------------------------------------------
     * @access protected
     * @author    zhujili <280000280@qq.com>
     * @date    2014/06/06
     * @mdate   2014/06/06
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    protected function _setSession($_data)
    {
        if (is_null($this->_getSession())) {
            $this->__token = $this->__newToken = md5(microtime() . rand(0, 100000));
        }
        $this->__session = array("expire" => SystemSet::SESSION_EXPIRE, "createTime" => time(), "data" => $_data);
        cache("token_" . $this->__token, $this->__session, SystemSet::SESSION_EXPIRE);
    }

    /**
     * 删除会话
     * +----------------------------------------------------------
     * @return void
     * +----------------------------------------------------------
     * @access protected
     * @author    zhujili <280000280@qq.com>
     * @date    2014/06/06
     * @mdate   2014/06/06
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    protected function _delSession()
    {
        if ($this->__token) {
            cache("token_" . $this->__token, null);
            $this->__session = $this->__newToken = $this->__token = null;
        }
    }

    /**
     * 接口结束后调用的方法
     * +----------------------------------------------------------
     * @param    mixed $_retMsg 返回给前端的对象
     * +----------------------------------------------------------
     * @access protected
     * @author    zhujili <280000280@qq.com>
     * @date    2014/06/07
     * @mdate   2014/06/07
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    protected function _afterStartup($_retMsg){
    }

    /**
     * 接口结束后调用的方法
     * +----------------------------------------------------------
     * @param    mixed $_retMsg 返回给前端的对象
     * +----------------------------------------------------------
     * @access protected
     * @author    zhujili <280000280@qq.com>
     * @date    2014/06/07
     * @mdate   2014/06/07
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    protected function _getModel($_name){
        return core::load("Model.{$_name}Model");
    }
}

?>