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
 * |    Model.class.php
 * +------------------------------------------------------------------------------
 * |    CreateTime:    2014/06/06
 * |    ModifyTime:    2014/06/06
 * |    Author:        zhujili <280000280@qq.com>
 * +------------------------------------------------------------------------------
 * |    Description:控制器基类
 * |        1.CURD操作、事务封装
 * |        2.CURD操作前、操作后预处理接口
 * |        3.按用户ID水平分表
 * |        4.Model之间不允许依赖，重用的逻辑一律用继承和抽象来实现
 * +------------------------------------------------------------------------------
 */
class Model
{
    //等于（=）
    static function eq($_field, $_value){
        return "`" . $_field . "`='" . $_value . "'";
    }

    //大于（>）
    static function gt($_field, $_value){
        return "`" . $_field . "`>'" . $_value . "'";
    }

    //小于（<）
    static function lt($_field, $_value){
        return "`" . $_field . "`<'" . $_value . "'";
    }

    //大于等于（>=）
    static function egt($_field, $_value){
        return "`" . $_field . "`>='" . $_value . "'";
    }

    //小于等于（<=）
    static function elt($_field, $_value){
        return "`" . $_field . "`<='" . $_value . "'";
    }

    //模糊查询 (LIKE)，支持[]^%_*#符号
    static function like($_field, $_value){
        return "`" . $_field . "` LIKE '" . $_value . "'";
    }

    //组合OR条件
    static function groupOr(){
        if(func_get_arg(0) instanceof Model){
            $args = func_get_arg(1);
        }else{
            $args = func_get_args();
        }
        $len = count($args);
        $sql = "(";
        for($i = 0 ;$i < $len; $i++){
            $sql .= $args[$i] ." OR ";
        }
        $sql = trim($sql, ' OR ');
        $sql .= ")";
        return $sql;
    }

    //组合AND条件
    static function groupAnd(){
        if(func_get_arg(0) instanceof Model){
            $args = func_get_arg(1);
        }else{
            $args = func_get_args();
        }
        $len = count($args);
        $sql = "(";
        for($i = 0 ;$i < $len; $i++){
            $sql .= $args[$i] ." AND ";
        }
        $sql = trim($sql, ' AND ');
        $sql .= ")";
        return $sql;
    }

    //数据库驱动
    private $__db = null;
    //表名称
    private $__table = "";
    //升序
    private $__orderDesc = "";
    //降序
    private $__orderAsc = "";
    //升降序先后顺序
    private $__eofOrder = "";
    //查询限制
    private $__limit = "";
    //查询条件
    private $__where = "";

    //构造方法
    public function __construct()
    {
        $this->__db = core::load("Lib.Mysql", DriveConf::$MYSQL);
    }

    //--------------------------------------------------------------------------
    // SQL语句封装方法
    //--------------------------------------------------------------------------

    //清除查询条件
    private function __cleanSql(){
        $this->__table = "";
        $this->__orderDesc = "";
        $this->__orderAsc = "";
        $this->__limit = "";
        $this->__where = "";
    }

