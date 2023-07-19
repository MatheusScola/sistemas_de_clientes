<?php

// Função para validar e enviar arquivo para a base de dados.
function verificarArquivo($error, $size, $name, $tmp_name) {

    // Trantando possíveis erros.
    if($error) {
        die("Falha ao enviar arquivo");
    }

    // Conferindo tamanho do arquivo.
    if($size > 2097152) {
        die("Arquivo muito grande!! Max: 2MB");
    }

    // Colentado informações do arquivo.
    $pasta = "arquivos/";
    $nomeDoArquivo = $name;
    $novoNomeDoArquivo = uniqid();
    $extensao = strtolower(pathinfo($nomeDoArquivo, PATHINFO_EXTENSION));

    if($extensao != "jpg" && $extensao != "png") {
        die("Tipo de arquivo não aceito");
    }
    // Criando novo caminho do arquivo.
    $path = $pasta . $novoNomeDoArquivo . "." . $extensao;
    
    // Movendo arquivo para novo caminho.
    $deu_certo = move_uploaded_file($tmp_name, $path);
    if($deu_certo) {
        return $path;

    } else{
        return false;
    }
}


?>