
<?php
session_set_cookie_params(0, '/');
session_start();

include_once("conexao.php");

$email_input = trim($_POST['email'] ?? '');
$pass_input  = trim($_POST['pass'] ?? '');

if (!empty($email_input) && !empty($pass_input)) {

    $sql = "SELECT * FROM dados
            WHERE LOWER(TRIM(email)) = ?
            LIMIT 1";

    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {

        $email_lc = strtolower($email_input);

        mysqli_stmt_bind_param($stmt, "s", $email_lc);
        mysqli_stmt_execute($stmt);

        $resultado = mysqli_stmt_get_result($stmt);

        if ($usuario = mysqli_fetch_assoc($resultado)) {

            $senha_banco = trim($usuario['pass']);

            // Verifica a senha
            if (
                $pass_input === $senha_banco ||
                password_verify($pass_input, $senha_banco)
            ) {

                // Verifica o status do cadastro
                if ($usuario['status_cadastro'] === 'pendente') {

                    $_SESSION['msg'] = "<p style='color:orange;'>Seu cadastro ainda aguarda aprovação de um professor.</p>";

                } elseif ($usuario['status_cadastro'] === 'rejeitado') {

                    $_SESSION['msg'] = "<p style='color:red;'>Seu cadastro foi rejeitado. Procure a escola para mais informações.</p>";

                } elseif ($usuario['status_cadastro'] === 'aprovado') {

                    // Cadastro aprovado: cria a sessão
                    $_SESSION['usuario_id'] = $usuario['codigo'];
                    $_SESSION['usuario_nome'] = $usuario['nome'];
                    $_SESSION['usuario_email'] = $usuario['email'];

                    mysqli_stmt_close($stmt);

                    header("Location: aluno/index2.php");
                    exit();

                } else {

                    $_SESSION['msg'] = "<p style='color:red;'>Status de cadastro inválido. Procure a escola.</p>";
                }

            } else {

                $_SESSION['msg'] = "<p style='color:red;'>Senha incorreta!</p>";
            }

        } else {

            $_SESSION['msg'] = "<p style='color:red;'>E-mail não encontrado!</p>";
        }

        mysqli_stmt_close($stmt);

    } else {

        $_SESSION['msg'] = "<p style='color:red;'>Erro na consulta do banco de dados.</p>";
    }

} else {

    $_SESSION['msg'] = "<p style='color:red;'>Preencha todos os campos!</p>";
}

header("Location: index.php");
exit();
?>
