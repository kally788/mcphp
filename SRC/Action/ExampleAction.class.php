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
    //基本接口
    ///VIEW/index.php?action=example&method=hello
    public function hello()
    {
        $this->output("hello mcphp!~");
    }

    //带简单类型参数$接口
    ///VIEW/index.php?action=example&method=simpleParams&param=我是参数
    public function simpleParams($_params)
    {
        $ret = "处理参数后返回值：" . $_params;
        $this->output($ret);
    }

    //带复杂类型参数的接口
    ///VIEW/index.php?action=example&method=mixedParams&param={"object":{"key":"我是一个对象"},"array":["我是数组"],"int":123,"string":"我是字符串"}
    public function mixedParams($_params)
    {
        $ret = "分析得到的数据：object:" . $_params["object"]["key"] .",array:" .
            $_params["array"][0] . ",int:" . $_params["int"] . ",string:" . $_params["string"] . "";
        $this->output($ret);
    }

    //返回复杂类型的接口
    ///VIEW/index.php?action=example&method=mixedReturn
    public function mixedReturn()
    {
        $object = array("int"=>123,"string"=>"我是字符串","object"=>array("key"=>"我是一个对象"),"array"=>array("我是数组"));
        $this->output($object);
    }

    //带请求编号的接口，通常是前端用来识别本次请求的
    ///VIEW/index.php?action=example&method=discernReq&reqId=123456
    public function discernReq()
    {
        $this->output(null);//无返回数据
    }

    //记录登录状态的接口
    ///VIEW/index.php?action=example&method=login&param={"user":"zhujili","pwd":123456}
    public function login($_params)
    {
        //此处进行用户名和密码验证。实际情况需要到数据库验证并且密码用MD5加密处理
        if("zhujili" == $_params["user"] && 123456 == $_params["pwd"]){
            //如果登录成功，保存登录状态。
            $this->_setSession(array("user"=>$_params["user"],"data"=>"自己定义的各种数据，也可以没有。但不要保存太多数据到session"));
            //返回登录成功后数据，实际情况一般返回户基本信息之类的数据
            $this->output("登录成功！");
        }
        //登录失败，返回一个错误码，错误码可以在ErrCode中自定义。实际情况可能有用户名错误、密码错误、用户不存在等
        $this->output("登录失败！",ErrCode::LOGIN_ERR);
    }

    //需要登录后才能访问的接口，调用登录接口后，取得token才能调用该接口
    //所有需要登录验证的接口，返回数据中要是否有新的token，如果有，前端要及时替换，否则token会过期
    ///VIEW/index.php?action=example&method=authFunc&token=用户登陆后获得的TOKEN
    public function authFunc()
    {
        //进行token验证，只要调用下面的接口即可
        $this->_tokenAuth();
        //以下是验证通过后的其它逻辑。如果_tokenAuth没通过，不会执行下面的代码，直接返回token验证失败错误码“5”（参考：ErrCode 系统错误码列表）
        $this->output("验证通过了，这里的处理业务逻辑的");
    }

    //退出登录接口
    ///VIEW/index.php?action=example&method=exitLogin&token=用户登陆后获得的TOKEN
    public function exitLogin()
    {
        //进行token验证
        $this->_tokenAuth();
        //退出登录逻辑，直接调用下面的接口清除SESSION即可
        $this->_delSession();
        $this->output("安全退出成功！");
    }

    //-----------------------------------------------------------------------------------------------
    // 数据层操作接口
    //-----------------------------------------------------------------------------------------------

    //添加数据
    //添加1条，/VIEW/index.php?action=example&method=addData&param={"a":888,"b":999,"c":"abc","d":"我是字符串"}
    //添加3条，/VIEW/index.php?action=example&method=addData&param=[{"a":1,"b":1,"c":"aa","d":"aa"},{"a":2,"b":2,"c":"bb","d":"bb"},{"a":3,"b":3,"c":"cc","d":"cc"}]
    public function addData($_params){
        //取得数据层
        $model = $this->_getModel("Example");

        //其它逻辑操作
        //......

        //调用添加数据方法
        if(3 === count($_params)){
            //添加3条记录
            $id = $model->addData($_params);
            $this->output("新添加数据的ID为：" . $id . "," . ($id + 1) . "," . ($id +2));
        }else{
            //添加1条记录
            $id = $model->addData($_params);
            $this->output("新添加数据的ID为：" . $id);
        }
    }


    //查询数据
    ///VIEW/index.php?action=example&method=operateData&param=1
    //根据参数类型决定查询方式，可选值1-8
    public function operateData($_params){
        //取得数据层
        $model = $this->_getModel("Example");

        //其它逻辑操作
        //......

        //根据参数类型决定查询方式
        switch($_params){
            case 1:
                //读取id=1的数据
                $rs = $model->idByData(1);
                $this->output(array("读取ID为1的记录：" => $rs[0]));
                break;
            case 2:
                //复杂查询
                $rs = $model->complexGetData();
                $this->output(array("复杂查询[(`id`>1 and `id`<4) or `a` like '%8%']：" => $rs));
                break;
            case 3:
                //查询3条数据，并按a字段排序
                $rs = $model->orderLimit();
                $this->output(array("a字段降序然后读取3条记录：" => $rs));
                break;
            case 4:
                //读取数据返回为K=>V方式
                $rs = $model->getObj();
                $this->output(array("取得的数据用OBJECT封装，KEY为数据库主键：" => $rs));
                break;
            case 5:
                //更新数据
                $rs1 = $model->idByData(2);
                $model->upObj(2);
                $rs2 = $model->idByData(2);
                $this->output(array("修改ID为2的记录"=>array("修改前：" => $rs1[0], "修改后：" => $rs2[0])));
                break;
            case 6:
                //获取记录数
                $count = $model->dataCount();
                $this->output(array("test表的记录数是："=>$count));
                break;
            case 7:
                //删除数据
                $delCount = $model->delData();
                $this->output(array("删除了的记录数："=>$delCount));
                break;
            case 8:
                //分表操作
                $userId = 43261;//模拟的用户ID，通常是采用用户ID进行分表。也可以用其它的来确定分表规则
                $this->output(array("获得43261用户的split表数据："=>$model->splitData($userId)));
                break;
            default:
                //错误的参数
                $this->output(null, ErrCode::GET_CONDITION_ERR);
        }
    }

    //事务操作，删除一条test表的数据，再插入一条新的数
    ///VIEW/index.php?action=example&method=trans&param=6
    public function trans($_params){
        //取得数据层
        $model = $this->_getModel("Example");

        //其它逻辑操作
        //......

        if($model->continuous($_params)){
            $this->output("事务操作成功");
        }else{
            $this->output("事务操作失败");
        }
    }

    //自定义缓存数据
    ///VIEW/index.php?action=example&method=setCacheData&param=要缓存的数据
    public function setCacheData($_params){
        //直接调用公共方法进行缓存数据即可
        cache("cacheData", $_params, 60);
        $this->output("缓存成功");
    }

    //获取自定义缓存数据
    ///VIEW/index.php?action=example&method=getCacheData
    public function getCacheData(){
        //直接调用公共方法获取缓存即可
        $this->output("缓存：" . cache("cacheData"));
    }

    //上传图片
    ///VIEW/index.php?action=example&method=upload
    //需要自定义file文件参数，HTML中的文本域
    public function upload(){
        $name=time();
        $name.=strrchr($_FILES["file"]["name"],".");
        $type=$_FILES["file"]["type"];
        $size=$_FILES["file"]["size"];
        $tmp_name=$_FILES["file"]["tmp_name"];
        if($_FILES["file"]["error"]>0){
            $this->output("上传文件有误:".$_FILES["file"]["error"]);
        }else{
            if(move_uploaded_file($tmp_name,DIR_STATIC . "/" . $name)){
                $this->output("上传文件名：" . $name . " ,文件类型：" . $type . " ,文件大小：" . ($size/1024) . " ,上传到：" . $tmp_name);
            }else{
                $this->output($name . "上传失败");
            }
        }
    }

    public function beforeStartup($_param){

    }

    protected function _afterStartup($_retMsg){

    }
}

?>