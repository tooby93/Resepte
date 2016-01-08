<?php


    error_reporting(E_ERROR | E_WARNING | E_PARSE);
    ini_set('display_errors',1);
    header("Content-type: text/html; Charset=UTF-8");
    define('__ROOT__',dirname(__FILE__).'/');
    define('FILES_ROOT',__ROOT__.'files/');
    define('FILE_SIZE',1024);

    if($_SERVER['HTTP_USER_AGENT'] == 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)' && isset($_GET['search']['ingredient_id']) && !empty($_GET['search']['ingredient_id'])){
        header('HTTP/1.0 403 Forbidden');
        die('You are forbidden!');
    }

    $_request_end = substr($_SERVER['REQUEST_URI'],-4, 4);
    if($_request_end == '.mjs' || substr($_SERVER['REQUEST_URI'],-5, 5) == '.mcss'){
        include(__ROOT__.'tpl/min/nginx-mininification.php');
        die();
    }

    include(__ROOT__ . '/framework/vinal.php');

    Vinal::app()->run();