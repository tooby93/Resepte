<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Альберт
 * Date: 05.04.13
 * Time: 23:36
 * To change this template use File | Settings | File Templates.
 */

class VController {
    /**
     * @var - Name Controller
     */
    var $id;

    /**
     * @var VMysqliDatabase|VDataBase
     */
    var $db;

    public function __construct(){
        $this->db = VF::app()->database;

    }

    public function render($view,$params = array(),$return = false){
        return Vinal::app()->render->render($view,$params,$return);
    }

    public function renderPartial($view,$params = array(),$return = false){
        return Vinal::app()->render->renderPartial($view,$params,$return);
    }

    public function redirect($url){
        VF::app()->redirect($url);
    }
}