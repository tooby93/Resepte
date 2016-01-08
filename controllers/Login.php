<?php
/**
 * Created by PhpStorm.
 * User: Albert
 * Date: 8/5/14
 * Time: 9:26 PM
 */

class LoginController extends VController{
    public function actionAuth(){
        if(isset($_POST['login']) && isset($_POST['password'])){
            echo VF::app()->user->login($_POST['login'], $_POST['password']);
        }
    }
} 