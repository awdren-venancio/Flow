<?php
    include '../autenticacao.php';
    include "../class/database.php";
    $banco = new Database();
    
    $categoria   = $_GET['categoria'];
    $categoria_array = explode(',',$categoria);
    $categoria = '';

    foreach($categoria_array as $row){
        $row = trim($row);
        $categoria .= "'$row',";
    }

    $sql = "select 
        b.id_boletim,
        b.id_categoria,
        c.nome as nome_categoria,
        date_format(b.datahora_fechamento, '%d/%m/%Y %H:%i') as datahora_fechamento
    from boletim b 
    join categoria c on c.id = b.id_categoria
    where b.id_categoria in ($categoria'') order by b.datahora_fechamento desc";
    $boletins = $banco->executeSql($sql);

    echo json_encode($boletins);