<?php
    include '../autenticacao.php';
    include "../class/database.php";
    $banco = new Database();
    
    $categoria   = $_GET['categoria'];
    $boletim     = $_GET['boletim'];
    $objeto      = $_GET['objeto'];

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
        l.id,
        l.boletim_id,
        l.boletim_edicao,
        l.boletim_categoria,
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
    from licitacao l where true ";
    if ($categoria != '') {
        $sql .= " and l.boletim_categoria in ($categoria'')";
    }
    if ($boletim != ''){
        $sql .= " and l.boletim_id in ($boletim'')";
    }
    if ($objeto != '') {
        $sql .= " and l.objeto like '%$objeto%'";
    }
    $sql .= " limit 100";
    $licitacoes = $banco->executeSql($sql);

    foreach ($licitacoes as $key => $licitacao) {
        $sql = "select * from licitacao_documento where id_licitacao = '" . $licitacao['id'] . "'";
        $documentos = $banco->executeSql($sql);
        $licitacoes[$key]['documentos'] = $documentos;
    }

    echo json_encode($licitacoes);