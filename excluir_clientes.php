<?php

// Conferindo se existe alguma sessão iniciada.
if(!isset($_SESSION)) {
    // Criando nova sessão
    session_start();
}

// Conferindo se o cliente logado é um Administrador.
if (!isset($_SESSION['admin']) || !$_SESSION['admin'] ) {
    header(("Location: clientes.php"));
    die();
}

// importando arquivo.
include("lib/conexao.php");

// Coletando ID do cliente.
$id = intval($_GET['id']);

// Buscando foto do cliente na base de dados.
$sql_cliente = "SELECT foto FROM clientes WHERE id = $id";
$query_cliente = $mysqli->query($sql_cliente) or die($mysqli->error);
$cliente = $query_cliente->fetch_assoc();

if (isset($_POST['confirmar'])) {

    // Deletando cliente da base de dados.
    $sql_code = "DELETE FROM clientes WHERE id = $id";
    $deu_certo = $mysqli->query($sql_code) or die ($mysqli->error);
    
    if ($deu_certo) { 
        
        // Apagando foto do cliente.
        if (!empty($cliente['foto'])) {
            unlink($cliente['foto']);
        }?>

        <h1>Cliente deletado com sucesso!</h1>
        <p><a href="clientes.php">Clique aqui</a> para voltar para a lista de clientes.</p>
        <?php
        die();

    }
}


?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir cliente</title>
</head>
<body>
    <H1>Tem certeza que deseja deletar este cliente?</H1>
    <form action="" method="POST">
        <a style="margin-right: 40px;" href="clientes.php">Não</a>
        <button name="confirmar" value="1" type="submit">Sim</button>
    </form>
</body>
</html>
