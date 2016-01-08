<?php
    class VDataBaseConfig{
        /**
         * @var string Тип базы данных
         */
        public $dbType = 'Mysqli';
        /**
         * @var string Имя сервера базы данных
         */
        public $dbHost = 'localhost';

        /**
         * @var string Пользователь
         */
        public $dbUser = '';

        /**
         * @var string Пароль
         */
        public $dbPassword = '';

        /**
         * @var string Имя базы данных
         */
        public $dbBase = '';
    }
?>