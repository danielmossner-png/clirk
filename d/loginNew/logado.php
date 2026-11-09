<?php
    // Configura os cookies da sessão para valerem em todo o site (raiz /)
    session_set_cookie_params(0, '/');
    session_start();

    // Inclui a conexão com o banco de dados
    include_once("conexao.php");

    // Recebe e limpa os dados enviados pelo formulário
    $email_input = trim($_POST['email'] ?? '');
    $pass_input  = trim($_POST['pass'] ?? '');

    // 1. Verifica se os campos foram preenchidos
    if (!empty($email_input) && !empty($pass_input)) {
        
        // Busca o usuário pelo E-mail no banco de dados
        $sql = "SELECT * FROM dados WHERE LOWER(TRIM(email)) = ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt) {
            $email_lc = strtolower($email_input);
            mysqli_stmt_bind_param($stmt, "s", $email_lc);
            mysqli_stmt_execute($stmt);
            $resultado = mysqli_stmt_get_result($stmt);

            // 2. Verifica se o e-mail existe
            if ($usuario = mysqli_fetch_assoc($resultado)) {
                $senha_banco = trim($usuario['pass']);

                // 3. Verifica se a senha confere (texto puro ou com password_hash)
                if ($pass_input === $senha_banco || password_verify($pass_input, $senha_banco)) {
                    
                    // --- SUCESSO: SALVA OS DADOS NA SESSÃO ---
                    $_SESSION['usuario_id']   = $usuario['codigo'] ?? $usuario['id'];
                    $_SESSION['usuario_nome'] = $usuario['nome'];
                    $_SESSION['usuario_email']= $usuario['email'];
                    
                    mysqli_stmt_close($stmt);

                    // Redireciona para a área restrita do site
                    header("Location: ../index2.php"); // Ajuste o caminho se o index2.php estiver em outra pasta
                    exit();

                } else {
                    // Senha incorreta
                    $_SESSION['msg'] = "<p style='color:red;'>Senha incorreta!</p>";
                }
            } else {
                // E-mail não cadastrado
                $_SESSION['msg'] = "<p style='color:red;'>E-mail não encontrado!</p>";
            }
            mysqli_stmt_close($stmt);
        } else {
            $_SESSION['msg'] = "<p style='color:red;'>Erro na consulta do banco de dados.</p>";
        }
    } else {
        $_SESSION['msg'] = "<p style='color:red;'>Preencha todos os campos!</p>";
    }

    // --- ERRO: SE CHEGOU AQUI, O LOGIN FALHOU ---
    // Redireciona de volta para a tela de login (este arquivo HTML que você acabou de mandar, ex: index.php)
    header("Location: index.php"); 
    exit();
?>