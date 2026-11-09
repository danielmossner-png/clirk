<?php
    // Configura os cookies da sessão para valerem em todo o site (raiz /)
    session_set_cookie_params(0, '/');
    session_start();

    // Inclui a conexão com o banco de dados
    include_once("conexao.php");

    // Recebe e limpa os dados enviados pelo formulário via POST
    $email_input = trim($_POST['email'] ?? '');
    $pass_input  = trim($_POST['pass'] ?? '');

    // 1. Verifica se os campos não estão vazios
    if (!empty($email_input) && !empty($pass_input)) {
        
        // Prepara a consulta SQL para buscar o usuário pelo e-mail (ignorando maiúsculas/minúsculas e espaços)
        $sql = "SELECT * FROM dados WHERE LOWER(TRIM(email)) = ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt) {
            $email_lc = strtolower($email_input);
            mysqli_stmt_bind_param($stmt, "s", $email_lc);
            mysqli_stmt_execute($stmt);
            $resultado = mysqli_stmt_get_result($stmt);

            // 2. Verifica se encontrou o usuário
            if ($usuario = mysqli_fetch_assoc($resultado)) {
                $senha_banco = trim($usuario['pass']);

                // 3. Valida a senha (suporta texto puro ou hash gerado por password_hash)
                if ($pass_input === $senha_banco || password_verify($pass_input, $senha_banco)) {
                    
                    // --- LOGIN BEM-SUCEDIDO ---
                    $_SESSION['usuario_id']   = $usuario['codigo'] ?? $usuario['id'];
                    $_SESSION['usuario_nome'] = $usuario['nome'];
                    $_SESSION['usuario_email']= $usuario['email'];
                    
                    mysqli_stmt_close($stmt);

                    // Redireciona para a área restrita do site
                    header("Location: ../index2.php");
                    exit();

                } else {
                    // Senha incorreta
                    $_SESSION['msg'] = "<p style='color:red;'>Senha incorreta!</p>";
                }
            } else {
                // E-mail não encontrado no banco
                $_SESSION['msg'] = "<p style='color:red;'>E-mail não encontrado!</p>";
            }
            mysqli_stmt_close($stmt);
        } else {
            // Erro ao preparar a query
            $_SESSION['msg'] = "<p style='color:red;'>Erro na consulta do banco de dados.</p>";
        }
    } else {
        // Campos em branco
        $_SESSION['msg'] = "<p style='color:red;'>Preencha todos os campos!</p>";
    }

    // --- SE CHEGOU AQUI, HOUVE ALGUM ERRO (E-mail incorreto, senha incorreta ou campos vazios) ---
    // Redireciona de volta para a página do formulário de login (Ajuste o caminho se necessário, ex: index.php ou login.php)
    header("Location: .../index2.php"); 
    exit();
?>