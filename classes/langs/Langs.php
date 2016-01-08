<?php
/**
 * Created by PhpStorm.
 * User: Albert
 * Date: 9/6/14
 * Time: 11:13 AM
 */

class Langs {
    private $lang = array(
        2 => '/languages/en.php',
    );

    private $cache;

    function L($name){
        if(VF::app()->lang_id == 1) return $name;

        if(empty($this->cache)){
            include(__ROOT__.$this->lang[VF::app()->lang_id]);
            $this->cache = $_lang_array;

        }


        if(isset($this->cache[$name])) return $this->cache[$name];
        return $name;
    }
} 