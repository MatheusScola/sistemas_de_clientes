<?php

// Conferindo se existe alguma sessão iniciada.
if(!isset($_SESSION)) {
    // Criando nova sessão
    session_start();
}

include("lib/conexao.php");

if(isset($_POST['email']) && isset($_POST['password'])) {

    // Evitando que acessem o banco de dados através do SQL injection 
    $email = $mysqli->escape_string($_POST['email']);
    $password = $_POST['password'];

    // Puxando cliente da base de dados através do E-mail.
    $sql_code = "SELECT * FROM clientes WHERE email = '$email'";
    $query_code = $mysqli->query($sql_code) or die ($mysqli->error);
    
    // Conferindo se existe alguma conta com esse E-mail.
    if ($query_code->num_rows  == 0) {
        echo "E-mail não está vinculado com nenhuma conta do sistema!";

    } else {
        $cliente = $query_code->fetch_assoc();
        
        // Conferindo a senha digitada.
        if (!password_verify($password, $cliente['senha'])) {
            echo "Senha incorreta!";

        } else {

            // Conferindo se existe alguma sessão iniciada.
            if(!isset($_SESSION)) {
                // Criando nova sessão
                session_start();
            }
            $_SESSION['usuario'] = $cliente['id'];
            $_SESSION['admin'] = $cliente['admin'];
            header("Location: clientes.php");
        }
    }
    
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <form method="POST">
        <p>
            <Label>E-mail</Label>
            <input name= "email" type="text">
        </p>

        <p>
            <Label>Senha</Label>
            <input name= "password" type="password">
        </p>

        <p>
            <button type="submit">Entrar</button>
        </p>
    </form>
</body>
</html>