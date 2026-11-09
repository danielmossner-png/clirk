<?php
session_set_cookie_params(0, '/');
session_start();

include_once("conexao.php");

$email_input = trim($_POST['email'] ?? '');
$pass_input  = trim($_POST['pass'] ?? '');

if (!empty($email_input) && !empty($pass_input)) {
    
    $sql = "SELECT * FROM users WHERE LOWER(TRIM(email)) = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        $email_lc = strtolower($email_input);
        mysqli_stmt_bind_param($stmt, "s", $email_lc);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);

        if ($usuario = mysqli_fetch_assoc($resultado)) {
            $senha_banco = trim($usuario['pass']);

            if ($pass_input === $senha_banco || password_verify($pass_input, $senha_banco)) {
                
                // Grava as variáveis do professor na sessão
                $_SESSION['professor_id']    = $usuario['id_users'];
                $_SESSION['professor_nome']  = $usuario['nome'];
                $_SESSION['professor_email'] = $usuario['email'];
                
                header("Location: index2.php"); 
                exit();
                
            } else {
                $_SESSION['msg'] = "<p style='color:#ff4d4d; text-align:center;'>Senha incorreta!</p>";
            }
        } else {
            $_SESSION['msg'] = "<p style='color:#ff4d4d; text-align:center;'>E-mail não encontrado!</p>";
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['msg'] = "<p style='color:#ff4d4d; text-align:center;'>Erro no banco de dados.</p>";
    }
} else {
    $_SESSION['msg'] = "<p style='color:#ff4d4d; text-align:center;'>Preencha todos os campos!</p>";
}

header("Location: index.php");
exit();
?>