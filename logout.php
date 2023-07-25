<?php
// Conferindo se existe alguma sessão iniciada.
if(!isset($_SESSION)) {
    // Criando nova sessão
    session_start();
}

// Eliminando todas as sessões iniciadas.
session_destroy();
header("Location: index.php");

?>