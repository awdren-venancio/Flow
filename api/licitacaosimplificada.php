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
        $data = trim($data);
        if ($data != ''){
            $data = implode("-",array_reverse(explode("/",$data)));
        }
        return $data;
    }

    $objeto                  = $_GET['objeto'];
    $uf                      = $_GET['uf'];
    $cidade                  = $_GET['cidade'];
    $edital                  = $_GET['edital'];
    $modalidade              = $_GET['modalidade'];
    $orgao_codigo            = $_GET['orgao_codigo'];
    $orgao_nome              = $_GET['orgao_nome'];
    $obs                     = $_GET['obs']; 
    
    $uf                = preparaCampoMultiselect($uf);
    $cidade            = preparaCampoMultiselect($cidade);
    $modalidade        = preparaCampoMultiselect($modalidade);
    $orgao_codigo      = preparaCampoMultiselect($orgao_codigo);
    
    $sql = "select 
        l.id,
        l.situacao,
        l.objeto,
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
    if ($modalidade != '') {
        $sql .= " and l.modalidade_abreviacao in ($modalidade'')";
    }
    if ($orgao_codigo != '') {
        $sql .= " and l.orgao_codigo in ($orgao_codigo'')";
    }
    if ($orgao_nome != '') {
        $sql .= " and l.orgao_nome like '%$orgao_nome%'";
    }
    if ($obs != '') {
        $sql .= " and l.observacao like '%$obs%'";
    }
    
    $sql .= " order by codigo desc limit 100";
    $licitacoes = $banco->executeSql($sql);

    foreach ($licitacoes as $key => $licitacao) {
        $sql = "select * from licitacao_documento where id_licitacao = '" . $licitacao['id'] . "'";
        $documentos = $banco->executeSql($sql);
        $licitacoes[$key]['documentos'] = $documentos;
    }

    echo json_encode($licitacoes);