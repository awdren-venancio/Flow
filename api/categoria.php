<?php
    include '../autenticacao.php';
    include "../class/database.php";
    $banco = new Database();
    
    $categoria = $_GET['categoria'];
    if ($categoria != '') {
        $categoria_array = explode(',',$categoria);
        $categoria = '';

        foreach($categoria_array as $row){
            $row = trim($row);
            $categoria .= "'$row',";
        }
    }

    $sql = "select 
        id,
        nome
    from categoria ";
    if ($categoria != '') {
        $sql .= "where id in ($categoria'') ";
    } 
    $sql .= "order by id";
    $categorias = $banco->executeSql($sql);

    echo json_encode($categorias);