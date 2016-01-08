<?php

class ClassesException extends Exception{
    function __construct($message){
        if($message == 404){
            header("HTTP/1.0 404 Not Found");
            VF::app()->render->renderPartial('errors/404');
            die();
        }
        if($message == 1){
            VF::app()->render->renderPartial('errors/1');
            die();
        }
        $this->message = "<b>Произошла ошибка:</b> $message";
        parent::__construct();
    }
}
