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

// Função para formatar telefone
function limpar_texto($str){
    return preg_replace("/[^0-9]/","",$str);
}

if(count($_POST) > 0){

    // importando arquivos
    include('lib/conexao.php');
    include('lib/upload.php');
    include("lib/mail.php");

    // Criando variáveis com os dados do cliente
    $erro = false;
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $dt_Nascimento = $_POST['dt_Nascimento'];
    $telefone = $_POST['telefone'];
    $password = $_POST['senha'];
    $funcao = $_POST['admin'];

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

    if (strlen($password) < 6 && strlen($password) > 16) {
        $erro = "A senha deve ter entre 6 e 16 caracteres!";
    } else {
        // Criptografando a senha do cliente.
        $encrypted_password = password_hash($password, PASSWORD_DEFAULT);
    }

    $path = "";
    if (!empty($_FILES['foto']['name'])) {
        $foto = $_FILES['foto'];
        
        // Conferindo se a foto é válida.
        $path = verificarArquivo($foto['error'], $foto['size'], $foto['name'], $foto['tmp_name']);

        if(!$path) {
            $erro = "Erro ao enviar arquivo da foto!";
        }
    }

    if ($erro) {
        echo "<p><b>ERRO: $erro</b></p>";

    } else {
        // Inserção dos dados do cliente na base de dados
        $sql_code = "INSERT INTO clientes (nome, foto, email, senha , nascimento, telefone, cadastro, admin) VALUES ('$nome', '$path', '$email', '$encrypted_password' , '$dt_Nascimento', '$telefone', NOW(), $funcao)";
        $deu_certo = $mysqli->query($sql_code) or die($mysqli->error);

        if($deu_certo) {
            echo "<p><b>Cliente cadastrado com sucesso!!</b></p>";

            // Montando texto que será enviado ao cliente.
            $text_email =
            "<h1>Parabéns, $nome!</h1>
            <p>Sua conta foi criada no meu site SitedeTeste.com</p>
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
            unset($_POST);
        }
    }
}

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
    <form method="POST" enctype="multipart/form-data">

        <p>
            <label>Nome:</label>
            <input value="<?php if(isset($_POST['nome'])) echo $_POST['nome']; ?>" name="nome" type="text">
        </p>


        <p>
            <label>E-mail:</label>
            <input value="<?php if(isset($_POST['email'])) echo $_POST['email']; ?>" name="email" type="text">
        </p>

        <p>
            <label>Telefone:</label>
            <input value="<?php if(isset($_POST['telefone'])) echo $_POST['telefone']; ?>" placeholder="(11) 98888-8888" name="telefone" type="text">
        </p>

        <p>
            <label>Data de nascimento:</label>
            <input value="<?php if(isset($_POST['dt_Nascimento'])) echo $_POST['dt_Nascimento']; ?>" placeholder="DD/MM/AAAA" name="dt_Nascimento" type="text">
        </p>

        <p>
            <label>Senha:</label>
            <input value="<?php if(isset($_POST['senha'])) echo $_POST['senha']; ?>" name="senha" type="text">
        </p>

        <p>
            <label>Foto do cliente:</label>
            <input name="foto" type="file">
        </p>

        <p>
            <label>Tipo:</label>
            <label><input name="admin" value="1" type="radio">ADMIN</label>
            <label><input name="admin" value="0" checked type="radio"> CLIENTE</label>
        </p>

        <p>
            <button type="submit">Salvar Cliente</button>
        </p>

    </form>
</body>
</html>