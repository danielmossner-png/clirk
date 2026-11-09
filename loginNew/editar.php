<?php
// Inicia a sessão caso seu sistema utilize variáveis de sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Passo 1: Conectar ao banco de dados
$servername = "localhost";
$username = "root";
$password = "admin";
$dbname = "dados";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Inicializa variáveis para os campos do formulário
$codigo = "";
$nome = "";
$sobrenome = "";
$endereco = "";
$telefone = "";
$escolhidos = "";

// --- PARTE 1: CAPTURA E CARREGA O REGISTRO ---
if (isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])) {
    $codigo = intval($_REQUEST['codigo']);
} elseif (isset($_SESSION['codigo'])) {
    $codigo = intval($_SESSION['codigo']);
} elseif (isset($_SESSION['id_users'])) {
    $codigo = intval($_SESSION['id_users']);
} else {
    $codigo = 1; 
}

// Busca os dados do banco para preencher a tela se não estiver salvando no momento
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['atualizar'])) {
    $sql = "SELECT * FROM dados WHERE codigo = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $codigo);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $linha = $resultado->fetch_assoc();
            $nome = $linha['nome'];
            $sobrenome = $linha['sobrenome'];
            $endereco = $linha['endereco'];
            $telefone = $linha['telefone'];
            $escolhidos = $linha['escolhidos'];
        }
        $stmt->close();
    }
}

// --- PARTE 2: SALVAR AS ALTERAÇÕES (POST) ---
if (isset($_POST['atualizar'])) {
    $codigo = intval($_POST['codigo']);
    $nome = trim($_POST['nome']);
    $sobrenome = trim($_POST['sobrenome']);
    $endereco = trim($_POST['endereco']);
    $telefone = trim($_POST['telefone']);
    $escolhidos = trim($_POST['escolhidos']);

    $sql_update = "UPDATE dados SET nome = ?, sobrenome = ?, endereco = ?, telefone = ?, escolhidos = ? WHERE codigo = ?";
    $stmt_update = $conn->prepare($sql_update);

    if ($stmt_update) {
        $stmt_update->bind_param("sssssi", $nome, $sobrenome, $endereco, $telefone, $escolhidos, $codigo);
        
        if ($stmt_update->execute()) {
            echo "<script>alert('Registro atualizado com sucesso!'); window.location.href='consultaDSC.php';</script>";
            exit;
        } else {
            echo "<p style='color:red;'>Erro ao atualizar: " . $conn->error . "</p>";
        }
        $stmt_update->close();
    }
}

$conn->close();
?>

<!-- --- PARTE 3: FORMULÁRIO HTML PREENCHIDO --- -->
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Cadastro</title>
    <style>
        /* Reset básico e tipografia */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Fundo escuro para a página inteira */
        body {
            background-color: #121212;
            color: #e0e0e0;
            padding: 40px 20px;
            max-width: 1000px;
            margin: 0 auto;
        }

        /* Container para os botões do topo */
        .botoes-navegacao {
            margin-bottom: 30px;
            display: flex;
            gap: 10px;
        }

        /* Estilização dos botões */
        .botoes-navegacao input[type="button"] {
            background-color: #1e1e1e;
            color: #ffffff;
            border: 1px solid #2d2d2d;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        /* Efeito ao passar o mouse nos botões */
        .botoes-navegacao input[type="button"]:hover {
            background-color: #ffffff;
            color: #121212;
            border-color: #ffffff;
        }

        /* Título Principal */
        h2 {
            color: #ffffff;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
            border-left: 4px solid #ffffff;
            padding-left: 12px;
        }

        /* Estrutura da Tabela */
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #1e1e1e;
            border: 1px solid #2d2d2d;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5);
            margin-bottom: 20px;
        }

        /* Cabeçalho da Tabela */
        th {
            background-color: #252525;
            color: #ffffff;
            font-weight: 600;
            text-align: left;
            padding: 14px 18px;
            font-size: 14px;
            border-bottom: 2px solid #2d2d2d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Linhas da Tabela */
        td {
            padding: 14px 18px;
            font-size: 15px;
            color: #cccccc;
            border-bottom: 1px solid #2d2d2d;
        }

        /* Estilo dos Inputs e Textarea */
        input[type="text"], textarea {
            width: 100%;
            padding: 8px 12px;
            background-color: #121212;
            color: #ffffff;
            border: 1px solid #2d2d2d;
            border-radius: 4px;
            font-size: 14px;
        }

        input[type="text"]:focus, textarea:focus {
            outline: none;
            border-color: #ffffff;
        }

        /* Estilo unificado para os botões do formulário */
        .btn-formulario {
            display: inline-block;
            background-color: #1e1e1e;
            color: #ffffff;
            border: 1px solid #2d2d2d;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-formulario:hover {
            background-color: #ffffff;
            color: #121212;
            border-color: #ffffff;
        }

        .container-acoes {
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>

    <h2>Editar Registro (Código: <?php echo $codigo; ?>)</h2>

    <form method="POST" action="">
        <!-- Campo oculto guardando o código -->
        <input type="hidden" name="codigo" value="<?php echo $codigo; ?>">

        <table cellpadding="8">
            <tr>
                <td><label>Nome:</label></td>
                <td><input type="text" name="nome" value="<?php echo htmlspecialchars($nome); ?>" required></td>
            </tr>
            <tr>
                <td><label>Sobrenome:</label></td>
                <td><input type="text" name="sobrenome" value="<?php echo htmlspecialchars($sobrenome); ?>"></td>
            </tr>
            <tr>
                <td><label>Endereço:</label></td>
                <td><input type="text" name="endereco" value="<?php echo htmlspecialchars($endereco); ?>"></td>
            </tr>
            <tr>
                <td><label>Telefone:</label></td>
                <td><input type="text" name="telefone" value="<?php echo htmlspecialchars($telefone); ?>"></td>
            </tr>
            <tr>
                <td><label>Escolhidos:</label></td>
                <td><textarea name="escolhidos" rows="4" cols="30"><?php echo htmlspecialchars($escolhidos); ?></textarea></td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <div class="container-acoes">
                        <button type="submit" name="atualizar" class="btn-formulario">Salvar Alterações</button>
                        <a href="consultaDSC.php" class="btn-formulario">Cancelar</a>
                    </div>
                </td>
            </tr>
        </table>
    </form>

</body>
</html>