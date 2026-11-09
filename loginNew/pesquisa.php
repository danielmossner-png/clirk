<?php
$servername = "localhost";
$username = "root";
$password = "admin";
$dbname = "dados";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Pesquisar Cadastros</title>
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

        /* Estilização dos botões do topo */
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
            appearance: none;
            -webkit-appearance: none;
        }

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

        /* Formulário de Busca */
        .form-busca {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
        }

        .form-busca input[type="text"] {
            flex: 1;
            padding: 10px 14px;
            background-color: #1e1e1e;
            color: #ffffff;
            border: 1px solid #2d2d2d;
            border-radius: 4px;
            font-size: 14px;
        }

        .form-busca input[type="text"]:focus {
            outline: none;
            border-color: #ffffff;
        }

        .btn-busca {
            display: inline-flex;
            align-items: center;
            justify-content: center;
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
            appearance: none;
            -webkit-appearance: none;
        }

        .btn-busca:hover {
            background-color: #ffffff;
            color: #121212;
            border-color: #ffffff;
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

        td {
            padding: 14px 18px;
            font-size: 15px;
            color: #cccccc;
            border-bottom: 1px solid #2d2d2d;
        }

        tbody tr:nth-child(even) {
            background-color: #161616;
        }

        tbody tr:hover {
            background-color: #2a2a2a;
        }

        /* Botões de Ação na Tabela */
        .btn-acao {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 14px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.2s ease;
            margin-right: 5px;
            appearance: none;
            -webkit-appearance: none;
            box-sizing: border-box;
            background-color: #1e1e1e;
            color: #ffffff;
            border: 1px solid #2d2d2d;
            cursor: pointer;
        }

        .btn-editar:hover {
            background-color: #ffffff;
            color: #121212;
            border-color: #ffffff;
        }

        .btn-excluir {
            color: #ff6b6b;
        }

        .btn-excluir:hover {
            background-color: #ff6b6b;
            color: #ffffff;
            border-color: #ff6b6b;
        }

        .total-registros {
            font-size: 14px;
            color: #aaaaaa;
            text-align: right;
            padding: 5px 10px;
        }

        .erro-mensagem {
            text-align: center;
            background-color: #1e1e1e;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #2d2d2d;
            color: #aaaaaa;
        }
    </style>
</head>
<body>

    <div class="botoes-navegacao">
        <a href="consultaASC.php"><input type="button" value="A-Z"/></a>
        <a href="consultaDSC.php"><input type="button" value="Z-A"/></a>
        <a href="pesquisa.php"><input type="button" value="Pesquisar"/></a>
        <a href="../index.html"><input type="button" value="Inicio"/></a>
    </div>

    <h2>Pesquisar Registros</h2>

    <form method="GET" action="pesquisa.php" class="form-busca">
        <input type="text" name="busca" placeholder="Digite o nome, sobrenome ou código..." value="<?php echo htmlspecialchars($busca); ?>">
        <button type="submit" class="btn-busca">Buscar</button>
    </form>

<?php
// Se estiver vazio traz todos, se tiver algo filtra na consulta
if (empty($busca)) {
    $sql = "SELECT codigo, nome, sobrenome, endereco, telefone, escolhidos FROM dados ORDER BY nome ASC";
    $stmt = $conn->prepare($sql);
} else {
    $sql = "SELECT codigo, nome, sobrenome, endereco, telefone, escolhidos 
            FROM dados 
            WHERE nome LIKE ? OR sobrenome LIKE ? OR codigo LIKE ? 
            ORDER BY nome ASC";
    $stmt = $conn->prepare($sql);
    $param = "%" . $busca . "%";
    $stmt->bind_param("sss", $param, $param, $param);
}

if ($stmt) {
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        echo "<table>";
        echo "<thead>";
        echo "<tr>
                <th>Codigo</th>
                <th>Nome</th>
                <th>Sobrenome</th>
                <th>Endereço</th>
                <th>Telefone</th>
                <th>Escolhidos</th>
                <th>Ações</th>
              </tr>";
        echo "</thead>";
        echo "<tbody>";

        while ($linha = $resultado->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($linha['codigo']) . "</td>";
            echo "<td>" . htmlspecialchars($linha['nome']) . "</td>";
            echo "<td>" . htmlspecialchars($linha['sobrenome']) . "</td>";
            echo "<td>" . htmlspecialchars($linha['endereco']) . "</td>";
            echo "<td>" . htmlspecialchars($linha['telefone']) . "</td>";
            echo "<td>" . htmlspecialchars($linha['escolhidos']) . "</td>";
            echo "<td>
                    <a href='editar.php?codigo=" . $linha['codigo'] . "' class='btn-acao btn-editar'>Editar</a>
                    <a href='edicao.php?codigo=" . $linha['codigo'] . "' class='btn-acao btn-excluir' onclick=\"return confirm('Tem certeza que deseja excluir este registro?');\">Excluir</a>
                  </td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
        echo "<div class='total-registros'>Total de registros: " . $resultado->num_rows . "</div>";
    } else {
        echo "<div class='erro-mensagem'>Nenhum registro encontrado para '" . htmlspecialchars($busca) . "'.</div>";
    }
    $stmt->close();
}

$conn->close();
?>

</body>
</html>