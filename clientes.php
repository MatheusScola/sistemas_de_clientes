<?php

// Coletando dados da base de dados.

include("lib/conexao.php");

$sql_code = "SELECT * FROM clientes ORDER BY id ASC ";
$query_clientes = $mysqli->query($sql_code) or die($mysqli->error);
$num_clientes = $query_clientes->num_rows;

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes</title>
</head>
<body>
    
    <h1>Lista de Clientes</h1>
    <p><a href="cadastrar_clientes.php">Cadastrar novo cliente</a></p>

    <table border="1" cellpadding = "10">
        <thead>
            <th>ID</th>
            <th>Foto</th>
            <th>Nome</th>
            <th>E-mail</th>
            <th>Telefone</th>
            <th>Data de nascimento</th>
            <th>Data de cadastro</th>
            <th>Ações</th>
        </thead>
        <tbody>
             <?php if($num_clientes == 0 )  {?> <!-- Tratando caso que não haja clientes na base de dados. -->
                <tr>
                    <td colspan="7">Nenhum cliente foi cadastrado!</td>
                </tr>

                <?php 
            } else {

                // Percorrendo os clientes que estão na base de dados.

                while ($cliente = $query_clientes->fetch_assoc()) {
                    $Dt_cadastro = "Não informado";
                    $Dt_nascimento = "Não informado";
                    $telefone = "Não informado";
                    $foto = "Não informada";

                    // Tratando as variáveis.

                    if(!empty($cliente['telefone'])){
                        $telefone = formatar_telefone($cliente['telefone']);
                    }

                    if(!empty($cliente['nascimento']) && $cliente['nascimento'] != "0000-00-00") {
                        $Dt_nascimento = formatar_data($cliente['nascimento']);
                    }

                    if(!empty($cliente['cadastro'])) {
                        $Dt_cadastro = date("d/m/Y H:i", strtotime($cliente['cadastro']));
                    }

                    if(!empty($cliente['foto'])) {
                        $foto = $cliente['foto'];
                    }
                ?>
                <tr>
                    <!-- Exibindo os dados dos clientes -->

                    <td style="text-align: center;"><?php echo $cliente['id'] ?></td>
                    <td><img height="50" src="<?php echo $cliente['foto']; ?>"></td>
                    <td><?php echo $cliente['nome'] ?></td>
                    <td><?php echo $cliente['email'] ?></td>
                    <td><?php echo $telefone ?></td>
                    <td style="text-align: center;" ><?php echo $Dt_nascimento ?></td>
                    <td><?php echo $Dt_cadastro ?></td>
                    <td><a href="editar_clientes.php?id=<?php echo $cliente['id'] ?>">Editar</a> <a href="excluir_clientes.php?id=<?php echo $cliente['id'] ?>">Deletar</a></td> <!-- Criando links das páginas de manipulação do cliente. -->
                
                </tr>
                <?php
                }
            }?>
        </tbody>
    </table>
</body>
</html>