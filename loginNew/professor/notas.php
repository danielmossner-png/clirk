<?php
session_set_cookie_params(0, '/');
session_start();

// Configurações de Conexão com o MySQL
$host = "localhost";
$usuario = "root"; 
$senha = "admin"; 
$banco = "dados"; 

// 1. Criar Conexão
$conexao = new mysqli($host, $usuario, $senha, $banco);

if ($conexao->connect_error) {
    die("Falha na conexão com a base de dados: " . $conexao->connect_error);
}

// 2. Processar a Gravação das Notas (POST)
$mensagemSucesso = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar_notas'])) {
    if (isset($_POST['notas']) && is_array($_POST['notas'])) {
        // Usa INSERT ... ON DUPLICATE KEY UPDATE para inserir se não existir ou atualizar se já existir
        $stmt = $conexao->prepare("INSERT INTO `notas escolares` (`idnotas escolares`, `nota 1`, `nota 2`, `nota 3`, `media`, `situação`) 
                                   VALUES (?, ?, ?, ?, ?, ?) 
                                   ON DUPLICATE KEY UPDATE 
                                   `nota 1` = VALUES(`nota 1`), 
                                   `nota 2` = VALUES(`nota 2`), 
                                   `nota 3` = VALUES(`nota 3`), 
                                   `media` = VALUES(`media`), 
                                   `situação` = VALUES(`situação`)");

        foreach ($_POST['notas'] as $codigo => $dadosAluno) {
            $nota1 = floatval($dadosAluno['nota1']);
            $nota2 = floatval($dadosAluno['nota2']);
            $nota3 = floatval($dadosAluno['nota3']);

            $media = ($nota1 + $nota2 + $nota3) / 3;
            $situacao = ($media >= 6.0) ? 'Aprovado' : 'Recuperação';

            $cod_int = intval($codigo);
            $n1_str = strval($nota1);
            $n2_str = strval($nota2);
            $n3_str = strval($nota3);
            $media_str = strval(number_format($media, 1, '.', ''));

            // Tipos: i (int para id) e 5 strings (sssss)
            $stmt->bind_param("isssss", $cod_int, $n1_str, $n2_str, $n3_str, $media_str, $situacao);
            $stmt->execute();
        }

        $stmt->close();
        $mensagemSucesso = "Notas guardadas/atualizadas com sucesso na tabela 'notas escolares'!";
    }
}

// 3. Consulta SQL unindo os dados cadastrais com as notas
$sql = "SELECT d.`codigo`, d.`nome`, d.`sobrenome`, d.`email`, d.`escola`, d.`ano_escolar`, 
               n.`nota 1`, n.`nota 2`, n.`nota 3`, n.`media`, n.`situação` 
        FROM `dados` d 
        LEFT JOIN `notas escolares` n ON d.`codigo` = n.`idnotas escolares`";

$resultado = $conexao->query($sql);

