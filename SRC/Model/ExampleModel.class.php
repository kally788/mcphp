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

        //读取一条ID为$_id的记录
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
        return $this->_table("test")->_orderDesc("a")->_limit(3)->_read();
    }

    public function getObj()
    {
        //其它逻辑
        //......

        //读取为OBJECT
        return $this->_table("test")->_read("*", "id");
    }

    public function upObj($_id)
    {
        //其它逻辑
        //......

        //修改ID为$_id的记录,d字段附上当前时间戳
        return $this->_table("test")->_eq("id",$_id)->_update(array("c"=>"efg", "d"=>"时间戳:" . time()));
    }

    public function dataCount(){
        //其它逻辑
        //......

        //获取数据量记录数
        return $this->_table("test")->_read("count");
    }

    public function delData(){
        //其它逻辑
        //......

        //刪除ID大于等于5的记录
        return $this->_table("test")->_egt("id",5)->_delete();
    }

    public function splitData($_id)
    {
        //其它逻辑
        //......

        //根据用户ID进行分表定位
        return $this->_table("split", $_id, 10)->_read();
    }

    public function continuous($_id){
        //其它逻辑
        //......

        //开始事务
        $this->_startTrans();
        $this->_table("test")->_eq("id",$_id)->_delete();//删除一条记录
        //$this->_table("testxxxx")->_eq("id",$_id)->_delete();//去掉本句的注释，事务将回滚，因为表不存在
        $this->_table("test")->_create(array("a"=>33,"b"=>33,"c"=>"cc","d"=>"cc"));//插入一条记录
        if($this->_isError()){
            //出现错误
            $this->_rollback();
            return false;
        }else{
            //正常提交
            $this->_commit();
            return true;
        }
    }

} 