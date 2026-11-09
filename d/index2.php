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
        <title>Portal do Docente - Paraná</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/png" href="img/logo-parana.png">
        
        <style>
            /* Resposta Global e Fontes */
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: Arial, Helvetica, sans-serif;
            }

            body {
                background-color: #f4f6f8;
                color: #333333;
            }

            /* --- Cabeçalho (Header) --- */
            .header {
                background-color: #ffffff;
                border-bottom: 1px solid #e0e0e0;
                padding: 10px 40px;
            }

            .nav {
                display: flex;
                justify-content: space-between;
                align-items: center;
                max-width: 1400px;
                margin: 0 auto;
            }

            .nav-brand {
                display: flex;
                align-items: center;
                gap: 15px;
            }

            /* Crachá do perfil do utilizador */
            .user-badge {
                background-color: #003366;
                color: #ffffff;
                padding: 6px 14px;
                border-radius: 20px;
                font-size: 13px;
                font-weight: bold;
                display: flex;
                align-items: center;
                gap: 6px;
            }

            .user-badge a {
                color: #ff6b6b;
                text-decoration: none;
                margin-left: 4px;
            }

            .user-badge a:hover {
                text-decoration: underline;
            }

            .nav-logo {
                height: 38px;
                width: auto;
            }

            .nav-brand h1 {
                font-size: 20px;
                color: #004d80;
                font-weight: 800;
                letter-spacing: 0.5px;
            }

            .nav-items {
                display: flex;
                align-items: center;
                gap: 15px;
            }

            .manu {
                display: none;
            }

            .nav-button {
                text-decoration: none;
                color: #004d80;
                font-size: 13px;
                font-weight: bold;
                padding: 8px 16px;
                border-radius: 4px;
                transition: background-color 0.2s;
            }

            .nav-button.active {
                background-color: #00994d;
                color: #ffffff;
            }

            .nav-button:hover:not(.active) {
                background-color: #e6f0fa;
            }

            /* --- Banner Principal --- */
            .banner {
                background: linear-gradient(90deg, #008542 0%, #004d80 100%);
                color: #ffffff;
                text-align: center;
                padding: 45px 20px;
            }

            .banner-content h2 {
                font-size: 26px;
                font-weight: bold;
                margin-bottom: 12px;
            }

            .banner-content p {
                font-size: 13px;
                max-width: 650px;
                margin: 0 auto;
                line-height: 1.5;
                opacity: 0.95;
            }

            /* --- Conteúdo Principal e Cards --- */
            .main {
                max-width: 1000px;
                margin: 30px auto 60px auto;
                padding: 0 20px;
            }

            .main .text {
                text-align: center;
                color: #004d80;
                font-size: 20px;
                font-weight: bold;
                margin-bottom: 30px;
                position: relative;
                display: inline-block;
                left: 50%;
                transform: translateX(-50%);
            }

            .main .text::after {
                content: '';
                display: block;
                width: 40px;
                height: 3px;
                background-color: #00994d;
                margin: 6px auto 0 auto;
                border-radius: 2px;
            }

            /* Grelha dos cartões (2 por linha) */
            .atractions {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 25px;
            }

            /* Estilo do Cartão */
            .at.box-card {
                background-color: #ffffff;
                border-radius: 12px;
                border-top: 4px solid #00994d;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
                overflow: hidden;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                transition: transform 0.2s, box-shadow 0.2s;
            }

            .at.box-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
            }

            .card-header {
                height: 140px;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
                background-color: #ffffff;
            }

            .at-img {
                max-height: 90px;
                max-width: 80%;
                object-fit: contain;
            }

            .card-body {
                padding: 0 25px 25px 25px;
                text-align: center;
                display: flex;
                flex-direction: column;
                flex-grow: 1;
            }

            .fig {
                font-size: 16px;
                font-weight: bold;
                color: #004d80;
                margin-bottom: 10px;
            }

            .card-desc {
                font-size: 12px;
                color: #666666;
                line-height: 1.4;
                margin-bottom: 20px;
                flex-grow: 1;
            }

            .card-btn {
                display: block;
                width: 100%;
                background-color: #00994d;
                color: #ffffff;
                text-decoration: none;
                padding: 10px 0;
                border-radius: 6px;
                font-size: 13px;
                font-weight: bold;
                transition: background-color 0.2s;
            }

            .card-btn:hover {
                background-color: #007a3d;
            }

            /* --- Rodapé --- */
            .footer {
                background-color: #004d80;
                color: #ffffff;
                text-align: center;
                padding: 18px 20px;
                font-size: 12px;
            }

            /* Responsividade móvel */
            @media (max-width: 768px) {
                .atractions {
                    grid-template-columns: 1fr;
                }
                
                .nav-items a {
                    display: none;
                }
                
                .manu {
                    display: block;
                    background: none;
                    border: none;
                    cursor: pointer;
                }
            }
        </style>
    </head>
    <body>
        <!-- Cabeçalho -->
        <header class="header">
            <nav class="nav">
                <div class="nav-brand">
                <div class="user-badge">
    <span>👤 Olá, <?php echo htmlspecialchars($nomeUsuario); ?></span> | <a href="../logout.php">Sair</a>
