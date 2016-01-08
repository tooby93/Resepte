<?php
class Loader{
    /**
     * Имя последнего загрузившегося класса
     * @var string
     */

    private $_last_class = array();

    /**
     * @var Хранение классов
     */
    public static $_classes;


    /**
     * @var Экземпляр текущего класса
     */
    public static $instance;

    public static function I(){
        return new Loader();
    }

    public function run(){
        spl_autoload_register(array($this,'autoClasses'));
    }

    /**
     * Загружает класс по пути и названию, возпращает класс или false
     * @param $path Путь
     * @param $name Имя
     * @return bool
     */

    public function newObject($path,$name){
        //Проверяем существует ли файл, если да, то существует ли класс, если нет подгружаем файл
        if(file_exists($path) && !class_exists($name)){
            include_once($path);
            return new $name();
        }

        //Проверяем существует ли класс
        if(class_exists($name)){

            return new $name();
        }

        return false;
    }



    private function loadDataBase(){
        $config = Vinal::app()->config("database");
        $name = ucfirst($config->dbType);

        $file_name_root  = __ROOT__."classes/database/types/$name.php";
        $file_name_vinal = __VINALDIR__."classes/database/types/$name.php";

        $name = "{$name}DataBase";

        if(($obj = $this->newObject($file_name_root,$name.'_')) != false){
            return $obj;
        }

        if(($obj = $this->newObject($file_name_vinal,$name)) != false){
            return $obj;
        }

    }

    public function loadClass($name){
        if($name == 'database'){
            //Получаем тип базы данных
            $config = Vinal::app()->config("database");
            $dbType = $config->dbType;
            $name = "{$dbType}DataBase";
        }


        $name = ucfirst($name);
        $this->autoClasses($name);

        if(class_exists($name)){}
        elseif(class_exists('V'.$name)){
            $name = 'V'.$name;
        }else{return false;}


        if(isset(self::$_classes[$name])){ return self::$_classes[$name];}
        $class = new $name();

        self::$_classes[$name] = $class;
        return $class;

    }



    /**
     * Автозагрузка классов
     */
    public function autoClasses($name){
        if(empty($name)) return false;
        $dir = '';
        $class_name = '';
        $only_vinal = 0;


        if(substr($name,-6) == 'Config'){
            $dir = 'configs/';
            $class_name = $name;
            $file_name = ucfirst(str_replace('Config','',$name)).'.php';

        }
        elseif(substr($name,-8) == 'DataBase'){
            $dir = 'classes/database/';
            $class_name = $name;
            $file_name = str_replace('DataBase','',$name).'.php';
        }
        elseif(substr($name,-10) == 'Controller' && $name != 'VController'){
            $dir = 'controllers/';
            $class_name = $name;
            $file_name = str_replace('Controller','',ucfirst($name)).'.php';

        }
        elseif(substr($name,-9) == 'Exception'){
            $dir = __VINALDIR__.'classes/exceptions/';
            $class_name = $name;
            $file_name = str_replace('Exception','',$name).'.php';
            $only_vinal = 1;
        }else{


            $dir = (substr($name,0,1) == 'V')?substr($name,1):$name;
            $dir = 'classes/'.strtolower($dir).'/';
            $class_name = $name;
            $file_name = $name.'.php';



            preg_match_all('/([A-Z])/',$class_name,$a);

            if(count($a[1]) > 1 && $a[1][0] != 'V'){
                $class_name[strpos($class_name,$a[1][0])] = strtolower($a[1][0]);
                $a = $a[1][1];

                $class = ucfirst(substr($class_name,0,strpos($class_name,$a)));
                $general_class = substr($class_name,strpos($class_name,$a));

                $class_name = $a;
                $dir = 'classes/'.strtolower($general_class).'/';
                $file_name = strtolower($class).'.php';


            }
        }



        if(substr($name,0,1) == 'V'){
            $class_name = $name;
            $dir = __VINALDIR__.$dir;
            $file_name = substr($file_name,1);
            $only_vinal = 1;
        }



        if($only_vinal != 1 and !class_exists($class_name) and file_exists(__ROOT__.$dir.$file_name)){
            include(__ROOT__.$dir.$file_name);

            if(!empty($class_name))
                $this->_last_class[] = $class_name;

            return $class_name;
        }



        if($only_vinal != 1){
            $class_name = "V$class_name";
            $only_vinal = 1;
            $dir = __VINALDIR__.$dir;

        }


        if($only_vinal == 1 and !class_exists($class_name) and file_exists($dir.$file_name)){

            include($dir.$file_name);
            if(!empty($class_name))
                $this->_last_class[] = $class_name;
            return $class_name;
        }

    }

}
