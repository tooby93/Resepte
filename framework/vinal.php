<?php
/*
 * Подключение загрузчика классов
 */
 define('__VINALDIR__',dirname(__FILE__).'/');
 include_once(__VINALDIR__ . '/classes/loader/Loader.php');
 session_start();

//ini_set('display_errors','On');
//error_reporting(E_ALL);

/**
 * Class Vinal
 * @property User $user
 * @property VRender $render
 * @property Mail $mail
 * @property PostOpt $postopt
 * @property Strings $strings
 */
class Vinal{
    public $vf_version = 0.3; //Framework Version

    /**
     * @var Экземпляр текущего класса
     */
    public static $instance;

    /**
     * @var - Name Controller
     */
    public $controller_id;

    /**
     * @var - Name Action
     */
    public $action_id;

    public $_route;


    /**
     * @var VCache
     */
    public $cache;

    /**
     * @var VMysqliDatabase|VDataBase
     */
    public $database;



    public $lang_id;
    public $is_admin = false;



    public static function app(){
        if(!isset(self::$instance))
            self::$instance = new Vinal();
        return self::$instance;
    }

    public function run($controller = '', $action = ''){
        try{
            Loader::I()->run(); //Автозагрузка классов

            //Мультиязычность
            //Кеширование
            if(Vinal::app()->config("general")->cache == true){
                $this->cache = new VCache();
            }
            $this->database = Loader::I()->loadClass('database');

            $route = new VRouting();
            if(VF::app()->user->isAuth()){
                VF::app()->database->update('users', array('last_view' => time()), 'id = '.VF::app()->user->getId());
            }
            if(isset($_GET['lang_id'])){
                if((int)$_GET['lang_id'] > 2 && $_GET['lang_id'] < 1) $_GET['lang_id'] = 1;
                setcookie('lang_id', (int)$_GET['lang_id'], time()+9000000, '/');
                $_COOKIE['lang_id'] = (int)$_GET['lang_id'];
                VF::app()->lang_id = (int)$_GET['lang_id'];
            }

            if($_SERVER['HTTP_CF_CONNECTING_IP'] == '108.46.198.12' || $_SERVER['HTTP_CF_CONNECTING_IP'] == '67.80.30.28'){
                $this->is_admin = true;
            }

            if(preg_match('/android/is', $_SERVER['HTTP_USER_AGENT']) && !preg_match('#index/android#', $_SERVER['REQUEST_URI']) && !preg_match('#auth#', $_SERVER['REQUEST_URI'])){
                if(isset($_GET['na'])){
                    setcookie('no_android_page', 1, time()+9000000, '/');
                }else if(isset($_COOKIE['no_android_page'])){

                }else{
                   $this->redirect('/index/android/?r='.urlencode($_SERVER['REQUEST_URI']));
                }
            }

            if(!isset($_COOKIE['lang_id'])){
                $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
                if($lang == 'ru' || $lang == 'by' || $lang == 'ua' || $lang == 'kz'){
                    $lang_id = 1;
                }else{
                    $lang_id = 2;
                }

                setcookie('lang_id', $lang_id, time()+9000000, '/');
                VF::app()->lang_id = $lang_id;
            }else{
                VF::app()->lang_id = (int)$_COOKIE['lang_id'];
            }



            $route->run($controller, $action);




        }catch (ClassesException $e){
            echo $e->getMessage(); //TODO: Вывод ошибок
        }

    }

    public function __get($name){
        try{

            $object = Loader::I()->loadClass($name);

            if(is_object($object))
                return $object;
            else
                throw new ClassesException("Неудалось загрузить класс $name");
        }catch (ClassesException $e){
            echo $e->getMessage();
        }
    }

    /**
     * Доступ к классу конфигураций
     * @param $name Название конфига
     * @return object Объект конфига
     */
    public function config($name){
        if(isset($this->configs[$name])){
            return $this->configs[$name];
        }else{
            $cname = ucfirst($name);
            $cname = "{$name}Config";



            if(class_exists($cname)){
                $obj = new $cname();
            }else{
                $cname = 'V'.$cname;
                if(class_exists($cname)) $obj = new $cname();
            }



            $this->configs[$name] = $obj;

            return $obj;
        }
    }

    public $configs = array();

    /**
     * Получение версии фрейворка
     * @return float
     */
    public function getVersion(){
        return $this->vf_version;
    }


    public function redirect($url){
        if(!empty($this->current_region)){
            header("Location: /".$this->current_region."$url");
        }
        header("Location: $url");
    }

    public function isAjax(){
        if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
            return true;
        }else{
            return false;
        }
    }

    public function youtubeTime($duration){
        $duration = str_replace('PT', '', $duration);
        $h = explode("H", $duration);
        if(count($h) > 1){
            $hours = (int)$h[0];
            $duration = $h[1];
        }else{
            $hours = 0;
        }

        $m = explode("M", $duration);
        if(count($m) > 1){
            $min = (int)$m[0];
            $duration = $m[1];
        }else{
            $min = 0;
        }


        $ss = '';
        $s_h = ($hours > 1)?'hours':'hour';
        $s_m = ($min > 1)?'mins':'min';
        if($hours > 0)$ss .= $hours.' <span class="small">'.$s_h.'</span> ';
        if($min > 9)$ss .= $min.' <span class="small">'.$s_m.'</span> ';
        else if($min < 9 && $min > 0) $ss .=$min.' <span class="small">'.$s_m.'</span> ';


        return $ss;
    }

}

class VF extends Vinal{

}

function __($name){
    return VF::app()->langs->L($name);
}