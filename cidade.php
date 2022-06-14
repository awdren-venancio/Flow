<?php
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    
    include "class/database.php";

    function getCidades ($uf, $nome = ''){
        $banco = new Database();
        $sql = "select * from cidade where uf = '$uf'";
        if ($nome != '') {
            $sql .= " and nome like '%$nome%'";
        }
        $cidades = $banco->executeSql($sql);
        return $cidades;
    }

    $uf   = $_GET['uf'];
    $nome = $_GET['nome'];
    $cidades = getCidades($uf, $nome);
    var_dump($cidades);
    