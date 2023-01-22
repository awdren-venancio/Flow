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

    $categoria               = $_GET['categoria'];
    $categoriaEmpresa        = $_GET['categoriaEmpresa'];
    $boletim                 = $_GET['boletim'];
    $objeto                  = $_GET['objeto'];
    $uf                      = $_GET['uf'];
    $cidade                  = $_GET['cidade'];
    $edital                  = $_GET['edital'];
    $modalidade              = $_GET['modalidade'];
    $inclusao_de             = $_GET['inclusao_de'];
    $inclusao_ate            = $_GET['inclusao_ate'];
    $prazo_de                = $_GET['prazo_de'];
    $prazo_ate               = $_GET['prazo_ate'];
    $nr_conlicitacao         = $_GET['nr_conlicitacao'];
    $orgao_codigo            = $_GET['orgao_codigo'];
    $orgao_nome              = $_GET['orgao_nome'];
    $obs                     = $_GET['obs']; 
    
    $categoria         = preparaCampoMultiselect($categoria);
    $categoriaEmpresa  = preparaCampoMultiselect($categoriaEmpresa);
    $boletim           = preparaCampoMultiselect($boletim);
    $uf                = preparaCampoMultiselect($uf);
    $cidade            = preparaCampoMultiselect($cidade);
    $modalidade        = preparaCampoMultiselect($modalidade);
    $orgao_codigo      = preparaCampoMultiselect($orgao_codigo);
    $inclusao_de       = dataParaBanco($inclusao_de);
    $inclusao_ate      = dataParaBanco($inclusao_ate);
    $prazo_de          = dataParaBanco($prazo_de);
    $prazo_ate         = dataParaBanco($prazo_ate);

    $sql = "select 
        l.id,
        l.boletim_id,
        l.boletim_edicao,
        l.boletim_categoria,
        l.boletim_categoria as categoriaEmpresa,
        l.situacao,
        l.objeto,
        date_format(l.boletim_datahora_fechamento, '%m/%d/%Y %H:%i') as boletim_datahora_fechamento,
        date_format(l.datahora_abertura, '%m/%d/%Y %H:%i') as datahora_abertura,
        date_format(l.datahora_documento, '%m/%d/%Y %H:%i') as datahora_documento,
        date_format(l.datahora_visita, '%m/%d/%Y %H:%i') as datahora_visita,
        date_format(l.datahora_prazo, '%m/%d/%Y %H:%i') as datahora_prazo,
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
    if ($categoriaEmpresa != '') {
        $sql .= " and l.boletim_categoria in ($categoriaEmpresa'')";
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
    if ($prazo_de != '') {
        // Algumas licitacoes a informação de data_prazo vem no campo data_abertura
        $sql .= " and (date(l.datahora_prazo) >= '$prazo_de' or date(l.datahora_abertura) >= '$prazo_de')";
    }
    if ($prazo_ate != '') {
        $sql .= " and (date(l.datahora_prazo) <= '$prazo_ate' or date(l.datahora_abertura) <= '$prazo_ate')";
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
    if ($nr_conlicitacao != '') {
        $sql .= " and l.id = '$nr_conlicitacao'";
    }

    $sql .= " order by boletim_datahora_fechamento desc limit 100";
    $licitacoes = $banco->executeSql($sql);

    foreach ($licitacoes as $key => $licitacao) {
        $sql = "select * from licitacao_documento where id_licitacao = '" . $licitacao['id'] . "'";
        $documentos = $banco->executeSql($sql);
        $licitacoes[$key]['documentos'] = $documentos;
    }

    echo json_encode($licitacoes);