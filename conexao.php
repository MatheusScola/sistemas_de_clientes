<?php
// Criando variáveis

$host = "localhost";
$db = "crud_clientes";
$user = 'root';
$pass = '';

// Criando conexão com a base de dados
$mysqli = new mysqli($host, $user, $pass, $db);

// Conferindo a conexão com a base de dados
if ($mysqli -> connect_errno) {
    die("Falha na conexão com o banco de dados!");
}

function formatar_data($data) {
    return implode('/', array_reverse(explode('-', $data)));
}

function formatar_telefone($telefone) {
    $DDD = substr($telefone, 0, 2);
    $primeira_parte = substr($telefone, 2,5) ;
    $segunda_parte = substr($telefone, 7, 10);
    
    return "($DDD) $primeira_parte-$segunda_parte";
}