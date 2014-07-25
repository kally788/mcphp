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
 * |    Memcached.class.php
 * +------------------------------------------------------------------------------
 * |    CreateTime:    2014/05/22
 * |    ModifyTime:    2014/06/10
 * |    Author:        zhujili <280000280@qq.com>
 * +------------------------------------------------------------------------------
 * |    Description:缓存驱动
 * +------------------------------------------------------------------------------
 */
class Memcached { //类定义开始

	//操作句柄
	private $__mem;

    private $__config = array(
        "CLUSTER" => array(array("127.0.0.1", 11211, 1)), //分布式memcached
        "EXPIRE" => 0 //缓存有效时间 (秒)，0为永不过期
    );

	//构造函数
	public function __construct($_config = null)
    {

        if ($_config && is_array($_config)) {
            $this->__config = array_merge($this->__config, $_config);
        }
        if ( is_null ( $this->__mem ) ) {
            $this->__mem = new Memcache();
            $stats = array();
            foreach( $this->__config["CLUSTER"] as $v ){
                $this->__mem->addServer( $v[0], $v[1]);//, true, $v[2], null, 60
                if(0 == $this->__mem->getServerStatus($v[0], $v[1])){
                    $stats[] = $v;
                }
            }
            if(! empty($stats)){
                if(count($stats) == count($this->__config["CLUSTER"])){
                    error("所有缓存库出现故障", ErrCode::MEMCACHED_CONNECT_ERR);
                }else{
                    logs("部分缓存库出现故障 ： " . json_encode($stats), LogRecord::ERROR);
                }
            }
            $this->__mem->setCompressThreshold ( 2000, 0.2 );
        }
	}

	//析函数关闭memcache所有连接
	public function __destruct() {

        if ( ! empty ( $this->__mem ) ) {
            $this->__mem->close ();
        }
	}

	/**
	 +----------------------------------------------------------
	 * 获取缓存值
	 +----------------------------------------------------------
	 * @param   string $_key 键名
	 +----------------------------------------------------------
	 * @return  object<键值>
	 +----------------------------------------------------------
	 * @author	zhujili <280000280@qq.com>
	 * @date	2014/02/25
	 * @mdate   2014/02/25	
	 * @version 1.1.0.0	
	 +----------------------------------------------------------
	 */
	public function get( $_key ) {

		if ( ! is_null( $this->__mem ) ) {
            return $this->__mem->get ( $_key );
		}
		return null;
	}

	/**
	 +----------------------------------------------------------
	 * 设置缓存值
	 +----------------------------------------------------------
	 * @param   string $key 键名
	 * @param 	string/array/int $value 键值
	 * @param	int    $expire缓存有效时间(秒)，null时为配置的缓存时间，0永不过期
	 +----------------------------------------------------------
	 * @return  void
	 +----------------------------------------------------------
	 * @author	zhujili <280000280@qq.com>
	 * @date	2014/02/25
	 * @mdate   2014/02/25	
	 * @version 1.1.0.0	
	 +----------------------------------------------------------
	 */
	public function set( $_key, $_value, $_expire = NULL ) {

		//设置默认的缓存有效时间(秒)
		if ( is_null ( $_expire ) ) {
            $_expire = $this->__config["EXPIRE"];
		}
		if ( ! is_null($this->__mem)  && ! $this->__mem->add ( $_key, $_value, MEMCACHE_COMPRESSED, $_expire ) ) {
			$this->__mem->set ( $_key, $_value, MEMCACHE_COMPRESSED, $_expire );
		}
	}

	/**
	 +----------------------------------------------------------
	 * 删除缓存值
	 +----------------------------------------------------------
	 * @param   string $key 键名
	 +----------------------------------------------------------
	 * @return  void
	 +----------------------------------------------------------
	 * @author	zhujili <280000280@qq.com>
	 * @date	2014/02/25
	 * @mdate   2014/02/25	
	 * @version 1.1.0.0	
	 +----------------------------------------------------------
	 */
	public function del( $_key ) {
		
		if ( ! is_null($this->__mem) ) {
			$this->__mem->delete ( $_key );
		}
	}

	/**
	 +----------------------------------------------------------
	 * 清除所有缓存值
	 +----------------------------------------------------------
	 * @param   void
	 +----------------------------------------------------------
	 * @return  void
	 +----------------------------------------------------------
	 * @author	zhujili <280000280@qq.com>
	 * @date	2014/02/25
	 * @mdate   2014/02/25	
	 * @version 1.1.0.0
	 +----------------------------------------------------------
	 */
	public function clear( ) {

        if ( ! is_null($this->__mem) ) {
            $this->__mem->flush ( );
        }
	}
}
?>