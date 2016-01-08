<?php

class VMysqliDatabase extends VDatabase{
    /**
     * @var Хранить экземпляр подключения
     */
    private static $i;

    /**
     * Хранит копию конфигурации базы
     * @var object
     */
    protected $config;


    public function __construct(){
        if(!isset($this->config)){
            $this->config = Vinal::app()->config("database");
        }

        if(!isset(self::$i))
            self::$i = new mysqli($this->config->dbHost,$this->config->dbUser,$this->config->dbPass,$this->config->dbBase);

        self::$i->query("SET NAMES utf8");
    }



    public function queryRow(){
        $result = self::$i->query($this->sql);
        if(is_object($result)){
            return $result->fetch_assoc();
        }else{
            if(!empty(self::$i->error))
                throw new Exception(self::$i->error);
        }

        return null;

    }

    public function queryScalar(){
        $result = self::$i->query($this->sql);
        if(is_object($result)){
            $r = $result->fetch_row();
            return $r[0];
        }else{
            if(!empty(self::$i->error))
                throw new Exception(self::$i->error);
        }

        return null;
    }

    public function queryAll(){
        $result = self::$i->query($this->sql);
        if(is_object($result)){
            for ($res = array(); $tmp = $result->fetch_assoc();) $res[] = $tmp;

            return $res;
        }else{
            if(!empty(self::$i->error))
                throw new Exception(self::$i->error);
        }

        return null;
    }

    public function query($sql){
        $r = self::$i->query($sql);
        if($r) return $r;
        else throw new Exception(self::$i->error);
    }

    public function getLastId(){
        return self::$i->insert_id;
    }

    public function escape($s){
        return self::$i->escape_string($s);
    }

    public function insert($table_name,$params, $ignore = false, $duplicate = null){
        if($ignore == true){
            $i = ' IGNORE ';
        }else{
            $i = '';
        }

        $SQL = 'INSERT '.$i.' INTO '.$table_name.' SET ';
        $i = 0;
        foreach($params as $key=>$val){
            $i++;
            $SQL .= ' `'.$key.'`="'.self::$i->real_escape_string($val).'"';
            if($i != count($params)) $SQL .= ',';

        }

        if(count($duplicate) > 0){
            $SQL .= " ON DUPLICATE KEY UPDATE ";
            foreach($duplicate as $key=>$val){
                if(preg_match('#\+#is', $val)){
                    $SQL .= ' '.$key.'='.self::$i->real_escape_string($val).',';
                }else{
                    $SQL .= ' '.$key.'="'.self::$i->real_escape_string($val).'",';
                }

            }
            $SQL = substr($SQL, 0, -1);
        }



        return $this->query($SQL);
    }

    public function update($table_name,$params,$where){
        $SQL = 'UPDATE '.$table_name.' SET ';
        $i = 0;
        foreach($params as $key=>$val){
            $i++;
            $val = self::$i->escape_string($val);
            if(preg_match('#\+#is', $val) || preg_match('#\-#is', $val)){
                $SQL .= ' '.$key.'='.self::$i->real_escape_string($val).'';
            }else{
                $SQL .= ' '.$key.'="'.self::$i->real_escape_string($val).'"';
            }

            if($i != count($params)) $SQL .= ',';

        }

        $SQL .= " WHERE $where";
       return $this->query($SQL);

    }
}
