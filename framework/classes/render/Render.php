<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Альберт
 * Date: 05.04.13
 * Time: 23:36
 * To change this template use File | Settings | File Templates.
 */

class VRender {
    public $title = '';

    public function render($view,$params = array(), $return = false){
        $config = Vinal::app()->config("general");
        $tpl_dir = $config->templates_dir;
        $layout_dir = __ROOT__.str_replace(array('<controller>','//'),array('',''),$tpl_dir).'/layouts/';
        $tpl_dir = str_replace(array('<controller>'),array(Vinal::app()->controller_id),$tpl_dir);

        $tpl_file = __ROOT__.$tpl_dir.$view.'.php';
        if(!empty($config->layout)) $layout_file = $layout_dir.$config->layout.'.php';


        $render = $this->load($tpl_file,$params);


        if(!empty($layout_file)){
            $params['render'] = $render;
            $content = $this->load($layout_file,$params);
        }else
            $content = $render;
        $content = str_replace(array("\n", "\r", "\t"), array("", "", ""), $content);
        $content = preg_replace('/ {2,}/',' ',$content);;


        if($return == true) return $content;
        else echo $content;
    }


    public function renderPartial($view,$params = array(), $return = false){
        $config = Vinal::app()->config("general");
        $tpl_dir = $config->templates_dir;

        if(preg_match('/errors/ius', $view)){
            $tpl_dir = str_replace(array('<controller>'),array(''),$tpl_dir);
        }else{
            $tpl_dir = str_replace(array('<controller>'),array(Vinal::app()->controller_id),$tpl_dir);
        }


        $tpl_file = __ROOT__.$tpl_dir.$view.'.php';

        $render = $this->load($tpl_file,$params);

        if($return == true) return $render;
        else echo $render;
    }

    private function load($file,$params = array()){
        if(file_exists($file)){
            ob_start();
            foreach($params as $key => $val) $$key = $val;
            include($file);
            $c = ob_get_clean();
            $c = $this->renderLang($c);
            return $c;
        }
    }

    private function renderLang($content){
        preg_match_all('#{\*(.*?)\*}#', $content, $langes);

        if(count($langes) > 0){
            foreach($langes[1] as $l){
                $ll = explode("-", $l);
                $nn = '';
                if(count($ll) == 2) $nn = VF::app()->lang->load($ll[0], $ll[1]);
                else $nn = VF::app()->lang->load($ll[0]);

                $content = str_replace('{*'.$l.'*}', $nn, $content);
            }
        }

        return $content;
    }

    public function setTitle($title){
        $this->title = $title;
    }

    public function getTitle(){
        return $this->title;
    }
}