</div>
                    <img src="img/logo-parana.png" alt="Logotipo do Governo do Paraná" class="nav-logo">
                    <h1>CLIRK ACADEMY</h1>
                </div>

                <div class="nav-items">
                    <button class="manu"><img src="img/menu-icon.png" alt="Menu"></button>
                    <a href="index.php" class="nav-button active">Início</a>
                    <a href="frequencia.php" class="nav-button">Frequência</a>
                    <a href="notas.php" class="nav-button">Notas</a>
                    <a href="planos.php" class="nav-button">Plano de Aula</a>
                </div>
            </nav>
        </header>

        <!-- Banner Principal -->
        <section class="banner">
            <div class="banner-content">
                <h2>Painel do Professor - Paraná</h2>
                <p>Gerencie suas turmas, registre chamadas diárias, lance avaliações e acompanhe o desempenho com sua conta @professor.pr.gov.br</p>
            </div>
        </section>

        <!-- Conteúdo Principal com Cards do Professor -->
        <main class="main">
            <h2 class="text">Gestão Escolar</h2>
            
            <div class="atractions">
                <!-- Card 1: Registro de Chamada -->
                <div class="at box-card">
                    <div class="card-header">
                        <img src="img/redacao-parana.png" alt="Registro de Chamada" class="at-img">
                    </div>
                    <div class="card-body">
                        <figcaption class="fig">Registro de Chamada</figcaption>
                        <p class="card-desc">Realize a chamada diária dos alunos, registre presenças, faltas justificadas e acompanhe a assiduidade.</p>
                        <a href="frequencia.php" class="card-btn">Fazer Chamada</a>
                    </div>
                </div>

                <!-- Card 2: Lançamento de Notas -->
                <div class="at box-card">
                    <div class="card-header">
                        <img src="img/matific.png" alt="Lançamento de Notas" class="at-img">
                    </div>
                    <div class="card-body">
                        <figcaption class="fig">Lançamento de Notas</figcaption>
                        <p class="card-desc">Registre as notas das avaliações, trabalhos e atividades do trimestre por turma e disciplina.</p>
                        <a href="notas.php" class="card-btn">Lançar Notas</a>
                    </div>
                </div>

                <!-- Card 3: Registro de Aulas / RLM -->
                <div class="at box-card">
                    <div class="card-header">
                        <img src="img/leia-parana.png" alt="Plano de Aula" class="at-img">
                    </div>
                    <div class="card-body">
                        <figcaption class="fig">Plano de Aula & Conteúdo</figcaption>
                        <p class="card-desc">Cadastre o planejamento didático, registre os conteúdos ministrados na aula e acesse o diário.</p>
                        <a href="planos.php" class="card-btn">Registrar Aula</a>
                    </div>
                </div>

                <!-- Card 4: Relatórios da Turma -->
                <div class="at box-card">
                    <div class="card-header">
                        <img src="img/edutech.png" alt="Relatórios Acadêmicos" class="at-img">
                    </div>
                    <div class="card-body">
                        <figcaption class="fig">Relatórios da Turma</figcaption>
                        <p class="card-desc">Acompanhe estatísticas de desempenho, médias gerais da turma e relatórios de alunos com risco de reprovação.</p>
                        <a href="relatorios.php" class="card-btn">Ver Relatórios</a>
                    </div>
                </div>

                <!-- Card 5: Aceitar Alunos -->
                <div class="at box-card">
                    <div class="card-header">
                        <img src="img/edutech.png" alt="Relatórios Acadêmicos" class="at-img">
                    </div>
                    <div class="card-body">
                        <figcaption class="fig">Aceitar Alunos</figcaption>
                        <p class="card-desc">Gerencie os cadastros dos alunos da sua escola.</p>
                        <a href="aprovar_alunos.php" class="card-btn">Acessar</a>
                    </div>
                </div>

        <!-- Rodapé -->
        <footer class="footer">
            <p><strong>© Governo do Estado do Paraná - Secretaria da Educação (SEED-PR)</strong></p>
        </footer>
    </body>
</html>