<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Альберт
 * Date: 29.03.13
 * Time: 20:24
 * To change this template use File | Settings | File Templates.
 */

class VGeneralConfig{
    /**
     * Роутинг
     */
    public $routing = array(
        '/' => 'index/index',
        '/<controller:[a-z]>/<action:[a-z]>' => '<controller>/<action>',
        '/<controller:[a-z]>' => '<controller>/index'
    );

    /**
     * @var string - Папка шаблона по-умолчанию
     */
    public $templates_dir = 'templates/<controller>/';

    /**
     * @var string - Layout
     */
    public $layout = '';

    /**
     * Кеширование (настраивается в CacheConfig)
     * @var bool
     */

    public $cache = false;
}