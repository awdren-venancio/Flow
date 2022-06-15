<?php
    include 'autenticacao.php';
    include "class/database.php";

    function getCidades ($uf, $nome = ''){
        $banco = new Database();

        $uf_array = explode(',',$uf);
        $uf = '';
        foreach($uf_array as $row){
            $uf .= trim("'$row',");
        }

        $sql = "select * from cidade where uf in ($uf,'')";
        if ($nome != '') {
            $sql .= " and nome like '%$nome%'";
        }
        $sql .= ' order by nome';
        $cidades = $banco->executeSql($sql, 'select');

        $sql = "INSERT INTO flow.estado (nome) VALUES ('$sql')";
        $banco->executeSql($sql);
        
        return $cidades;
    }

    
    $uf   = $_GET['uf'];
    $nome = $_GET['nome'];

    $cidades = getCidades($uf, $nome);
    echo json_encode($cidades);
    