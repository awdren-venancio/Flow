<?php
ini_set('max_execution_time', 0);
include "../class/database.php";
$banco = new Database();

$sql = "insert into log_cron (
        origem, 
        datahora_inicio
    ) values (
        'cidade.php',
        now()
    )";
$id_cron = $banco->executeSql($sql);

$curl = curl_init();

// Buscando os estados brasileiros
curl_setopt_array($curl, array(
    CURLOPT_URL => 'http://servicodados.ibge.gov.br/api/v1/localidades/estados',
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
$estados = $response;

// Buscando as cidades de cada estado
foreach ($estados as $estado) {
    $sigla = $estado['sigla'];
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "http://servicodados.ibge.gov.br/api/v1/localidades/estados/$sigla/municipios",
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

    $cidades = $response;

    foreach ($cidades as $cidade) {
        $nome = str_replace("'","\'",$cidade['nome']);
        
        $sql = "select nome from cidade where nome = '$nome' and uf = '$sigla'";
        $res = $banco->executeSql($sql);
        if (empty($res)) {
            $sql = "insert into cidade (
                nome,
                uf
            ) values (
                '$nome',
                '$sigla'
            )";
        }
        $banco->executeSql($sql);
    }
}

$sql = "update log_cron set datahora_fim = now() where id = $id_cron";
$banco->executeSql($sql);
?>