$alunos = array();
if ($resultado && $resultado->num_rows > 0) {
    while($row = $resultado->fetch_assoc()) {
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

    <title>Lançamento de Notas - CLIRK ACADEMY</title>

    <link rel="icon" type="image/png" href="img/logo-parana.png">
    <link rel="stylesheet" href="estilo-professor.css">
</head>

<body class="pagina-notas">

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

                <a href="frequencia.php" class="nav-button">
                    Frequência
                </a>

                <a href="notas.php" class="nav-button active">
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

            <h2>Painel do Professor - Paraná</h2>

            <p>
                Gerencie as avaliações e acompanhe o desempenho dos alunos.
            </p>

        </div>

    </section>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="main">

        <h2 class="section-title">Lançamento de Notas</h2>

        <div class="box-card">

            <div class="card-body">

                <h3 class="fig">Notas dos Alunos</h3>

                <p class="card-desc">
                    Registre as três avaliações de cada aluno.
                    A média e a situação são calculadas automaticamente.
                </p>

                <?php if (!empty($mensagemSucesso)): ?>

                    <div class="mensagem sucesso">
                        <?php echo $mensagemSucesso; ?>
                    </div>

                <?php endif; ?>

                <form method="POST" action="">

                    <!-- FILTROS -->
                    <div class="notas-filtros">

                        <div class="notas-filtro-grupo">

                            <label for="select-turma">
                                Ano Escolar / Turma
                            </label>

                            <select id="select-turma">
                                <option value="todos">Todas as Turmas</option>
                            </select>

                        </div>

                        <div class="notas-filtro-grupo">

                            <label for="select-disciplina">
                                Disciplina
                            </label>

                            <select id="select-disciplina">
                                <option value="matematica">Matemática</option>
                                <option value="portugues">Língua Portuguesa</option>
                            </select>

                        </div>

                    </div>

                    <!-- TABELA DE NOTAS -->
                    <div class="notas-tabela-container">

                        <table class="tabela-notas">

                            <thead>
                                <tr>
                                    <th>Cód.</th>
                                    <th>Nome do Aluno</th>
                                    <th>Email</th>
                                    <th>Escola</th>
                                    <th>Ano</th>
                                    <th>Nota 1</th>
                                    <th>Nota 2</th>
                                    <th>Nota 3</th>
                                    <th>Média</th>
                                    <th>Situação</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php if (empty($alunos)): ?>

                                    <tr>
                                        <td colspan="10" class="notas-vazio">
                                            Nenhum aluno encontrado na base de dados.
                                        </td>
                                    </tr>

                                <?php else: ?>

                                    <?php foreach ($alunos as $aluno): 

                                        $codigo = $aluno['codigo'];

                                        $n1 = isset($aluno['nota 1']) && $aluno['nota 1'] !== ''
                                            ? floatval($aluno['nota 1']) : 0.0;

                                        $n2 = isset($aluno['nota 2']) && $aluno['nota 2'] !== ''
                                            ? floatval($aluno['nota 2']) : 0.0;

                                        $n3 = isset($aluno['nota 3']) && $aluno['nota 3'] !== ''
                                            ? floatval($aluno['nota 3']) : 0.0;

                                        $media = ($n1 + $n2 + $n3) / 3;
                                        $aprovado = ($media >= 6.0);
                                        $situacaoTexto = $aprovado ? 'Aprovado' : 'Recuperação';

                                    ?>

                                        <tr>

                                            <td class="notas-codigo">
                                                <?php echo htmlspecialchars($codigo); ?>
                                            </td>

                                            <td class="notas-nome">
                                                <strong>
                                                    <?php echo htmlspecialchars($aluno['nome'] . ' ' . $aluno['sobrenome']); ?>
                                                </strong>
                                            </td>

                                            <td>
                                                <?php echo htmlspecialchars($aluno['email'] ?? '-'); ?>
                                            </td>

                                            <td>
                                                <?php echo htmlspecialchars($aluno['escola'] ?? '-'); ?>
                                            </td>

                                            <td>
                                                <?php echo htmlspecialchars($aluno['ano_escolar'] ?? '-'); ?>
                                            </td>

                                            <td>
                                                <input
                                                    type="number"
                                                    step="0.1"
                                                    min="0"
                                                    max="10"
                                                    name="notas[<?php echo $codigo; ?>][nota1]"
                                                    value="<?php echo $n1; ?>"
                                                    onchange="calcularMediaLinha(this)"
                                                    aria-label="Nota 1 de <?php echo htmlspecialchars($aluno['nome']); ?>"
                                                >
                                            </td>

                                            <td>
                                                <input
                                                    type="number"
                                                    step="0.1"
                                                    min="0"
                                                    max="10"
                                                    name="notas[<?php echo $codigo; ?>][nota2]"
                                                    value="<?php echo $n2; ?>"
                                                    onchange="calcularMediaLinha(this)"
                                                    aria-label="Nota 2 de <?php echo htmlspecialchars($aluno['nome']); ?>"
                                                >
                                            </td>

                                            <td>
                                                <input
                                                    type="number"
                                                    step="0.1"
                                                    min="0"
                                                    max="10"
                                                    name="notas[<?php echo $codigo; ?>][nota3]"
                                                    value="<?php echo $n3; ?>"
                                                    onchange="calcularMediaLinha(this)"
                                                    aria-label="Nota 3 de <?php echo htmlspecialchars($aluno['nome']); ?>"
                                                >
                                            </td>

                                            <td class="media-val">
                                                <strong>
                                                    <?php echo number_format($media, 1, '.', ''); ?>
                                                </strong>
                                            </td>

                                            <td>
                                                <span class="status-badge <?php echo $aprovado ? 'status-aprovado' : 'status-recuperacao'; ?>">
                                                    <?php echo $situacaoTexto; ?>
                                                </span>
                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                    <!-- BOTÃO -->
                    <div class="notas-acoes">

                        <button
                            type="submit"
                            name="salvar_notas"
                            class="card-btn"
                        >
                            Salvar Notas
                        </button>

                    </div>

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

        // Cálculo automático da média
        function calcularMediaLinha(input) {

            const tr = input.closest('tr');
            const inputs = tr.querySelectorAll('input[type="number"]');

            const n1 = parseFloat(inputs[0].value) || 0;
            const n2 = parseFloat(inputs[1].value) || 0;
            const n3 = parseFloat(inputs[2].value) || 0;

            const media = (n1 + n2 + n3) / 3;
            const aprovado = media >= 6.0;

            const mediaTd = tr.querySelector('.media-val strong');
            const statusBadge = tr.querySelector('.status-badge');

            mediaTd.textContent = media.toFixed(1);

            if (aprovado) {
                statusBadge.textContent = 'Aprovado';
                statusBadge.className = 'status-badge status-aprovado';
            } else {
                statusBadge.textContent = 'Recuperação';
                statusBadge.className = 'status-badge status-recuperacao';
            }

        }

    </script>

</body>
</html>
