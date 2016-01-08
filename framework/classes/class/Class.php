<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Альберт
 * Date: 15.04.13
 * Time: 20:25
 * To change this template use File | Settings | File Templates.
 */

class VClass {

    public function loadClass($name, $general){
        $file = dirname(__FILE__).strtolower($name).'.php';

        if(file_exists($file)){
            include($file);
            if(class_exists($name.$general)){
                return true;
            }
        }

        return false;
    }
}