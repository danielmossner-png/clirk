<?php
session_set_cookie_params(0, '/');
session_start();

include_once("conexao.php");

// Verifica se o professor está logado
if (!isset($_SESSION['professor_id'])) {
    header("Location: index.php");
    exit();
}

// Token de segurança
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$professor_id = $_SESSION['professor_id'];
$mensagem = "";
$tipo_mensagem = "";

// Busca a escola do professor
$sql_escola = "SELECT escola FROM users WHERE id_users = ? LIMIT 1";
$stmt_escola = mysqli_prepare($conn, $sql_escola);
mysqli_stmt_bind_param($stmt_escola, "i", $professor_id);
mysqli_stmt_execute($stmt_escola);

$resultado_escola = mysqli_stmt_get_result($stmt_escola);
$professor = mysqli_fetch_assoc($resultado_escola);
mysqli_stmt_close($stmt_escola);

if (!$professor) {
    die("Professor não encontrado.");
}

$escola_professor = $professor['escola'];

// Processa aprovação ou rejeição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token = $_POST['csrf_token'] ?? '';
    $aluno_id = filter_input(INPUT_POST, 'aluno_id', FILTER_VALIDATE_INT);
    $novo_status = $_POST['status'] ?? '';

    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        $mensagem = "Requisição inválida. Atualize a página e tente novamente.";
        $tipo_mensagem = "erro";

    } elseif (!$aluno_id || !in_array($novo_status, ['aprovado', 'rejeitado'], true)) {
        $mensagem = "Dados inválidos.";
        $tipo_mensagem = "erro";

    } else {
        $sql_update = "UPDATE dados
                       SET status_cadastro = ?
                       WHERE codigo = ?
                       AND escola = ?
                       AND status_cadastro = 'pendente'";

        $stmt_update = mysqli_prepare($conn, $sql_update);

        mysqli_stmt_bind_param(
            $stmt_update,
            "sis",
            $novo_status,
            $aluno_id,
            $escola_professor
        );

        mysqli_stmt_execute($stmt_update);

        if (mysqli_stmt_affected_rows($stmt_update) > 0) {
            $mensagem = $novo_status === 'aprovado'
                ? "Aluno aprovado com sucesso!"
                : "Cadastro rejeitado com sucesso!";

            $tipo_mensagem = "sucesso";

        } else {
            $mensagem = "Não foi possível alterar o cadastro. Verifique se o aluno pertence à sua escola e ainda está pendente.";
            $tipo_mensagem = "erro";
        }

        mysqli_stmt_close($stmt_update);
    }
}

// Busca somente alunos pendentes da escola do professor
$sql_alunos = "SELECT codigo, nome, sobrenome, email, cidade, escola, ano_escolar
               FROM dados
               WHERE escola = ?
               AND status_cadastro = 'pendente'
               ORDER BY codigo DESC";

$stmt_alunos = mysqli_prepare($conn, $sql_alunos);
mysqli_stmt_bind_param($stmt_alunos, "s", $escola_professor);
mysqli_stmt_execute($stmt_alunos);

$resultado_alunos = mysqli_stmt_get_result($stmt_alunos);

function escapar($valor) {
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}

$total_alunos = mysqli_num_rows($resultado_alunos);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Aprovação de Alunos - Paraná</title>

    <link rel="icon" type="image/png" href="img/logo-parana.png">

    <link rel="stylesheet" href="estilo-professor.css">
</head>

<body class="pagina-aprovacao">

    <!-- CABEÇALHO -->
    <header class="header">
        <nav class="nav">

            <div class="nav-brand">

                <div class="user-badge">
                    👤 Olá, <?= escapar($_SESSION['professor_nome'] ?? 'Professor') ?>
                    | <a href="../logout.php">Sair</a>
                </div>

                <img src="img/logo-parana.png"
                     alt="Logotipo do Governo do Paraná"
                     class="nav-logo">

                <h1>CLIRK ACADEMY</h1>

            </div>

            <div class="nav-items">
                <a href="aprovar_alunos.php" class="nav-button active">Aceitar alunos</a>
                <a href="index2.php" class="nav-button">Início</a>
                <a href="frequencia.php" class="nav-button">Frequência</a>
                <a href="notas.php" class="nav-button">Notas</a>
                <a href="planos.php" class="nav-button">Plano de Aula</a>
            </div>

        </nav>
    </header>

    <!-- BANNER -->
    <section class="banner">
        <div>
            <h2>Gerenciamento de Cadastros</h2>
            <p>
                Analise as solicitações de cadastro dos alunos da sua escola,
                aprove ou rejeite os registros pendentes.
            </p>
        </div>
    </section>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="main">

        <h2 class="titulo-secao">Aprovação de Alunos</h2>

        <p class="escola">
            Escola: <strong><?= escapar($escola_professor) ?></strong>
        </p>

        <div class="contador">
            <span>
                <?= $total_alunos ?>
                <?= $total_alunos === 1 ? 'cadastro pendente' : 'cadastros pendentes' ?>
            </span>
        </div>

        <?php if ($mensagem !== ''): ?>
            <div class="mensagem <?= escapar($tipo_mensagem) ?>">
                <?= escapar($mensagem) ?>
            </div>
        <?php endif; ?>

        <?php if ($total_alunos > 0): ?>

            <div class="grade-alunos">

                <?php while ($aluno = mysqli_fetch_assoc($resultado_alunos)): ?>

                    <div class="aluno">

                        <span class="status-pendente">● PENDENTE</span>

                        <h3>
                            <?= escapar($aluno['nome'] . ' ' . $aluno['sobrenome']) ?>
                        </h3>

                        <div class="dados-aluno">

                            <p>
                                <strong>E-mail:</strong>
                                <?= escapar($aluno['email']) ?>
                            </p>

                            <p>
                                <strong>Cidade:</strong>
                                <?= escapar($aluno['cidade']) ?>
                            </p>

                            <p>
                                <strong>Ano escolar:</strong>
                                <?= escapar($aluno['ano_escolar']) ?>
                            </p>

                        </div>

                        <form method="POST" class="acoes">

                            <input type="hidden"
                                   name="csrf_token"
                                   value="<?= escapar($_SESSION['csrf_token']) ?>">

                            <input type="hidden"
                                   name="aluno_id"
                                   value="<?= (int)$aluno['codigo'] ?>">

                            <button class="aprovar"
                                    type="submit"
                                    name="status"
                                    value="aprovado">
                                ✓ Aprovar
                            </button>

                            <button class="rejeitar"
                                    type="submit"
                                    name="status"
                                    value="rejeitado">
                                ✕ Rejeitar
                            </button>

                        </form>

                    </div>

                <?php endwhile; ?>

            </div>

        <?php else: ?>

            <div class="vazio">
                <div class="vazio-icon">✓</div>

                <h3>Tudo em dia!</h3>

                <p>
                    Não existem cadastros pendentes nesta escola.
                    Novas solicitações aparecerão aqui.
                </p>
            </div>

        <?php endif; ?>

        <a href="index2.php" class="voltar">
            ← Voltar ao painel
        </a>

    </main>

    <!-- RODAPÉ -->
    <footer class="footer">
        <p>
            <strong>
                © Governo do Estado do Paraná - Secretaria da Educação (SEED-PR)
            </strong>
        </p>
    </footer>

</body>
</html>

<?php
mysqli_stmt_close($stmt_alunos);
?>
