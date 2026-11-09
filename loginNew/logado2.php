<?php
    // Configura os cookies da sessão para valerem em todo o site (raiz /)
    session_set_cookie_params(0, '/');
    session_start();

    include_once("conexao2.php");

    $email_input = trim($_POST['email'] ?? '');
    $pass_input  = trim($_POST['pass'] ?? '');

    if (!empty($email_input) && !empty($pass_input)) {
        // Busca o usuário pelo E-mail
        $sql = "SELECT * FROM users WHERE LOWER(TRIM(email)) = ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt) {
            $email_lc = strtolower($email_input);
            mysqli_stmt_bind_param($stmt, "s", $email_lc);
            mysqli_stmt_execute($stmt);
            $resultado = mysqli_stmt_get_result($stmt);

            if ($usuario = mysqli_fetch_assoc($resultado)) {
                $senha_banco = trim($usuario['pass']);

                // Verifica a senha (em texto puro ou hash)
                if ($pass_input === $senha_banco || password_verify($pass_input, $senha_banco)) {
                    // Salva os dados na sessão global
                    $_SESSION['usuario_id']   = $usuario['id_users'] ?? $usuario['id'];
                    $_SESSION['usuario_nome'] = $usuario['nome'];
                    $_SESSION['usuario_email']= $usuario['email'];
                    
                    // Redireciona para o index.php da raiz do site
                    header("Location: aluno/index2.php");
                    exit();
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

    header("Location: aluno/index2.php");
    exit();
    ?>  