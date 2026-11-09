<?php
require_once("conexao.php");

echo "<h3>Diagnóstico de Login</h3>";

if ($conn) {
    echo "<p style='color:green;'>✓ Conexão com o banco de dados OK!</p>";
} else {
    die("<p style='color:red;'>✗ Falha na conexão com o banco de dados!</p>");
}

$email_teste = "sad.weq@escola.pr.gov.br";
$sql = "SELECT * FROM dados WHERE email = '$email_teste'";
$res = mysqli_query($conn, $sql);

if ($res && mysqli_num_rows($res) > 0) {
    $dados = mysqli_fetch_assoc($res);
    echo "<p style='color:green;'>✓ Usuário encontrado no banco!</p>";
    echo "<pre>";
    print_r($dados);
    echo "</pre>";
} else {
    echo "<p style='color:red;'>✗ E-mail '$email_teste' não foi encontrado na tabela 'dados'.</p>";
}
?>