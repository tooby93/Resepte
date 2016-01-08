<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Альберт
 * Date: 29.03.13
 * Time: 20:41
 * To change this template use File | Settings | File Templates.
 */

class VRouting {
    public static function I(){
        return new VRouting();
    }

    public function run($controller = '', $action = ''){
        if(!empty($controller) && !empty($action)){
            $this->loadMethod($this->loadController($controller), $action);
        }else{
            $request_url = $_SERVER['REQUEST_URI'];
            $routing = Vinal::app()->config('general')->routing;
            $route = explode("/",$request_url);
            VF::app()->_route = $route;
            if($route[1] == 'ru' || $route[1] == 'by' || $route[1] == 'us'){
                header("Location: ".str_replace(array("ru/", 'by/','us/'), array('','',''),$_SERVER['REQUEST_URI']));
                die();
            }

            foreach($routing as $key=>$r){
                if($key == $request_url){
                    $route = explode("/",$r);
                    break;
                }else{
                    $chk_url = str_replace(array('.','?','*','&'),array('\.','\?','\*','\&'),$key);

                    if(preg_match('#<.*?:.*?>#is',$chk_url)){
                        $chk_url = preg_replace('/<(.*?):(.*?)>/is','($2{1,})',$chk_url);

                        if(preg_match("#$chk_url#is",$request_url,$a)){
                            $route = explode("/",$r);
                            preg_match_all('#<(.*?):(.*?)>#is',$key,$b);

                            foreach($b[1] as $k=>$c){
                                if($c == 'controller') $route[0] = $a[$k+1];
                                elseif($c == 'action') $route[1] = $a[$k+1];
                                else $route['params'][$c] = $a[$k+1];
                            }
                            if(count($route) > 0){
                                break;
                            }
                        }
                    }
                }
            }

            if(!empty($route)){
                $controller = $this->loadController($route[0]);
                $controller->id = $route[0];
                Vinal::app()->controller_id = $route[0];
                Vinal::app()->action_id     = $route[1];
                $this->loadMethod($controller,$route[1],$route['params']);
            }else
                throw new ClassesException(404); //TODO: Сделать страницу 404

        }



    }


    public function loadController($name){
        if(empty($name)){
            throw new ClassesException(404); //TODO: Сделать страницу 404
        }
        $name = "{$name}Controller";

        if(class_exists($name)){
            return new $name();
        }else
            throw new ClassesException(404);

    }

    public function loadMethod($controller,$name,$params = array()){
        $name = 'action'.ucfirst($name);


        if(method_exists($controller,$name)){
            if(count($params) > 0){
                $eval = '';
                $i = 0;
                foreach($params as $key=>$val){
                    $i++;
                    $eval .= '$'.$key.' = "'.$val.'"';
                    if($i != count($params)) $eval .= ',';
                }

                eval('$controller->$name('.$eval.');');
            }else{
                $controller->$name();
            }

        }else
            throw new ClassesException(404);
    }
}