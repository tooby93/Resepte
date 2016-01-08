<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Альберт
 * Date: 05.04.13
 * Time: 23:43
 * To change this template use File | Settings | File Templates.
 */


    class GeneralConfig extends VGeneralConfig{
        public $routing = array(
            '/r/<id:[0-9]>' => 'index/r',
            '/halloween' => 'index/index',
            '/recipe/<id:[0-9]>' => 'recipe/index',
            '/<controller:[a-z]>/view/<id:[0-9]>' => '<controller>/view',
            '/<controller:[a-z]>/<action:[a-z]>' => '<controller>/<action>',
            '/<controller:[a-z]>' => '<controller>/index',
            '/' => 'index/index',
        );

        public $layout = 'main';
        public $project_name = 'Resepte';
        public $cache = true;


        public $region_lang = array(
            'ru' => 'ru',
            'by' => 'ru',
            'us' => 'en'
        );

        public $default_city = array(
            1 => 1,
            2 => 2,
            3 => 3
        );




}

