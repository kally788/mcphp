<?php

/**
 * |    ExampleAction.class.php
 * +------------------------------------------------------------------------------
 * |    CreateTime:    2014/06/12
 * |    ModifyTime:    2014/06/12
 * |    Author:        zhujili <280000280@qq.com>
 * +------------------------------------------------------------------------------
 * |    Description:   Action例子
 * +------------------------------------------------------------------------------
 */

class ExampleAction extends action
{
    public function test($_params)
    {
        $this->output("hello mcphp!~");
    }

    public function beforeStartup($_param){

    }

    protected function _afterStartup($_retMsg){

    }
}

?>