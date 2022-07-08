<?php
ini_set('max_execution_time', 0);
include "../class/database.php";
$banco = new Database();

while (true){
    $sql = "select id, edital from licitacao where modalidade_abreviacao is null limit 1000";
    $licitacoes = $banco->executeSql($sql);

    if (empty($licitacoes)){
        echo 'Fim!';
        exit;
    }

    foreach ($licitacoes as $row){
        $modalidade_abrev = explode('/',$row['edital']);
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

        $id = $row['id'];
        $sql = "update licitacao set
            modalidade_abreviacao = '$modalidade_abrev',
            modalidade_nome       = '$modalidade_nome'    
        where id = '$id'";
        $banco->executeSql($sql);
    }
}