<?php
session_start();
include_once("conexao.php");

// Captura e limpa os dados enviados pelo formulário
$nome        = mysqli_real_escape_string($conn, trim($_POST['nome'] ?? ''));
$sobrenome   = mysqli_real_escape_string($conn, trim($_POST['sobrenome'] ?? ''));
$email       = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
$pass        = mysqli_real_escape_string($conn, trim($_POST['pass'] ?? ''));
$endereco    = mysqli_real_escape_string($conn, trim($_POST['endereco'] ?? ''));
$telefone    = mysqli_real_escape_string($conn, trim($_POST['telefone'] ?? ''));
$estado      = mysqli_real_escape_string($conn, trim($_POST['estado'] ?? ''));
$cidade      = mysqli_real_escape_string($conn, trim($_POST['cidade'] ?? ''));
$escola      = mysqli_real_escape_string($conn, trim($_POST['escola'] ?? ''));
$ano_escolar = mysqli_real_escape_string($conn, trim($_POST['ano_escolar'] ?? ''));

// Valida se os campos obrigatórios foram preenchidos
if (!empty($nome) && !empty($email) && !empty($pass)) {
    
    // Insere o utilizador na base de dados guardando a palavra-passe no campo pass
    $result_usuario = "INSERT INTO dados (nome, sobrenome, email, pass, endereco, telefone, estado, cidade, escola, ano_escolar) 
                       VALUES ('$nome', '$sobrenome', '$email', '$pass', '$endereco', '$telefone', '$estado', '$cidade', '$escola', '$ano_escolar')";

    $resultado_usuario = mysqli_query($conn, $result_usuario);

    if (mysqli_insert_id($conn)) {
        $_SESSION['msg'] = "<p style='color:green; text-align:center;'>Usuário cadastrado com sucesso!</p>";
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['msg'] = "<p style='color:red; text-align:center;'>Erro ao cadastrar no banco de dados.</p>";
        header("Location: cad_usuario.php");
        exit();
    }
} else {
    $_SESSION['msg'] = "<p style='color:red; text-align:center;'>Preencha todos os campos obrigatórios!</p>";
    header("Location: cad_usuario.php");
    exit();
}
?>