    /**
     * 定义SQL语句数据表
     * +----------------------------------------------------------
     * @param string $_tabName 表名称
     * @param int $_splitId 分表ID，通常为用户ID，null时没有分表。默认null
     * @param int $_splitCount 分表数，默认10（表0~表9）
     * +----------------------------------------------------------
     * @return $this 本对象
     * +----------------------------------------------------------
     * @access protected
     * @author zhujili <280000280@qq.com>
     * @date    2014/06/12
     * @mdate   2014/06/12
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    protected function _table($_tabName, $_splitId = null, $_splitCount = 10){
        $this->__cleanSql();
        if(null === $_splitId){
            $this->__table = "`" . SystemSet::TABLE_PREFIX . $_tabName . "`";
        }else{
            $this->__table = "`" . SystemSet::TABLE_PREFIX . $_tabName . ($_splitId % $_splitCount) . "`";
        }
        return $this;
    }

    /**
     * 定义SQL语句升序
     * +----------------------------------------------------------
     * @param string args ... 可变长度参数。要进行升序的字段，多个时传递多个参数即可
     * +----------------------------------------------------------
     * @return $this 本对象
     * +----------------------------------------------------------
     * @access protected
     * @author zhujili <280000280@qq.com>
     * @date    2014/06/12
     * @mdate   2014/06/12
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    protected function _orderDesc(){
        $len = func_num_args();
        $this->__orderDesc = "";
        for($i = 0 ;$i < $len; $i++){
            $this->__orderDesc .= "`" . func_get_arg($i) . "` DESC,";
        }
        $this->__orderDesc = trim($this->__orderDesc, ',');
        $this->__eofOrder = "orderDesc";
        return $this;
    }

    /**
     * 定义SQL语句降序
     * +----------------------------------------------------------
     * @param string args ... 可变长度参数。要进行降序的字段，多个时传递多个参数即可
     * +----------------------------------------------------------
     * @return $this 本对象
     * +----------------------------------------------------------
     * @access protected
     * @author zhujili <280000280@qq.com>
     * @date    2014/06/12
     * @mdate   2014/06/12
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    protected function _orderAsc(){
        $len = func_num_args();
        $this->__orderAsc = "";
        for($i = 0 ;$i < $len; $i++){
            $this->__orderAsc .= "`" . func_get_arg($i) . "` ASC,";
        }
        $this->__orderAsc = trim($this->__orderAsc, ',');
        $this->__eofOrder = "orderAsc";
        return $this;
    }

    /**
     * 定义SQL语句数量限制
     * +----------------------------------------------------------
     * @param int $_start 开始位置
     * @param int $_end 结束位置，null时$_start作为总数量计算。删除、修改数据该参数无效
     * +----------------------------------------------------------
     * @return $this 本对象
     * +----------------------------------------------------------
     * @access protected
     * @author zhujili <280000280@qq.com>
     * @date    2014/06/12
     * @mdate   2014/06/12
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    protected function _limit($_start, $_end = null){
        if(null === $_end){
            $this->__limit = "LIMIT " . $_start;
        }else{
            $this->__limit = "LIMIT " . $_start . "," . $_end;
        }
        return $this;
    }

    /**
     * 等于（=）
     * +----------------------------------------------------------
     * @param string $_field 字段名
     * @param string|int $_value 比较值
     * +----------------------------------------------------------
     * @return $this 本对象
     * +----------------------------------------------------------
     * @access protected
     * @author zhujili <280000280@qq.com>
     * @date    2014/06/12
     * @mdate   2014/06/12
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    protected function _eq($_field, $_value){
       $this->__where = self::eq($_field, $_value);
        return $this;
    }

    /**
     * 大于（>）
     * +----------------------------------------------------------
     * @param string $_field 字段名
     * @param int $_value 比较值
     * +----------------------------------------------------------
     * @return $this 本对象
     * +----------------------------------------------------------
     * @access protected
     * @author zhujili <280000280@qq.com>
     * @date    2014/06/12
     * @mdate   2014/06/12
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    protected function _gt($_field, $_value){
        $this->__where = self::gt($_field, $_value);
        return $this;
    }

    /**
     * 小于（<）
     * +----------------------------------------------------------
     * @param string $_field 字段名
     * @param int $_value 比较值
     * +----------------------------------------------------------
     * @return $this 本对象
     * +----------------------------------------------------------
     * @access protected
     * @author zhujili <280000280@qq.com>
     * @date    2014/06/12
     * @mdate   2014/06/12
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    protected function _lt($_field, $_value){
        $this->__where = self::lt($_field, $_value);
        return $this;
    }

    /**
     * 大于等于（>=）
     * +----------------------------------------------------------
     * @param string $_field 字段名
     * @param int $_value 比较值
     * +----------------------------------------------------------
     * @return $this 本对象
     * +----------------------------------------------------------
     * @access protected
     * @author zhujili <280000280@qq.com>
     * @date    2014/06/12
     * @mdate   2014/06/12
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    protected function _egt($_field, $_value){
        $this->__where = self::egt($_field, $_value);
        return $this;
    }

    /**
     * 小于等于（<=）
     * +----------------------------------------------------------
     * @param string $_field 字段名
     * @param int $_value 比较值
     * +----------------------------------------------------------
     * @return $this 本对象
     * +----------------------------------------------------------
     * @access protected
     * @author zhujili <280000280@qq.com>
     * @date    2014/06/12
     * @mdate   2014/06/12
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    protected function _elt($_field, $_value){
        $this->__where = self::elt($_field, $_value);
        return $this;
    }

    /**
     * 模糊查询 (LIKE)
     * +----------------------------------------------------------
     * @param string $_field 字段名
     * @param string|int $_value 支持[]^%_*#符号
     * +----------------------------------------------------------
     * @return $this 本对象
     * +----------------------------------------------------------
     * @access protected
     * @author zhujili <280000280@qq.com>
     * @date    2014/06/12
     * @mdate   2014/06/12
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    protected function _like($_field, $_value){
        $this->__where = self::like($_field, $_value);
        return $this;
    }

    /**
     * 组合OR条件
     * +----------------------------------------------------------
     * @param string args... 可选长度参数。进行OR比较的多个表达式字符串
     * +----------------------------------------------------------
     * @return $this 本对象
     * +----------------------------------------------------------
     * @access protected
     * @author zhujili <280000280@qq.com>
     * @date    2014/06/12
     * @mdate   2014/06/12
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    protected function _groupOr(){
        $args = func_get_args();
        $this->__where = self::groupOr($this, $args);
        return $this;
    }

    /**
     * 组合AND条件
     * +----------------------------------------------------------
     * @param string args... 可选长度参数。进行AND比较的多个表达式字符串
     * +----------------------------------------------------------
     * @return $this 本对象
     * +----------------------------------------------------------
     * @access protected
     * @author zhujili <280000280@qq.com>
     * @date    2014/06/12
     * @mdate   2014/06/12
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    protected function _groupAnd(){
        $args = func_get_args();
        $this->__where = self::groupAnd($this, $args);
        return $this;
    }

    //--------------------------------------------------------------------------
    // CURD方法
    //--------------------------------------------------------------------------

    /**
     * 插入数据
     * +----------------------------------------------------------
     * @param array $_options 要插入的数据对象，二维数组可插入多条
     * @param bool $_replace 是否采用replace方式插入
     * +----------------------------------------------------------
     * @return int|boot 插入记录的自增ID，如果插入多条，返回第一条的ID。失败时返回false
     * +----------------------------------------------------------
     * @access protected
     * @author zhujili <280000280@qq.com>
     * @date    2014/06/08
     * @mdate   2014/06/08
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    protected function _create(array $_options, $_replace = false)
    {
        if(false === $this->_beforeCreate($_options) || empty( $_options ) || empty($this->__table)){
            return false;
        };
        $sql = ($_replace ? "REPLACE" : "INSERT") . " INTO {$this->__table} ";
        if (isset($_options[0]) && is_array($_options[0])) {
            //合拼不一致的字段
            $field_value = array();
            foreach ($_options as $item) {
                $field_value = array_merge($field_value, $item);
            }
            //获得完整的字段
            $field = array_keys($field_value);
            $sql .= "(`" . implode("`,`", $field) . "`) VALUES ";
            //把值设置为字段名，保证不一致的插入使用默认值
            foreach ($field_value as $k => $v) {
                $field_value[$k] = "`" . $k . "`";
            }
            //创建批量插入的数据
            foreach ($_options as $item) {
                foreach ($item as $k => $v) {
                    $item[$k] = "'" . addslashes($v) . "'";
                }
                $sql .= "(" . implode(",", array_values(array_merge($field_value, $item))) . "),";
            }
            $sql = trim($sql, ',');
        } else {
            foreach ($_options as $k => $v) {
                $_options[$k] = addslashes($v);
            }
            $field = array_keys($_options);
            $value = array_values($_options);
            $sql .= "(`" . implode("`,`", $field) . "`) values ('" . implode("','", $value) . "')";
        }
        $result = $this->__db->insert($sql);
        $this->__cleanSql();
        if(false === $this->_afterCreate($_options)){
            return false;
        };
        return $result;
    }

    /**
     * 更新数据
     * +----------------------------------------------------------
     * @param array $_options 要更新的数据
     * +----------------------------------------------------------
     * @return int|boot 影响的行数。失败时返回false
     * +----------------------------------------------------------
     * @access protected
     * @author zhujili <280000280@qq.com>
     * @date    2014/06/11
     * @mdate   2014/06/11
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    protected function _update(array $_options)
    {
        if(false === $this->_beforeUpdate($_options) || empty( $_options ) || empty($this->__table)){
            return false;
        };
        $sql = "";
        foreach ($_options as $field => $value) {
            if(0 === stripos($value, $field)){
                $sql .= "`" . $field . "`=" . $value . ",";
            }else{
                $sql .= "`" . $field . "`='" . $value . "',";
            }
        }
        $sql = "UPDATE {$this->__table} SET " . trim($sql, ',');
        if(empty($this->__where)){
            $sql .= " WHERE 1";
        }else{
            $sql .= " WHERE " . $this->__where;
        }
        if(! empty($this->__orderDesc) || ! empty($this->__orderAsc)){
            if("orderDesc" == $this->__eofOrder){
                $sql .= " ORDER BY " . ($this->__orderAsc? ($this->__orderAsc . "," . $this->__orderDesc) : $this->__orderDesc);
            }else{
                $sql .= " ORDER BY " . ($this->__orderDesc? ($this->__orderDesc . "," . $this->__orderAsc) : $this->__orderAsc);
            }
            $sql = trim($sql, ',');
        }
        if(! empty($this->__limit)){
            $tmp = explode(",", $this->__limit);
            $sql .= " " . $tmp[0];
        }
        $result = $this->__db->modify($sql);
        $this->__cleanSql();
        if(false === $this->_afterUpdate($_options)){
            return false;
        };
        return $result;
    }

    /**
     * 读取数据
     * +----------------------------------------------------------
     * @param array|string $_field 查询的字段数组，空数组|*|null|时返回全部字段，count返回记录数。默认*
     * @param string $_primary 按该字段作为key返回集合（通常是主键字段），null时返回数组，默认null
     * @param int $_cache 缓存时间/秒，0时永不过期（仅在对数据更新不需要事实时的才采用），null时不缓存，默认null
     * +----------------------------------------------------------
     * @return array|boot 数据集合，没有$_primary参数时，返回的是一个连续索引的数组。失败时返回false
     * +----------------------------------------------------------
     * @access protected
     * @author zhujili <280000280@qq.com>
     * @date    2014/06/11
     * @mdate   2014/06/11
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    protected function _read($_field = "*", $_primary = null, $_cache = null)
    {
        if(false === $this->_beforeRead($_field, $_primary, $_cache) || empty($this->__table)){
            return false;
        };
        if("count" == $_field){
            $sql = "SELECT COUNT(*) FROM {$this->__table}";
        }else{
            $sql = "SELECT " . ( "*" == $_field || empty($_field) ?  "*" : implode(",", $_field)) . " FROM {$this->__table}";
        }

        if(empty($this->__where)){
            $sql .= " WHERE 1";
        }else{
            $sql .= " WHERE " . $this->__where;
        }
        if(! empty($this->__orderDesc) || ! empty($this->__orderAsc)){
            if("orderDesc" == $this->__eofOrder){
                $sql .= " ORDER BY " . ($this->__orderAsc? ($this->__orderAsc . "," . $this->__orderDesc) : $this->__orderDesc);
            }else{
                $sql .= " ORDER BY " . ($this->__orderDesc? ($this->__orderDesc . "," . $this->__orderAsc) : $this->__orderAsc);
            }
            $sql = trim($sql, ',');
        }
        if(! empty($this->__limit)){
            $sql .= " " . $this->__limit;
        }
        $this->__cleanSql();
        if(null !== $_cache && is_int($_cache)){
            $data = cache(md5($sql . $_primary));
            if(!empty($data)){
                if(false === $this->_afterRead($_field, $_primary, $_cache)){
                    return false;
                };
                return $data;
            }
        }
        $result = $this->__db->select($sql);
        $data = false;
        if(false !== $result){
            if("count" == $_field){
                $tmp = $result->fetch_row();
                $data = (int)$tmp[0];
            }else{
                $data = array();
                if (is_null($_primary)) {
                    while ($rs = $result->fetch_array(1)) {
                        $data [] = $rs;
                    }
                } else {
                    while ($rs = $result->fetch_array(1)) {
                        $data [$rs[$_primary]] = $rs;
                    }
                }
            }
            if(null !== $_cache && is_int($_cache)){
                cache(md5($sql . $_primary), $data, $_cache);
            }
        }
        if(false === $this->_afterRead($_field, $_primary, $_cache)){
            return false;
        };
        return $data;
    }

    /**
     * 删除数据
     * +----------------------------------------------------------
     * @return int|boot 影响记录数，失败时返回false
     * +----------------------------------------------------------
     * @access protected
     * @author zhujili <280000280@qq.com>
     * @date    2014/06/11
     * @mdate   2014/06/11
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    protected function _delete()
    {
        if(false === $this->_beforeDelete() || empty($this->__table)){
            return false;
        };
        $sql = "DELETE FROM {$this->__table}";
        if(empty($this->__where)){
            $sql .= " WHERE 1";
        }else{
            $sql .= " WHERE " . $this->__where;
        }
        if(! empty($this->__orderDesc) || ! empty($this->__orderAsc)){
            if("orderDesc" == $this->__eofOrder){
                $sql .= " ORDER BY " . ($this->__orderAsc? ($this->__orderAsc . "," . $this->__orderDesc) : $this->__orderDesc);
            }else{
                $sql .= " ORDER BY " . ($this->__orderDesc? ($this->__orderDesc . "," . $this->__orderAsc) : $this->__orderAsc);
            }
            $sql = trim($sql, ',');
        }
        if(! empty($this->__limit)){
            $tmp = explode(",", $this->__limit);
            $sql .= " " . $tmp[0];
        }
        $result = $this->__db->modify($sql);
        $this->__cleanSql();
        if(false === $this->_afterDelete()){
            return false;
        };
        return $result;
    }

    //--------------------------------------------------------------------------
    // 事务
    //--------------------------------------------------------------------------

    /**
     * 开始事务
     * +----------------------------------------------------------
     * @access protected
     * @author zhujili <280000280@qq.com>
     * @date    2014/06/11
     * @mdate   2014/06/11
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    protected function _startTrans()
    {
        $this->__db->startTrans();
    }

    /**
     * 提交事务
     * +----------------------------------------------------------
     * @return boot 成功返回true，失败返回false
     * +----------------------------------------------------------
     * @access protected
     * @author zhujili <280000280@qq.com>
     * @date    2014/06/11
     * @mdate   2014/06/11
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    protected function _commit()
    {
        return $this->__db->commit();
    }

    /**
     * 回滚事务
     * +----------------------------------------------------------
     * @return boot 成功返回true，失败返回false
     * +----------------------------------------------------------
     * @access protected
     * @author zhujili <280000280@qq.com>
     * @date    2014/06/11
     * @mdate   2014/06/11
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    protected function _rollback()
    {
        return $this->__db->rollback();
    }

    /**
     * 获取事务过程是否产生了错误
     * +----------------------------------------------------------
     * @return boot 无错true，出现错误false
     * +----------------------------------------------------------
     * @access protected
     * @author zhujili <280000280@qq.com>
     * @date    2014/06/11
     * @mdate   2014/06/11
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    protected function _isError(){
        return $this->__db->isError();
    }

    //--------------------------------------------------------------------------
    // 预处理接口
    //--------------------------------------------------------------------------

    /**
     * 创建数据前预处理接口
     * +----------------------------------------------------------
     * @param array $_options 要创建的数据
     * +----------------------------------------------------------
     * @return boot 返回false时，终止创建数据
     * +----------------------------------------------------------
     * @access protected
     * @author zhujili <280000280@qq.com>
     * @date    2014/06/11
     * @mdate   2014/06/11
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    protected function _beforeCreate(array $_options)
    {
    }

    /**
     * 创建数据后预处理接口
     * +----------------------------------------------------------
     * @param array $_options 要创建的数据
     * +----------------------------------------------------------
     * @return boot 返回false时，终止返回创建数据结果
     * +----------------------------------------------------------
     * @access protected
     * @author zhujili <280000280@qq.com>
     * @date    2014/06/11
     * @mdate   2014/06/11
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    protected function _afterCreate(array $_options)
    {
    }

    /**
     * 更新数据前预处理接口
     * +----------------------------------------------------------
     * @param array $_options 要更新的数据
     * +----------------------------------------------------------
     * @return boot 返回false时，终止更新数据
     * +----------------------------------------------------------
     * @access protected
     * @author zhujili <280000280@qq.com>
     * @date    2014/06/11
     * @mdate   2014/06/11
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    protected function _beforeUpdate(array $_options)
    {
    }

    /**
     * 更新数据后预处理接口
     * +----------------------------------------------------------
     * @param array $_options 要更新的数据
     * +----------------------------------------------------------
     * @return boot 返回false时，终止返回更新数据结果
     * +----------------------------------------------------------
     * @access protected
     * @author zhujili <280000280@qq.com>
     * @date    2014/06/11
     * @mdate   2014/06/11
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    protected function _afterUpdate(array $_options)
    {
    }

    /**
     * 查询数据前预处理接口
     * +----------------------------------------------------------
     * @param array|string $_field
     * @param string $_primary
     * @param int $_cache
     * +----------------------------------------------------------
     * @return boot 返回false时，终止查询数据
     * +----------------------------------------------------------
     * @access protected
     * @author zhujili <280000280@qq.com>
     * @date    2014/06/11
     * @mdate   2014/06/11
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    protected function _beforeRead($_field, $_primary, $_cache)
    {
    }

    /**
     * 查询数据后预处理接口
     * +----------------------------------------------------------
     * @param array|string $_field
     * @param string $_primary
     * @param int $_cache
     * +----------------------------------------------------------
     * @return boot 返回false时，终止返回查询数据结果
     * +----------------------------------------------------------
     * @access protected
     * @author zhujili <280000280@qq.com>
     * @date    2014/06/11
     * @mdate   2014/06/11
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    protected function _afterRead($_field, $_primary, $_cache)
    {
    }

    /**
     * 删除数据前预处理接口
     * +----------------------------------------------------------
     * @return boot 返回false时，终止删除数据
     * +----------------------------------------------------------
     * @access protected
     * @author zhujili <280000280@qq.com>
     * @date    2014/06/11
     * @mdate   2014/06/11
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    protected function _beforeDelete()
    {
    }

    /**
     * 删除数据后预处理接口
     * +----------------------------------------------------------
     * @return boot 返回false时，终止返回删除数据结果
     * +----------------------------------------------------------
     * @access protected
     * @author zhujili <280000280@qq.com>
     * @date    2014/06/11
     * @mdate   2014/06/11
     * @version 1.0.0.0
     * +----------------------------------------------------------
     */
    protected function _afterDelete()
    {
    }

}

?>