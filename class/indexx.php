<?php

    include "database.php";
    $banco = new Database();

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api-v2.blaze.com/crash_games/recent/history?page=1",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    $response = json_decode($response);

    $linhas = $response->records;

    foreach (array_reverse($linhas) as $linha) {
        $datahora = $linha->created_at;
        $valor    = $linha->crash_point;
        $id_blaze = $linha->id;

        $datahora = str_replace('T', ' ', $datahora);
        $datahora = str_replace('Z', '', $datahora);

        $sql = "SELECT * FROM awd.crash WHERE id_blaze = '$id_blaze' AND valor = $valor;";
        $res = $banco->executeSql($sql);
        
        if (empty($res)) {
            
            $sql = "INSERT INTO awd.crash (
                    valor,
                    datahora,
                    id_blaze
                ) values (
                    $valor,
                    '$datahora',
                    '$id_blaze'
            )";
            $banco->executeSql($sql);
        }
    }