<?php

class Database {
    private $user     = 'awdren';
    private $password = 'Inter761200';
    private $host     = 'database-1.cotw6xl7jgds.us-east-1.rds.amazonaws.com';
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
        } else {

            $query = str_replace("'",'##',$query);
            $sql = "insert into log_erro_sql (erro, sql_erro, datahora) values ('Sem erro','$query',now())";
            $this->conn->query($sql);
        }

        if ($tipo == 'insert'){
            $res = mysqli_insert_id($this->conn);
            return $res;
        }
        
        if ($tipo == 'select'){
            $res = [];
            while ($row = mysqli_fetch_assoc($conn)){
                $res[] = $row;
            }
            return $res;
        }
    }

}