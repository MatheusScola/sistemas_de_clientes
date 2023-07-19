<?php

// Função para formatar telefone
function limpar_texto($str){
    return preg_replace("/[^0-9]/","",$str);
}

// importando arquivo
include('lib/conexao.php');

// Coletando ID do cliente
$id = intval($_GET['id']);

// Criando variáveis com os dados do cliente
if (count($_POST) > 0) {

    $erro = false;
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $dt_Nascimento = $_POST['dt_Nascimento'];
    $telefone = $_POST['telefone'];


    // Conferindo campos preenchidos pelo usuário

    if (empty($nome) || strlen($nome) < 3 ){
        $erro =  "Preencha o campo 'nome'<br>";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL) ) {
        $erro = "Preencha o campo 'E-mail'<br>";
    }

    if (!empty($dt_Nascimento)) {
        if (strlen($dt_Nascimento) != 10) {
            $erro = "A data de nascimento deve seguir o padrão: DD/MM/AAAA";
        } 
        // Convertendo o formato da data para guardar na base de dados. ( BR --> EUA )

        $pedacos = explode('/', $dt_Nascimento);
        if (count($pedacos) == 3) {
            $dt_Nascimento = implode('-', array_reverse($pedacos));
        } else {
            $erro = "A data de nascimento deve seguir o padrão: DD/MM/AAAA";
        }

    }

    if (!empty($telefone)) {
        // Deixando somente números na variável do telefone

        $telefone = limpar_texto($telefone);

        if (strlen($telefone) != 11) {
            $erro = "O telefone deve seguir o padrão: (11) 98888-8888";
        }
    }

    if ($erro) {
        echo "<p><b>ERRO: $erro</b></p>";
    } else {
        
        // Alterando dados do cliente na base de dados.
        $sql_code = "UPDATE clientes
        SET nome = '$nome',
        email = '$email',
        nascimento = '$dt_Nascimento',
        telefone = '$telefone'
        WHERE id = '$id'";
        $deu_certo = $mysqli->query($sql_code) or die($mysqli->error);
        
        if ($deu_certo) {
            echo "<p><b>Cliente atualizado com sucesso!!!</b></p>";
            unset ($_POST);
        }
    }
}

    // Buscando dados do cliente na base de dados.
    $sql_cliente = "SELECT * FROM clientes WHERE id = $id";
    $query_cliente = $mysqli->query($sql_cliente) or die($mysqli->error);
    $cliente = $query_cliente->fetch_assoc();

?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastar clientes</title>
</head>
<body>
    <a href="clientes.php">Voltar para a lista de clientes</a>
    <form method="POST" action="">

        <p>
            <label>Nome:</label>
            <input value="<?php echo $cliente['nome']; ?>" name="nome" type="text">
        </p>


        <p>
            <label>E-mail:</label>
            <input value="<?php echo $cliente['email']; ?>" name="email" type="text">
        </p>

        <p>
            <label>Telefone:</label>
            <input value="<?php if(!empty($cliente['telefone'])) echo formatar_telefone($cliente['telefone']); ?>" placeholder="(11) 98888-8888" name="telefone" type="text">
        </p>

        <p>
            <label>Data de nascimento:</label>
            <input value="<?php if(!empty($cliente['nascimento'])) echo formatar_data($cliente['nascimento']); ?>" placeholder="DD/MM/AAAA" name="dt_Nascimento" type="text">
        </p>

        <p>
            <button type="submit">Salvar Cliente</button>
        </p>

    </form>
</body>
</html>