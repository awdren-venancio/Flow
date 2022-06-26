<?php
    function getAllCategoria () {
        $curl = curl_init();

        // Buscando os segmentos contratados no ConLicitacao
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://66.94.107.114/api/filtros.php',
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

        return json_decode($response, true);
    }