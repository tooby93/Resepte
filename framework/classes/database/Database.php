<?php
class VDatabase{
    /**
     * @var Хранит текущий SQL запрос
     */
    protected $sql;

    /**
     * Конструктор запроса
     * @var bool
     */
    private $construct = false;

    /**
     * @param $sql
     * @return VDataBase|VMysqliDataBase
     */
    public function sql($sql){
        $this->construct = false;
        $this->sql = $sql;
        return $this;
    }

    /**
     * @param $s
     * @return VDataBase|VMysqliDataBase
     */
    public function select($s){
        $this->sql = "SELECT $s";
        return $this;
    }

    /**
     * @param $s
     * @return VDataBase|VMysqliDataBase
     */
    public function from($s){
        $this->sql .= " FROM $s";
        return $this;
    }

    /**
     * @param $s
     * @return VDataBase|VMysqliDataBase
     */
    public function where($s){
        $this->sql .= " WHERE $s";
        return $this;
    }

    /**
     * @param $s
     * @param $ss
     * @return VDataBase|VMysqliDataBase
     */
    public function join($s, $ss){
        $this->sql .= " INNER JOIN $s ON $ss";
        return $this;
    }

    /**
     * @param $s
     * @param $ss
     * @return VDataBase|VMysqliDataBase
     */
    public function leftJoin($s, $ss){
        $this->sql .= " LEFT JOIN $s ON $ss";
        return $this;
    }

    /**
     * @param string $table_name
     * @param array $params
     * @param bool $ignore
     */
    public function insert($table_name,$params, $ignore = false){
        if($ignore == true){
            $i = ' IGNORE ';
        }else{
            $i = '';
        }

        $SQL = "INSERT $i INTO $table_name SET ";
        $i = 0;
        foreach($params as $key=>$val){
            $i++;
            $SQL .= ' '.$key.'="'.$val.'"';
            if($i != count($params)) $SQL .= ',';

        }

        return $this->query($SQL);
    }



    /**
     * @param string $table_name
     * @param array $params
     * @param string $where
     */
    public function update($table_name,$params,$where){
        $SQL = "UPDATE $table_name SET ";
        $i = 0;
        foreach($params as $key=>$val){
            $i++;
            $SQL .= ' '.$key.'="'.$val.'"';
            if($i != count($params)) $SQL .= ',';

        }

        $SQL .= " WHERE $where";
        return $this->query($SQL);

    }


    public function delete($table_name,$where){
        $SQL = "DELETE FROM $table_name WHERE $where";
        return $this->query($SQL);
    }


}
