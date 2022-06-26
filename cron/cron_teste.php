<?php
include "../class/database.php";
$banco = new Database();

$sql = "insert into log_cron (
        origem, 
        datahora_inicio
    ) values (
        'log_teste.php',
        now()
    )";
$id_cron = $banco->executeSql($sql);

$sql = "update log_cron set datahora_fim = now() where id = $id_cron";
$banco->executeSql($sql);
?>