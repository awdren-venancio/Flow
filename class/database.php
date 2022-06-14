<?php

class Database {
    private $user     = 'awdren';
    private $password = 'Inter761200';
    private $host     = 'database-1.cotw6xl7jgds.us-east-1.rds.amazonaws.com';
    private $port     = 3306;
    private $database = 'flow';

    private $conn;

    public function __construct() {
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

    public function executeSql ($query) {
        $conn = $this->conn->query($query);
        $res = [];
        while ($row = mysqli_fetch_assoc($conn)){
            $res[] = $row;
        }   
        return $res;
    }

}
