<?php
    include '../autenticacao.php';
    include "../class/database.php";
    
    $uf   = $_GET['uf'];
    $nome = $_GET['nome'];

    $banco = new Database();

    $uf_array = explode(',',$uf);
    $uf = '';
    foreach($uf_array as $row){
        $row = trim($row);
        $uf .= "'$row',";
    }

    $sql = "select * from cidade where uf in ($uf'')";
    if ($nome != '') {
        $sql .= " and nome like '%$nome%'";
    }

    $sql .= ' order by nome';
    $cidades = $banco->executeSql($sql);
    echo json_encode($cidades);
    