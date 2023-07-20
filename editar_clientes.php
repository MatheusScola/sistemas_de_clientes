<?php

// Função para formatar telefone
function limpar_texto($str){
    return preg_replace("/[^0-9]/","",$str);
}

// importando arquivos
include('lib/conexao.php');
include('lib/upload.php');
include('lib/mail.php');

// Coletando ID do cliente
$id = intval($_GET['id']);

// Criando variáveis com os dados do cliente
if (count($_POST) > 0) {

    $erro = false;
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $dt_Nascimento = $_POST['dt_Nascimento'];
    $telefone = $_POST['telefone'];
    $password = $_POST['senha'];

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

    $change_Password = False;
    if (!empty($password)) {
        if (strlen($password) < 6 && strlen($password) > 16) {
            $erro = "A senha deve ter entre 6 e 16 caracteres!";
            
        } else {
            // Criptografando a nova senha do cliente.
            $encrypted_password = password_hash($password, PASSWORD_DEFAULT);
            $change_Password = True;

        }
    }

    $alterou_foto = False;
    if (!empty($_FILES['foto']['name'])) {
        $foto = $_FILES['foto'];
        
        // Conferindo se a foto é válida.
        $path = verificarArquivo($foto['error'], $foto['size'], $foto['name'], $foto['tmp_name']);
        $alterou_foto = True;

        if(!$path) {
            $erro = "Erro ao enviar arquivo da foto!";
        }

        // Buscando foto antiga do cliente na base de dados.
        $sql_cliente = "SELECT foto FROM clientes WHERE id = $id";
        $query_cliente = $mysqli->query($sql_cliente) or die($mysqli->error);
        $cliente = $query_cliente->fetch_assoc();

        // Excluindo foto antiga do cliente.
        if (!empty($cliente['foto'])) {
            unlink($cliente['foto']);
        }
    }

    if ($erro) {
        echo "<p><b>ERRO: $erro</b></p>";
    } else {

        // Incluindo código SQL extra caso a senha seja modificada.
        $sql_code_extra = "";
        if($change_Password) {
            $sql_code_extra = "senha = '$encrypted_password',";
        }

        if($alterou_foto) {
            $sql_code_extra .= "foto = '$path', ";
        }

        // Alterando dados do cliente na base de dados.
        $sql_code = "UPDATE clientes SET nome = '$nome', $sql_code_extra email = '$email', nascimento = '$dt_Nascimento', telefone = '$telefone' WHERE id = '$id'";
        $deu_certo = $mysqli->query($sql_code) or die($mysqli->error);
        
        if ($deu_certo) {
            echo "<p><b>Cliente atualizado com sucesso!!!</b></p>";

            if ($change_Password){
                // Montando texto que será enviado ao cliente caso o a senha tenha sido alterada.
                $text_email =
                "<h1>Olá, $nome!</h1>
                <p>Sua senha foi atualizada!</p>
                <p>
                    <b>Login:</b> $email<br>
                    <b>Senha:</b> $password<br>
                </p>
                <p>Para fazer o seu login <a href=\"https://SitedeTeste.com/login.php\">clique aqui.</a></p>";
                
                // Enviando e-mail para cliente cadastrado.
                $email_enviado = send_email($email, "Cadastro realizado!", $text_email); 
            
                if(!$email_enviado){
                    echo $email_enviado;

                }
            }
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
    <title>Editar clientes</title>
</head>
<body>
    <a href="clientes.php">Voltar para a lista de clientes</a>
    <form method="POST" enctype="multipart/form-data">

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
            <input value="<?php if(!empty($cliente['nascimento']) && $cliente['nascimento'] != "0000-00-00" ) echo formatar_data($cliente['nascimento']) ?>" placeholder="DD/MM/AAAA" name="dt_Nascimento" type="text">
        </p>

        <p>
            <label>Senha:</label>
            <input name="senha" type="text">
        </p>

        <?php if($cliente['foto']) { ?>
        <p>
            <label>Foto atual:</label><br>
            <img height="50"  src="<?php echo $cliente['foto']; ?>">
        </p>
        <?php } ?>
        <p>
            <label>Nova foto do cliente:</label>
            <input name="foto" type="file">
        </p>

        <p>
            <button type="submit">Salvar Cliente</button>
        </p>

    </form>
</body>
</html>