<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Альберт
 * Date: 15.04.13
 * Time: 19:38
 * To change this template use File | Settings | File Templates.
 */

class VCache{
    /**
     * @var Memcache
     */
    private $memcache;

    public function __construct(){
        if(class_exists('Memcache')){
            $this->memcache = new Memcache();
            $config = Vinal::app()->config("cache");
            $this->memcache->connect($config->server,$config->port);
        }
    }

    public function get($key){
        $key = $this->aKey($key);
        return $this->memcache->get($key);
    }

    public function set($key,$value,$time = 300){
        $key = $this->aKey($key);
        $this->memcache->add($key,$value,$time);

    }

    public function delete($key){
        $key = $this->aKey($key);
        $this->memcache->delete($key);
    }

    protected  function aKey($key){
        return md5("VF.$key");
    }
}