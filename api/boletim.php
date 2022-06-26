<?php
    include '../autenticacao.php';
    include "../class/database.php";
    include "../curl/categoria.php";
    
    $segmento   = $_GET['segmento'];

    $banco = new Database();

    $segmento_array = explode(',',$segmento);
    $segmento = '';
    foreach($segmento_array as $row){
        $row = trim($row);
        $segmento .= "'$row',";
    }

    $sql = "select 
        id_boletim,
        date_format(datahora_fechamento, '%d/%m/%Y %H:%i') as datahora_fechamento,
        id_segmento
    from boletim where id_segmento in ($segmento'') order by datahora_fechamento desc";
    $boletins = $banco->executeSql($sql);

    $categorias = getAllCategoria();
    $categorias = $categorias['filtros'];

    $array_categorias = [];
    foreach ($categorias as $categoria) {
        $array_categorias[$categoria['id']] = $categoria['descricao'];
    }

    $array_boletins = [];
    foreach ($boletins as $key => $boletim) {
        $boletins[$key]['descricao_categoria'] = $array_categorias[$boletim['id_segmento']];
    }

    echo json_encode($boletins);
    