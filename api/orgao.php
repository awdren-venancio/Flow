<?php
    include '../autenticacao.php';
    include "../class/database.php";

    $banco = new Database();

    $sql = "select * from _orgao order by orgao_nome";

    $orgaos = $banco->executeSql($sql);
    echo json_encode($orgaos);