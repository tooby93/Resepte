<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Альберт
 * Date: 17.04.13
 * Time: 11:34
 * To change this template use File | Settings | File Templates.
 */

class User {

    /**
     * Проверка авторизации
     * @return bool
     */
    public function isAuth(){
        if($_SESSION['user_id'] > 0){
            return true;
        }else{
            return false;
        }

    }



    public function isAdmin(){
        if($this->getId() == 1 || $this->getId() == 12){
            return true;
        }else return false;
    }

    public function getName($id = 0){
        if($id == 0) $id = $this->getId();

        $u = VF::app()->database->sql("SELECT last_name,first_name FROM users WHERE id = '$id'")->queryRow();


        if(!empty($u['first_name']) || !empty($u['last_name'])){
            $name = $u['first_name'].' '.$u['last_name'];
        }

        return $name;
    }

    public function getUserInfo($id = 0){
        if($id == 0) $id = $this->getId();

        $u = VF::app()->database->sql("SELECT last_name,first_name,avatar FROM users WHERE id = '$id'")->queryRow();
        return $u;
    }

    /**
     * @param $sum
     * @param $type 1 - Пополнение WM, 2-Пополнение robokassa, 3-Выплата, 4 - Оплата по партнерской программе, 5 - Оплата за статью, 6 - Покупка статьи
     * @param $user_id
     */

    public function addMoneyHistory($sum, $type, $user_id){
        VF::app()->database->insert('t_money_history', array(
            'user_id' => $user_id,
            'sum' => $sum,
            'type' => $type,
            'time' => date('Y-m-d H:i:s')
        ));
    }

    /**
     * Получение E-mail пользователя
     * @return mixed
     */
    public function getEmail(){
        return VF::app()->database->sql("SELECT email FROM users WHERE id = ".$this->getId())->queryScalar();
    }

    /**
     * Авторизация
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function login($email, $password){
        if(empty($email) || empty($password) || !filter_var($email, FILTER_VALIDATE_EMAIL)) return 0;

        $password = $this->hash($password);

        $u = VF::app()->database->sql("SELECT id FROM users WHERE email = '$email' and password ='$password'")->queryRow();
        if(!empty($u)){
            $_SESSION['user_id'] = $u['id'];
            $_SESSION['email'] = $email;
            return 1;
        }else{
            return 2;
        }
    }

    public function new_user($email, $password, $first_name = '', $last_name = ''){
        $password = $this->hash($password);

        $ref = 0;
        if(isset($_COOKIE['ref'])){
            $ref = $_COOKIE['ref'];
        }

        VF::app()->database->query("INSERT IGNORE INTO t_users (email, password, ref, first_name, last_name) VALUES ('$email', '$password', '$ref', '$first_name', '$last_name')");
        return 1;
    }

    /**
     * Получаем client из куки
     */
    public function getClient()
    {
        if (isset($_COOKIE['client']) and !empty($_COOKIE['client'])) {
            return urlencode($_COOKIE['client']);
        } else {
            unset($_SESSION);
            VF::app()->redirect('/?action=user&method=auth');
        }
        return "";
    }

    /**
     * Получение Id пользователя
     * @return int
     */
    public function getId(){
        return (int)$_SESSION['user_id'];
    }

    public function hash($s){
        return sha1(md5($s));
    }



}