<?php
session_set_cookie_params(0, '/');
session_start();

// Se o professor já estiver logado, vai para o painel
if (isset($_SESSION['professor_id'])) {
    header("Location: index2.php");
    exit();
}
?>
    
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Estuda Paraná</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background-color: #121212;
            color: #ffffff;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-card {
            background-color: #1e1e1e;
            padding: 30px;
            border-radius: 8px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.5);
        }
        .login-card h2 { margin-top: 0; font-size: 24px; }
        .login-card p { color: #b0b0b0; font-size: 14px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-size: 14px; }
        .form-group input[type="email"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #333;
            border-radius: 4px;
            background-color: #2a2a2a;
            color: #fff;
            box-sizing: border-box;
        }
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            margin-bottom: 20px;
            color: #b0b0b0;
        }
        .form-options a { color: #ffffff; text-decoration: none; font-weight: bold; }
        .btn-entrar {
            width: 100%;
            padding: 12px;
            background-color: #ffffff;
            color: #000000;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
        }
        .btn-entrar:hover { background-color: #e0e0e0; }
    </style>
</head>
<body>

    <div class="login-card">
        <h2>Login</h2>
        <p>Entre com seus dados corretamente para acessar o sistema.</p>

        <?php
        if (isset($_SESSION['msg'])) {
            echo $_SESSION['msg'];
            unset($_SESSION['msg']);
        }
        ?>

        <form action="logado.php" method="POST">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
            </div>

            <div class="form-group">
                <label for="pass">Senha:</label>
                <input type="password" name="pass" id="pass" required>
            </div>

            <div class="form-options">
                <label><input type="checkbox" name="manter"> Permanecer Logado</label>
                <a href="../../index.html">Voltar</a>
            </div>

            <button type="submit" class="btn-entrar">Entrar</button>
        </form>
    </div>

</body>
</html>