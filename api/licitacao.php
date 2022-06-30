<?php
    include '../autenticacao.php';
    include "../class/database.php";
    $banco = new Database();
    
    function preparaCampoMultiselect ($val) {
        $val = trim($val);

        if ($val != '') {
            $val_array = explode(',',$val);
            $val = '';
            foreach($val_array as $row){
                $row = trim($row);
                $val .= "'$row',";
            }
        } 
        return $val;   
    }

    function dataParaBanco ($data) {
        $data = implode("-",array_reverse(explode("/",$data)));
        return $data;
    }

    $categoria               = $_GET['categoria']; // ok
    $boletim                 = $_GET['boletim'];   // ok
    $objeto                  = $_GET['objeto'];    // ok
    $uf                      = $_GET['uf'];        // ok
    $cidade                  = $_GET['cidade'];    // ok
    $edital                  = $_GET['edital'];    // ok
    $modalidade              = $_GET['modalidade'];       // fazer
    $inclusao_de             = $_GET['inclusao_de'];      // fazer
    $inclusao_ate            = $_GET['inclusao_ate'];     // fazer
    $prazo_de                = $_GET['prazo_de'];         // fazer
    $prazo_ate               = $_GET['prazo_ate'];        // fazer
    $nr_conlicitacao         = $_GET['nr_conlicitacao'];  // ok
    $orgao                   = $_GET['orgao'];     // ok
    $obs                     = $_GET['obs'];       // ok 
    $id_boletim_conlicitacao = $_GET['id_boletim_conlicitacao']; // ok
    
    $categoria     = preparaCampoMultiselect($categoria);
    $boletim       = preparaCampoMultiselect($boletim);
    $uf            = preparaCampoMultiselect($uf);
    $cidade        = preparaCampoMultiselect($cidade);
    $modalidade    = preparaCampoMultiselect($modalidade);
    $orgao         = preparaCampoMultiselect($orgao);
    $inclusao_de   = dataParaBanco($inclusao_de);
    $inclusao_ate  = dataParaBanco($inclusao_ate);

    $sql = "select 
        l.id,
        l.boletim_id,
        l.boletim_edicao,
        l.boletim_categoria,
        l.situacao,
        l.objeto,
        date_format(l.boletim_datahora_fechamento, '%d/%m/%Y %H:%i') as boletim_datahora_fechamento,
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
    from licitacao l where boletim_datahora_fechamento between '$inclusao_de' and '$inclusao_ate' ";
    if ($categoria != '') {
        $sql .= " and l.boletim_categoria in ($categoria'')";
    }
    if ($boletim != ''){
        $sql .= " and l.boletim_id in ($boletim'')";
    }
    if ($objeto != '') {
        $sql .= " and l.objeto like '%$objeto%'";
    }
    if ($uf != '') {
        $sql .= " and l.orgao_uf in ($uf'')";
    }
    if ($cidade != '') {
        $sql .= " and l.orgao_cidade in ($cidade'')";
    }
    if ($edital != '') {
        $sql .= " and l.edital = '$edital'";
    }
    if ($orgao != '') {
        $sql .= " and l.orgao_codigo in ($orgao'')";
    }
    if ($obs != '') {
        $sql .= " and l.observacao like '%$obs%'";
    }
    if ($nr_conlicitacao != '') {
        $sql .= " and l.id = '$nr_conlicitacao'";
    }
    if ($id_boletim_conlicitacao != '') {
        $sql .= " and l.boletim_id = '$id_boletim_conlicitacao'";
    }

    $sql .= " limit 100";
    $licitacoes = $banco->executeSql($sql);

    foreach ($licitacoes as $key => $licitacao) {
        $sql = "select * from licitacao_documento where id_licitacao = '" . $licitacao['id'] . "'";
        $documentos = $banco->executeSql($sql);
        $licitacoes[$key]['documentos'] = $documentos;
    }

    echo json_encode($licitacoes);