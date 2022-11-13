<?php
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);

    include '../autenticacao.php';
    include "../class/database.php";

    $banco = new Database();

    $sql = "select * from _orgao order by orgao_nome";

    $orgaos = $banco->executeSql($sql);
    echo json_encode($orgaos);