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
 * |    ExampleModel.class.php
 * +------------------------------------------------------------------------------
 * |    CreateTime:    2014/06/12
 * |    ModifyTime:    2014/06/12
 * |    Author:        zhujili <280000280@qq.com>
 * +------------------------------------------------------------------------------
 * |    Description:   Model例子
 * +------------------------------------------------------------------------------
 */
class ExampleModel extends model
{
    public function addData($_data)
    {
        //其它逻辑
        //......

        return $this->_table("test")->_create($_data);
    }


    public function idByData($_id)
    {
        //其它逻辑
        //......

        //读取一条ID为N的记录
        return $this->_table("test")->_eq("id",$_id)->_read();
    }

    public function complexGetData(){

        //其它逻辑
        //......

        //查找id大于1并小于4和字段a包含8的记录,SQL语句为：(`id`>1 and `id`<4) or `a` like '%8%'
        return $this->_table("test")->_groupOr(Model::groupAnd(Model::gt("id",1),Model::lt("id",4)),Model::like("a","%8%"))->_read();
    }

    public function orderLimit()
    {
        //其它逻辑
        //......

        //a字段降序然后读取3条记录
        return $this->_table("test")->_limit(3)->_orderDesc("a")->_read();
    }
} 