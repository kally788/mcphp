<?php

/**
 * |    Mysql.class.php
 * +------------------------------------------------------------------------------
 * |    CreateTime:    2014/05/22
 * |    ModifyTime:    2014/06/10
 * |    Author:        zhujili <280000280@qq.com>
 * +------------------------------------------------------------------------------
 * |    Description:数据库驱动
 * +------------------------------------------------------------------------------
 */
class Mysql
{
    private $__master = null; //主库
    private $__slave = null; //从库
    private $__queryID = null; //当前查询ID
    private $__errList = array(); //错误列表
    private $__isTrans = 0; //是否处于事务中

    private $__config = array(
        "M" => array(
            "NAME" => "tmp", //主数据库名称
            "USER" => "root", //主数据库用户
            "PASSWORD" => "111111", //主数据库密码
            "HOST" => "127.0.0.1", //主机
            "PORT" => "3306" //端口
        ),
        "S" => array(
            "NAME" => "tmp", //从数据库名称
            "USER" => "root", //从数据库用户
            "PASSWORD" => "111111", //从数据库密码
            "HOST" => "127.0.0.1", //主机
            "PORT" => "3306" //端口
        ),
        "DB_CHARSET" => "utf8" //数据库编码
    );

    /**
     * 构造函数
     * +----------------------------------------------------------
     * @public
     * +----------------------------------------------------------
     * @param object $_config 配置文件
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
     * 拆解函数
     * +----------------------------------------------------------
     * @public
     * +----------------------------------------------------------
     * @author zhujili <280000280@qq.com>
     * @date 2014/05/22
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    public function __destruct()
    {
        $this->__free();
        if (! empty ($this->__slave)) {
            $this->__slave->close();
        }
        if ($this->__config["S"]["HOST"] != $this->__config["M"]["HOST"] || $this->__config["S"]["PORT"] != $this->__config["M"]["PORT"]) {
            if (! empty ($this->__master)) {
                $this->__master->close();
            }
        }
    }

    /**
     * 查询操作
     * +----------------------------------------------------------
     * @param string $_sql 查询语句
     * +----------------------------------------------------------
     * @return object 查询结果
     * +----------------------------------------------------------
     * @author    zhujili <280000280@qq.com>
     * @date 2014/05/22
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    public function select($_sql)
    {
        $this->__connSlave();
        //释放前次的查询结果
        if ( $this->__queryID ){
            $this->__free();
        }
        $this->__queryID = $this->__slave->query($_sql);
        if(false === $this->__queryID){
            $this->__error("select 错误的数据库查询 [ " . $this->__slave->errno . " ] : " . $this->__slave->error . " [ SQL ] : " . $_sql);
            return false;
        }
        return $this->__queryID;
    }

    /**
     * 插入操作
     * +----------------------------------------------------------
     * @param string $_sql 查询语句
     * +----------------------------------------------------------
     * @return int 自增长ID
     * +----------------------------------------------------------
     * @author    zhujili <280000280@qq.com>
     * @date 2014/05/22
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    public function insert($_sql)
    {
        $this->__connMaster();
        //释放前次的查询结果
        if ( $this->__queryID ){
            $this->__free();
        }
        $this->__queryID = $this->__master->query($_sql);
        if(false === $this->__queryID){
            $this->__error("insert 错误的数据库查询 [ " . $this->__master->errno . " ] : " . $this->__master->error . " [ SQL ] : " . $_sql);
            return false;
        }
        return $this->__master->insert_id;
    }

    /**
     * 更删操作
     * +----------------------------------------------------------
     * @param string $_sql 查询语句
     * +----------------------------------------------------------
     * @return int 影响行数
     * +----------------------------------------------------------
     * @author    zhujili <280000280@qq.com>
     * @date 2014/05/22
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    public function modify($_sql)
    {
        $this->__connMaster();
        //释放前次的查询结果
        if ( $this->__queryID ){
            $this->__free();
        }
        $this->__queryID = $this->__master->query($_sql);
        if(false === $this->__queryID){
            $this->__error("modify 错误的数据库查询 [ " . $this->__master->errno . " ] : " . $this->__master->error . " [ SQL ] : " . $_sql);
            return false;
        }
        return $this->__master->affected_rows;
    }


    /**
     * 开始事务
     * +----------------------------------------------------------
     * @return bool
     * +----------------------------------------------------------
     * @author    zhujili <280000280@qq.com>
     * @date 2014/06/10
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    public function startTrans(){
        if(0 === $this->__isTrans){
            $this->__isTrans = 1;
            $this->__errList = array();
            $this->__connMaster();
            $this->__master->autocommit(false);
        }
    }

    /**
     * 提交事务
     * +----------------------------------------------------------
     * @return bool
     * +----------------------------------------------------------
     * @author    zhujili <280000280@qq.com>
     * @date 2014/06/10
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    public function commit(){
        if(1 === $this->__isTrans){
            $this->__isTrans = 0;
            $this->__errList = array();
            $this->__queryID = $this->__master->commit();
            $this->__master->autocommit(true);
            if(false === $this->__queryID){
                $this->__error("commit 提交数据库事务错误 [ " . $this->__master->errno . " ] : " . $this->__master->error);
                return false;
            }
        }
        return true;
    }

    /**
     * 事务回滚
     * +----------------------------------------------------------
     * @return bool
     * +----------------------------------------------------------
     * @author    zhujili <280000280@qq.com>
     * @date 2014/06/10
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    public function rollback(){
        if(1 === $this->__isTrans){
            $this->__isTrans = 0;
            $this->__errList = array();
            $this->__queryID = $this->__master->rollback();
            $this->__master->autocommit(true);
            if(false === $this->__queryID){
                $this->__error("rollback 回滚数据库事务错误 [ " . $this->__master->errno . " ] : " . $this->__master->error);
                return false;
            }
        }
        return true;
    }

    /**
     * 是否产生错误
     * +----------------------------------------------------------
     * @return bool
     * +----------------------------------------------------------
     * @author    zhujili <280000280@qq.com>
     * @date 2014/06/10
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    public function isError()
    {
        if(!empty($this->__errList)){
            return true;
        }
        return false;
    }

    /**
     * 连接从库
     * +----------------------------------------------------------
     * @author    zhujili <280000280@qq.com>
     * @date 2014/05/22
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    private function __connSlave()
    {
        if (empty ($this->__slave)) {
            if ($this->__config["S"]["HOST"] == $this->__config["M"]["HOST"] && $this->__config["S"]["PORT"] == $this->__config["M"]["PORT"]) {
                if (empty($this->__master)) {
                    $this->__connMaster();
                }
                $this->__slave = $this->__master;
            } else {
                $this->__slave = new \mysqli ($this->__config["S"]["HOST"],
                    $this->__config["S"]["USER"],
                    $this->__config["S"]["PASSWORD"],
                    $this->__config["S"]["NAME"],
                    $this->__config["S"]["PORT"]);
                if (mysqli_connect_errno()) {
                    error("从数据库连接失败！" . mb_convert_encoding(mysqli_connect_error(), "UTF-8", "GBK,GB2312"), ErrCode::MYSQL_S_CONNECT_ERR);
                }
                $this->__slave->query('SET NAMES ' . $this->__config["DB_CHARSET"]);
            }
        }
    }

    /**
     * 连接主库
     * +----------------------------------------------------------
     * @author    zhujili <280000280@qq.com>
     * @date 2014/05/22
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    private function __connMaster()
    {
        if (empty ($this->__master)) {
            $this->__master = new \mysqli ($this->__config["M"]["HOST"],
                $this->__config["M"]["USER"],
                $this->__config["M"]["PASSWORD"] ,
                $this->__config["M"]["NAME"],
                $this->__config["M"]["PORT"]);
            if (mysqli_connect_errno()) {
                error("主数据库连接失败！" . mb_convert_encoding(mysqli_connect_error(), "UTF-8", "GBK,GB2312"), ErrCode::MYSQL_M_CONNECT_ERR);
            }
            $this->__master->query("SET NAMES " . $this->__config["DB_CHARSET"]);
        }
    }

    /**
     * 释放查询结果
     * +----------------------------------------------------------
     * @author    zhujili <280000280@qq.com>
     * @date 2014/05/22
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    private function __free() {
        if(is_object($this->__queryID)){
            $this->__queryID->free_result();
        }
        $this->__queryID = null;
    }

    /**
     * 查询错误
     * @param $_e
     */
    private function __error($_e){
        logs($_e, Logger::ERROR);
        $this->__errList[] = $_e;
    }
}

?>