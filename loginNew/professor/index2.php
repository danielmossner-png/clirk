
<?php
session_set_cookie_params(0, '/');
session_start();

// Proteção da página
if (!isset($_SESSION['professor_id'])) {
    $_SESSION['msg'] = "<p style='color:#ff4d4d; text-align:center;'>Acesso negado! Faça login como professor.</p>";
    header("Location: index.php");
    exit();
}

$nomeUsuario = $_SESSION['professor_nome'] ?? 'Professor';
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Portal do Docente - Paraná</title>

    <link rel="icon" type="image/png" href="img/logo-parana.png">

    <link rel="stylesheet" href="estilo-professor.css">
</head>

<body class="pagina-inicial">

    <!-- CABEÇALHO -->
    <header class="header">

        <nav class="nav">

            <div class="nav-brand">

                <div class="user-badge">
                    👤 Olá, <?= htmlspecialchars($nomeUsuario, ENT_QUOTES, 'UTF-8') ?>
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

                <a href="index2.php" class="nav-button active">
                    Início
                </a>

                <a href="frequencia.php" class="nav-button">
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

    <!-- BANNER PRINCIPAL -->
    <section class="banner">

        <div class="banner-content">

            <h2>Painel do Professor - Paraná</h2>

            <p>
                Gerencie suas turmas, registre chamadas diárias,
                lance avaliações e acompanhe o desempenho com sua
                conta @professor.pr.gov.br.
            </p>

        </div>

    </section>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="main">

        <h2 class="section-title">Gestão Escolar</h2>

        <div class="atractions">

            <!-- CARD 1: CHAMADA -->
            <div class="box-card">

                <div class="card-header">
                    <img
                        src="img/redacao-parana.png"
                        alt="Registro de Chamada"
                        class="at-img"
                    >
                </div>

                <div class="card-body">

                    <h3 class="fig">Registro de Chamada</h3>

                    <p class="card-desc">
                        Realize a chamada diária dos alunos, registre
                        presenças, faltas justificadas e acompanhe
                        a assiduidade.
                    </p>

                    <a href="frequencia.php" class="card-btn">
                        Fazer Chamada
                    </a>

                </div>

            </div>

            <!-- CARD 2: NOTAS -->
            <div class="box-card">

                <div class="card-header">
                    <img
                        src="img/matific.png"
                        alt="Lançamento de Notas"
                        class="at-img"
                    >
                </div>

                <div class="card-body">

                    <h3 class="fig">Lançamento de Notas</h3>

                    <p class="card-desc">
                        Registre as notas das avaliações, trabalhos
                        e atividades do trimestre por turma
                        e disciplina.
                    </p>

                    <a href="notas.php" class="card-btn">
                        Lançar Notas
                    </a>

                </div>

            </div>

            <!-- CARD 3: PLANO DE AULA -->
            <div class="box-card">

                <div class="card-header">
                    <img
                        src="img/leia-parana.png"
                        alt="Plano de Aula"
                        class="at-img"
                    >
                </div>

                <div class="card-body">

                    <h3 class="fig">Plano de Aula & Conteúdo</h3>

                    <p class="card-desc">
                        Cadastre o planejamento didático, registre
                        os conteúdos ministrados na aula e acesse
                        o diário.
                    </p>

                    <a href="planos.php" class="card-btn">
                        Registrar Aula
                    </a>

                </div>

            </div>

            <!-- CARD 4: RELATÓRIOS -->
            <div class="box-card">

                <div class="card-header">
                    <img
                        src="img/edutech.png"
                        alt="Relatórios Acadêmicos"
                        class="at-img"
                    >
                </div>

                <div class="card-body">

                    <h3 class="fig">Relatórios da Turma</h3>

                    <p class="card-desc">
                        Acompanhe estatísticas de desempenho,
                        médias gerais da turma e relatórios de
                        alunos com risco de reprovação.
                    </p>

                    <a href="relatorios.php" class="card-btn">
                        Ver Relatórios
                    </a>

                </div>

            </div>

            <!-- CARD 5: APROVAÇÃO DE ALUNOS -->
            <div class="box-card">

                <div class="card-header">
                    <img
                        src="img/edutech.png"
                        alt="Gerenciamento de alunos"
                        class="at-img"
                    >
                </div>

                <div class="card-body">

                    <h3 class="fig">Aceitar Alunos</h3>

                    <p class="card-desc">
                        Gerencie os cadastros dos alunos da sua
                        escola e aprove as solicitações pendentes.
                    </p>

                    <a href="aprovar_alunos.php" class="card-btn">
                        Acessar
                    </a>

                </div>

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
