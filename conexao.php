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