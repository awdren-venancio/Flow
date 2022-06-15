<?php
    include 'autenticacao.php';
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
    echo json_encode($cidades);
    