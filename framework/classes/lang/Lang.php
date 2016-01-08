<?php
/**
 * Created by JetBrains PhpStorm.
 * User: admin
 * Date: 9/7/13
 * Time: 5:05 PM
 * To change this template use File | Settings | File Templates.
 */

class VLang {

    private $_language;
    public $lang = 'ru';

    public function load($name, $name2 = ''){

        if(empty($_language)){
            if(file_exists(__VINALDIR__."../languages/".$this->lang.".php")){
                include(__VINALDIR__."../languages/".$this->lang.".php");

                $this->_language = $__language;
            }

        }

        if(!empty($name2)){
                if(isset($this->_language[$name][$name2])){
                    return $this->_language[$name][$name2];
                }else{
                    return false;
                }
        }
        return $this->_language[$name];

    }
}