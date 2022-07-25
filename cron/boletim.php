<?php
ini_set('max_execution_time', 0);
include "../class/database.php";
include "../curl/categoria.php";
$banco = new Database();

$sql = "insert into log_cron (
        origem, 
        datahora_inicio
    ) values (
        'boletim.php',
        now()
    )";
$id_cron = $banco->executeSql($sql);

// Define a quantidade máxima de boletins a ser salvo na base de dados, limite da API é 100
$max_boletins = 20;

$categorias = getAllCategoria();
$filtros = $categorias['filtros'];

// Buscando os boletins para cada categoria
foreach ($filtros as $filtro) {
    $boletim_categoria = $filtro['id'];
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "http://66.94.107.114/api/boletins.php?filtro=$boletim_categoria",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    $response = json_decode($response, true);

    $boletins = $response['boletins'];

    $qtd_boletins = 0;
    foreach ($boletins as $categoria) {
        $qtd_boletins++;
        if ($qtd_boletins > $max_boletins){
            continue;
        }
        $ultimo_boletim    = $categoria['id'];
        $boletim_categoria = $categoria['filtro_id'];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://66.94.107.114/api/boletim.php?filtro=$ultimo_boletim",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, true);

        $boletim         = $response['boletim'];
        $licitacoes      = $response['licitacoes'];
        $acompanhamentos = $response['acompanhamentos'];

        $boletim_id                  = $boletim['id'];
        $boletim_datahora_fechamento = $boletim['datahora_fechamento'];
        $boletim_edicao              = $boletim['numero_edicao'];

        $sql = "select id from boletim where id_boletim = '$ultimo_boletim'";
        $res = $banco->executeSql($sql);
        if (empty($res)) {
            $sql = "insert into boletim (
                id_boletim,
                id_categoria,
                edicao,
                datahora_fechamento,
                datahora_inclusao
            ) values (
                '$ultimo_boletim',
                '$boletim_categoria',
                '$boletim_edicao',
                '$boletim_datahora_fechamento',
                now()
            )";
            $banco->executeSql($sql);
        }

        foreach($licitacoes as $key_licitacao => $licitacao) {
            foreach($licitacao as $key_row => $row) {
                $row = str_replace("\'","|'",$row);
                $licitacao[$key_row] = str_replace("'","\'",$row);
            }
            $licitacoes[$key_licitacao] = $licitacao;
        }

        foreach($licitacoes as $licitacao) {
            $id                 = $licitacao['id'];
            $situacao           = $licitacao['situacao'];
            $objeto             = $licitacao['objeto'];
            $datahora_abertura  = $licitacao['datahora_abertura'];
            $datahora_documento = $licitacao['datahora_documento'];
            $datahora_retirada  = $licitacao['datahora_retirada'];
            $datahora_visita    = $licitacao['datahora_visita'];
            $datahora_prazo     = $licitacao['datahora_prazo'];
            $edital             = $licitacao['edital'];
            $processo           = $licitacao['processo'];
            $observacao         = $licitacao['observacao'];
            $item               = $licitacao['item'];
            $preco_edital       = $licitacao['preco_edital'];
            $valor_estimado     = $licitacao['valor_estimado'];
            $orgao_nome         = $licitacao['orgao']['nome'];
            $orgao_codigo       = $licitacao['orgao']['codigo'];
            $orgao_cidade       = $licitacao['orgao']['cidade'];
            $orgao_uf           = $licitacao['orgao']['uf'];
            $orgao_endereco     = $licitacao['orgao']['endereco'];
            $orgao_site         = $licitacao['orgao']['site'];
            $orgao_telefones    = $licitacao['orgao']['telefone'];
            
            $orgao_telefone = '';
            foreach ($orgao_telefones as $row){
                if ($orgao_telefone != '') {
                    $orgao_telefone .= ', ';
                }
                if ($row['ddd'] != ''){
                    $orgao_telefone .= '(' . $row['ddd'] . ')'; 
                }
                $orgao_telefone .= $row['numero'];

                if ($row['ramal'] != ''){
                    $orgao_telefone .= ' - Ramal: ' . $row['ramal']; 
                }
            }

            $modalidade_abrev = explode('/',$edital);
            $modalidade_abrev = $modalidade_abrev[0];

            if ($modalidade_abrev != 'RDC') {
                // Exceto RDC, considera-se os dois primeiros dígitos, ex: COMPRAS ELETRONICAS = CO, NAO INFORMADO = NÃ, INTERNACIONAL = IN
                $modalidade_abrev = $modalidade_abrev[0].$modalidade_abrev[1];
            }

            if ($modalidade_abrev == 'AP')  $modalidade_nome = 'Audiência Pública';
            if ($modalidade_abrev == 'CV')  $modalidade_nome = 'Carta Convite';
            if ($modalidade_abrev == 'CO')  $modalidade_nome = 'COMPRAS ELETRÔNICAS';
            if ($modalidade_abrev == 'CR')  $modalidade_nome = 'Concorrência';
            if ($modalidade_abrev == 'CS')  $modalidade_nome = 'Convite Shopping';
            if ($modalidade_abrev == 'CP')  $modalidade_nome = 'Cotação de Preços';
            if ($modalidade_abrev == 'CE')  $modalidade_nome = 'Cotação Eletrônica';
            if ($modalidade_abrev == 'DL')  $modalidade_nome = 'Dispensa de Licitação';
            if ($modalidade_abrev == 'IN')  $modalidade_nome = 'INTERNACIONAL';
            if ($modalidade_abrev == 'LE')  $modalidade_nome = 'Leilão';
            if ($modalidade_abrev == 'NÃ')  $modalidade_nome = 'Não Informado';
            if ($modalidade_abrev == 'PE')  $modalidade_nome = 'Pregão Eletrônico';
            if ($modalidade_abrev == 'PR')  $modalidade_nome = 'Pregão Presencial';
            if ($modalidade_abrev == 'RDC') $modalidade_nome = 'Regime Diferenciado de Contratação';
            if ($modalidade_abrev == 'SM')  $modalidade_nome = 'Sem Modalidade';
            if ($modalidade_abrev == 'TP')  $modalidade_nome = 'Tomada de Preço';

            $sql = "select id from licitacao where id = '$id'";
            $res = $banco->executeSql($sql);
            if (empty($res)) {
                $sql = "insert into licitacao (
                    id,
                    modalidade_abreviacao,
                    modalidade_nome,
                    boletim_id,
                    boletim_datahora_fechamento,
                    boletim_edicao,
                    boletim_categoria,
                    situacao,
                    objeto,
                    datahora_abertura,
                    datahora_documento,
                    datahora_retirada,
                    datahora_visita,
                    datahora_prazo,
                    edital,
                    processo,
                    observacao,
                    item,
                    preco_edital,
                    valor_estimado,
                    orgao_nome,
                    orgao_codigo,
                    orgao_cidade,
                    orgao_uf,
                    orgao_endereco,
                    orgao_telefone,
                    orgao_site
                ) values (
                    '$id',
                    '$modalidade_abrev',
                    '$modalidade_nome',
                    '$boletim_id',
                    '$boletim_datahora_fechamento',
                    '$boletim_edicao',
                    '$boletim_categoria',
                    '$situacao',
                    '$objeto',
                    '$datahora_abertura',
                    '$datahora_documento',
                    '$datahora_retirada',
                    '$datahora_visita',
                    '$datahora_prazo',
                    '$edital',
                    '$processo',
                    '$observacao',
                    '$item',
                    $preco_edital,
                    $valor_estimado,
                    '$orgao_nome',
                    '$orgao_codigo',
                    '$orgao_cidade',
                    '$orgao_uf',
                    '$orgao_endereco',
                    '$orgao_telefone',
                    '$orgao_site'
                )";
            } else {
                $sql = "update licitacao set
                    situacao                    = '$situacao',
                    objeto                      = '$objeto',
                    datahora_abertura           = '$datahora_abertura',
                    datahora_documento          = '$datahora_documento',
                    datahora_retirada           = '$datahora_retirada',
                    datahora_visita             = '$datahora_visita',
                    datahora_prazo              = '$datahora_prazo',
                    edital                      = '$edital',
                    processo                    = '$processo',
                    observacao                  = '$observacao',
                    item                        = '$item',
                    preco_edital                = $preco_edital,
                    valor_estimado              = $valor_estimado,
                    orgao_nome                  = '$orgao_nome',
                    orgao_codigo                = '$orgao_codigo',
                    orgao_cidade                = '$orgao_cidade',
                    orgao_uf                    = '$orgao_uf',
                    orgao_endereco              = '$orgao_endereco',
                    orgao_telefone              = '$orgao_telefone',
                    orgao_site                  = '$orgao_site',
                    datahora_ultima_atualizacao = now()
                where id = '$id'";
            }
            $banco->executeSql($sql);

            $documentos = $licitacao['documento'];
            foreach ($documentos as $documento) {
                $filename = $documento['filename'];
                $url      = $documento['url'];

                $sql = "select id from licitacao_documento where id_licitacao = '$id' and filename = '$filename'";
                $res = $banco->executeSql($sql);
                if (empty($res)) {
                    $sql = "insert into licitacao_documento (
                        id_licitacao,
                        filename,
                        url
                    ) values (
                        '$id',
                        '$filename',
                        '$url'
                    )";
                } else {
                    $sql = "update licitacao_documento set
                        url                         = '$url',
                        datahora_ultima_atualizacao = now()
                    where id_licitacao = '$id' and filename = '$filename'";
                }
                $banco->executeSql($sql);
            }
        }

        // Acompanhamentos

        foreach($acompanhamentos as $key_acompanhamentos => $acompanhamento) {
            foreach($acompanhamento as $key_row => $row) {
                $row = str_replace("\'","|'",$row);
                $acompanhamento[$key_row] = str_replace("'","\'",$row);
            }
            $acompanhamentos[$key_acompanhamentos] = $acompanhamento;
        }

        foreach($acompanhamentos as $acompanhamento) {
            $id_acompanhamento  = $acompanhamento['id'];

            $id_licitacao       = $acompanhamento['licitacao_id'];
            $objeto             = $acompanhamento['objeto'];
            $sintese            = $acompanhamento['sintese'];
            $data_fonte         = $acompanhamento['data_fonte'];
            $edital             = $acompanhamento['edital'];
            $processo           = $acompanhamento['processo'];
            $orgao_nome         = $acompanhamento['orgao']['nome'];
            $orgao_cidade       = $acompanhamento['orgao']['cidade'];
            $orgao_uf           = $acompanhamento['orgao']['uf'];

            $sql = "select id_acompanhamento from acompanhamento where id_acompanhamento = '$id_acompanhamento'";
            $res = $banco->executeSql($sql);
            if (empty($res)) {
                $sql = "insert into acompanhamento (
                    id_acompanhamento,
                    id_licitacao,
                    objeto,
                    sintese,
                    data_fonte,
                    edital,
                    processo,
                    orgao_nome,
                    orgao_cidade,
                    orgao_uf
                ) values (
                    '$id_acompanhamento',
                    '$id_licitacao',
                    '$objeto',
                    '$sintese',
                    '$data_fonte',
                    '$edital',
                    '$processo',
                    '$orgao_nome',
                    '$orgao_cidade',
                    '$orgao_uf'
                )";
            } else {
                $sql = "update acompanhamento set
                    id_licitacao      = '$id_licitacao',
                    objeto            = '$objeto',
                    sintese           = '$sintese',
                    data_fonte        = '$data_fonte',
                    edital            = '$edital',
                    processo          = '$processo',
                    orgao_nome        = '$orgao_nome',
                    orgao_cidade      = '$orgao_cidade',
                    orgao_uf          = '$orgao_uf'
                where id_acompanhamento = '$id_acompanhamento'";
            }
            $banco->executeSql($sql);
        }
    }
}

$sql = "update log_cron set datahora_fim = now() where id = $id_cron";
$banco->executeSql($sql);
?>