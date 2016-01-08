<?php
/**
 * Created by PhpStorm.
 * User: Albert
 * Date: 10/8/2014
 * Time: 3:06 PM
 */

class ShoppingListController extends VController{
    public function actionIndex(){
        if(VF::app()->user->isAuth()){
            $list = VF::app()->database->sql("SELECT id, name, done FROM user_shopping_list ORDER BY done, date_create DESC")->queryAll();
            $this->render('index', array('list'=>$list));
        }else{
            $this->redirect('/');
        }
    }

    public function actionUpdShoppingList(){
        if(isset($_GET['id']) && (int)$_GET['id'] > 0 && VF::app()->user->isAuth()){
            $user_id = VF::app()->user->getId();
            $id = (int)$_GET['id'];
            $done = (int)$_GET['done'];

            VF::app()->database->query("UPDATE user_shopping_list SET done = '$done' WHERE id = '$id' and user_id = '$user_id'");
        }
    }


    public function actionDelShoppingList(){
        if(VF::app()->user->isAuth() && !empty($_GET['ids'])){
            $user_id = VF::app()->user->getId();
            $ids = trim(mysql_real_escape_string(strip_tags($_GET['ids'])));

            VF::app()->database->query("DELETE FROM user_shopping_list WHERE id in ($ids) and user_id = '$user_id'");
        }
    }

    public function actionAddShoppingList(){
        if(VF::app()->user->isAuth() && !empty($_POST['name'])){
            $user_id = VF::app()->user->getId();
            $name = trim(mysql_real_escape_string(strip_tags($_POST['name'])));

            VF::app()->database->insert('user_shopping_list', array(
                'user_id' => $user_id,
                'name' => $name
            ));
        }
    }
} 