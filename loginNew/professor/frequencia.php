
<?php
session_start();
$host = "localhost";
$usuario = "root";
$senha = "admin";
$banco = "dados";

$conexao = new mysqli($host, $usuario, $senha, $banco);
if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}

$mensagemSucesso = "";
$dataHoje = date('Y-m-d');

// Processar o salvamento da frequência
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar_frequencia'])) {
    $disciplina = $_POST['disciplina'] ?? 'Geral';

    if (isset($_POST['chamada']) && is_array($_POST['chamada'])) {
        $stmt = $conexao->prepare("INSERT INTO `frequencia` (`id_aluno`, `data`, `disciplina`, `status`) VALUES (?, ?, ?, ?)
                                   ON DUPLICATE KEY UPDATE `status` = VALUES(`status`)");

        foreach ($_POST['chamada'] as $idAluno => $status) {
            $id = intval($idAluno);
            $stmt->bind_param("isss", $id, $dataHoje, $disciplina, $status);
            $stmt->execute();
        }

        $stmt->close();
        $mensagemSucesso = "Frequência do dia " . date('d/m/Y') . " salva com sucesso!";
    }
}

// Buscar todos os alunos cadastrados
$sqlAlunos = "SELECT `codigo`, `nome`, `sobrenome`, `escola`, `ano_escolar` FROM `dados`";
$resultadoAlunos = $conexao->query($sqlAlunos);

$alunos = [];

if ($resultadoAlunos && $resultadoAlunos->num_rows > 0) {
    while ($row = $resultadoAlunos->fetch_assoc()) {
        $alunos[] = $row;
    }
}

$conexao->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Lançamento de Frequência - Professor</title>

    <link rel="icon" type="image/png" href="img/logo-parana.png">

    <link rel="stylesheet" href="estilo-professor.css">
</head>

<body class="pagina-frequencia">

    <!-- CABEÇALHO -->
    <header class="header">

        <nav class="nav">

            <div class="nav-brand">

                <div class="user-badge">
                    👤 Olá, <?= htmlspecialchars($_SESSION['professor_nome'] ?? 'Professor', ENT_QUOTES, 'UTF-8') ?>
                    | <a href="../logout.php">Sair</a>
                </div>

                <img
                    src="img/logo-parana.png"
                    alt="Logotipo do Governo do Paraná"
                    class="nav-logo"
                >

                <h1>CLIRK ACADEMY</h1>

            </div>

            <button
                type="button"
                class="menu-toggle"
                id="menuToggle"
                aria-label="Abrir menu de navegação"
                aria-expanded="false"
                aria-controls="navItems"
            >
                <img src="img/menu-icon.png" alt="">
            </button>

            <div class="nav-items" id="navItems">

                <a href="aprovar_alunos.php" class="nav-button">
                    Aceitar alunos
                </a>

                <a href="index2.php" class="nav-button">
                    Início
                </a>

                <a href="frequencia.php" class="nav-button active">
                    Frequência
                </a>

                <a href="notas.php" class="nav-button">
                    Notas
                </a>

                <a href="planos.php" class="nav-button">
                    Plano de Aula
                </a>

            </div>

        </nav>

    </header>

    <!-- BANNER -->
    <section class="banner">

        <div class="banner-content">

            <h2>Controle de Frequência Diária</h2>

            <p>
                Faça a chamada dos alunos de forma rápida
                e acompanhe a frequência escolar.
            </p>

        </div>

    </section>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="main">

        <h2 class="section-title">Registro de Frequência</h2>

        <div class="box-card">

            <div class="card-body">

                <h3 class="fig">Lista de Chamada</h3>

                <?php if (!empty($mensagemSucesso)): ?>

                    <div class="mensagem sucesso">
                        <?php echo htmlspecialchars($mensagemSucesso, ENT_QUOTES, 'UTF-8'); ?>
                    </div>

                <?php endif; ?>

                <p class="card-desc">
                    Data de hoje:
                    <strong><?php echo date('d/m/Y'); ?></strong>
                </p>

                <form method="POST" action="">

                    <div class="frequencia-disciplina">

                        <label for="disciplina">
                            Disciplina:
                        </label>

                        <select name="disciplina" id="disciplina">

                            <option value="Matemática">Matemática</option>
                            <option value="Português">Língua Portuguesa</option>
                            <option value="História">História</option>

                        </select>

                    </div>

                    <div class="tabela-container">

                        <table>

                            <thead>
                                <tr>
                                    <th>Cód.</th>
                                    <th>Nome do Aluno</th>
                                    <th>Escola / Ano</th>
                                    <th>Presença / Ausência</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php if (empty($alunos)): ?>

                                    <tr>
                                        <td colspan="4" style="text-align:center;">
                                            Nenhum aluno cadastrado.
                                        </td>
                                    </tr>

                                <?php else: ?>

                                    <?php foreach ($alunos as $aluno): ?>

                                        <tr>

                                            <td>
                                                <?php echo (int)$aluno['codigo']; ?>
                                            </td>

                                            <td>
                                                <strong>
                                                    <?php echo htmlspecialchars($aluno['nome'] . ' ' . $aluno['sobrenome'], ENT_QUOTES, 'UTF-8'); ?>
                                                </strong>
                                            </td>

                                            <td>
                                                <?php echo htmlspecialchars($aluno['escola'] . ' (' . $aluno['ano_escolar'] . ')', ENT_QUOTES, 'UTF-8'); ?>
                                            </td>

                                            <td>

                                                <select name="chamada[<?php echo (int)$aluno['codigo']; ?>]">

                                                    <option value="Presente">Presente</option>
                                                    <option value="Ausente">Ausente</option>

                                                </select>

                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                    <button
                        type="submit"
                        name="salvar_frequencia"
                        class="card-btn"
                    >
                        Salvar Chamada
                    </button>

                </form>

            </div>

        </div>

    </main>

    <!-- RODAPÉ -->
    <footer class="footer">

        <p>
            <strong>
                © Governo do Estado do Paraná - Secretaria da Educação (SEED-PR)
            </strong>
        </p>

    </footer>

    <!-- MENU RESPONSIVO -->
    <script>

        const menuToggle = document.getElementById('menuToggle');
        const navItems = document.getElementById('navItems');

        menuToggle.addEventListener('click', function () {

            const aberto = navItems.classList.toggle('open');

            menuToggle.setAttribute('aria-expanded', aberto);

            menuToggle.setAttribute(
                'aria-label',
                aberto ? 'Fechar menu de navegação' : 'Abrir menu de navegação'
            );

        });

    </script>

</body>
</html>
