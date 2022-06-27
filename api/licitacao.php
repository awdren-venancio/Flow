<?php
    include '../autenticacao.php';
    include "../class/database.php";
    $banco = new Database();
    
    $categoria   = $_GET['categoria'];
    $boletim     = $_GET['boletim'];

    if ($categoria != '') {
        $categoria_array = explode(',',$categoria);
        $categoria = '';
        foreach($categoria_array as $row){
            $row = trim($row);
            $categoria .= "'$row',";
        }
    }

    if ($boletim != '') {
        $boletim_array = explode(',',$boletim);
        $boletim = '';
        foreach($boletim_array as $row){
            $row = trim($row);
            $boletim .= "'$row',";
        }
    }

    $sql = "select 
        l.situacao,
        l.objeto,
        date_format(l.datahora_abertura, '%d/%m/%Y %H:%i') as datahora_abertura,
        date_format(l.datahora_documento, '%d/%m/%Y %H:%i') as datahora_documento,
        date_format(l.datahora_visita, '%d/%m/%Y %H:%i') as datahora_visita,
        date_format(l.datahora_prazo, '%d/%m/%Y %H:%i') as datahora_prazo,
        l.edital,
        l.processo,
        l.observacao,
        l.item,
        l.preco_edital,
        l.valor_estimado,
        l.orgao_nome,
        l.orgao_codigo,
        l.orgao_cidade,
        l.orgao_uf,
        l.orgao_endereco,
        l.orgao_telefone,
        l.orgao_site
    from licitacao l ";
    if ($categoria != '') {
        $sql .= "l.id_categoria in ($categoria'')";
    }
    if ($boletim != ''){
        $sql .= "l.id_boletim in ($boletim'')";
    }
    $sql .= " limit 100";
    $licitacoes = $banco->executeSql($sql);

    echo json_encode($licitacoes);