<?php
ini_set('max_execution_time', 0);
include "../class/database.php";
include "../curl/categoria.php";
$banco = new Database();

$sql = "insert into log_cron (
        origem, 
        datahora_inicio
    ) values (
        'categoria.php',
        now()
    )";
$id_cron = $banco->executeSql($sql);

$categorias = getAllCategoria();
$categorias = $categorias['filtros'];

// Buscando as cidades de cada estado
foreach ($categorias as $categoria) {
    $id   = $categoria['id'];
    $nome = $categoria['descricao'];
    $nome = str_replace("'","\'",$nome);
    
    $sql = "select nome from categoria where id = $id";
    $res = $banco->executeSql($sql);
    if (empty($res)) {
        $sql = "insert into categoria (
            id,
            nome
        ) values (
            $id,
            '$nome'
        )";
    }
    $banco->executeSql($sql);
}

$sql = "update log_cron set datahora_fim = now() where id = $id_cron";
$banco->executeSql($sql);
?>