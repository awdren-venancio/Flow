<?php

class Database {
    private $user     = 'suporte';
    private $password = '@Inter761200';
    private $host     = '66.94.107.114';
    private $port     = 3306;
    private $database = 'flow';

    private $conn;
    private $conn2;

    public function __construct() {
        $this->conectarMySql();
    }

    public function conectarMySql() {
        @$this->conn = mysqli_connect (
            $this->host,
            $this->user,
            $this->password,
            $this->database
        );

        if (!$this->conn) {
            echo "Falha na conexao com o Banco de Dados!<br />";
            die();
        } else {
            $this->executeSql("set SQL_MODE = 'NO_ENGINE_SUBSTITUTION';");
        }
    }

    public function desconectarMySql() {
        mysqli_close($this->conn);
    }

    public function executeSql ($query, $tipo = '') {
        if ($tipo == ''){
            $tipo = explode(' ', substr(trim($query),0,20));
            $tipo = $tipo[0];
        }
        
        $conn = $this->conn->query($query);
        if (!$conn){
            $erro  = mysqli_error($this->conn);
            $erro  = str_replace("'",'##',$erro);
            $query = str_replace("'",'##',$query);
            $sql = "insert into log_erro_sql (erro, sql_erro, datahora) values ('$erro','$query',now())";
            $this->conn->query($sql);
            die();
        }

        if ($tipo == strtoupper('INSERT')){
            $res = mysqli_insert_id($this->conn);
            return $res;
        }
        
        if ($tipo == strtoupper('SELECT')){
            $res = [];
            while ($row = mysqli_fetch_assoc($conn)){
                $res[] = $row;
            }
            return $res;
        }
    }

}