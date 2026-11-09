<?php
// Configura e inicia a sessão na raiz do site
session_set_cookie_params(0, '/');
session_start();

// Recupera o nome do utilizador guardado na sessão pelo logado.php
$nomeUsuario = $_SESSION['usuario_nome'] ?? null;
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <title>Portal Unificado de Estudos - Paraná</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/png" href="img/logo-parana.png">
        <link rel="stylesheet" type="text/css" href="../../style.css">
        <style>
            /* Caixa destacada com o nome do utilizador logado */
            .user-identificacao {
                display: flex;
                align-items: center;
                gap: 8px;
                color: #ffffff;
                font-weight: bold;
                font-size: 14px;
                background-color: #004080;
                border: 1px solid #0056b3;
                padding: 6px 14px;
                border-radius: 20px;
                margin-right: 15px;
            }
            .btn-logout-topo {
                color: #ff8080;
                text-decoration: none;
                font-size: 12px;
                margin-left: 8px;
                border-left: 1px solid rgba(255,255,255,0.4);
                padding-left: 8px;
            }
            .btn-logout-topo:hover {
                text-decoration: underline;
                color: #ffffff;
            }
        </style>
    </head>
    <body>
        <!-- Cabeçalho -->
        <header class="header">
            <nav class="nav">
                <div class="nav-brand" style="display: flex; align-items: center;">
                    <!-- EXIBIÇÃO DO NOME DO UTILIZADOR LOGADO -->
                    <?php if (!empty($nomeUsuario)): ?>
                        <div class="user-identificacao">
                            <span>👤 Olá, <?php echo htmlspecialchars($nomeUsuario); ?></span>
                            <a href="../logout.php" class="btn-logout-topo">Sair</a>
                        </div>
                    <?php endif; ?>

                    <img src="img/logo-parana.png" alt="Logotipo do Governo do Paraná" class="nav-logo">
                    <h1>Clirk Academy</h1>
                </div>

                <div class="nav-items">
                    <button class="manu"><img src="img/menu-icon.png" alt="Menu"></button>
                    <a href="index.php" class="nav-button active">Início</a>
                    <a href="plataformas.html" class="nav-button">Plataformas</a>
                    <a href="tutoriais.html" class="nav-button">Como Acessar</a>
                    <a href="sobre.html" class="nav-button">Sobre</a>
                    <a href="notas.php" class="nav-button active">Ver notas</a>
                    <a href="frequencia.php" class="nav-button active">Chamada</a>
                    
                    <!-- Oculta os botões de cadastro/login quando já estiver logado -->
                    <?php if (empty($nomeUsuario)): ?>
                        <a href="loginNew/cad_usuario.php" class="nav-button btn-login">Create @escola</a>
                        <a href="loginNew/index.php" class="nav-button btn-login">Acesso @escola</a>
                    <?php endif; ?>
                </div>
            </nav>
        </header>

        <!-- Banner Principal -->
        <section class="banner">
            <div class="banner-content">
                <h2>Portal Unificado de Estudo do Paraná</h2>
                <p>Acesse todas as plataformas educacionais da rede estadual em um só lugar com sua conta @escola.pr.gov.br</p>
            </div>
        </section>

        <!-- Conteúdo Principal -->
        <main class="main">
            <h2 class="text">Plataformas Digitais</h2>
            
            <div class="atractions">
                <!-- Card 1 -->
                <div class="at box-card">
                    <div class="card-header">
                        <img src="img/redacao-parana.png" alt="Redação Paraná" class="at-img">
                    </div>
                    <div class="card-body">
                        <figcaption class="fig">Redação Paraná</figcaption>
                        <p class="card-desc">Produza e corrija suas redações com auxílio de inteligência artificial e feedback de professores.</p>
                        <a href="https://redacao.pr.gov.br" target="_blank" class="card-btn">Acessar Plataforma</a>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="at box-card">
                    <div class="card-header">
                        <img src="img/matific.png" alt="Matific" class="at-img">
                    </div>
                    <div class="card-body">
                        <figcaption class="fig">Matific</figcaption>
                        <p class="card-desc">Atividades interativas e jogos pedagógicos para desenvolver o raciocínio lógico-matemático.</p>
                        <a href="https://www.matific.com" target="_blank" class="card-btn">Acessar Plataforma</a>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="at box-card">
                    <div class="card-header">
                        <img src="img/leia-parana.png" alt="Leia Paraná" class="at-img">
                    </div>
                    <div class="card-body">
                        <figcaption class="fig">Leia Paraná</figcaption>
                        <p class="card-desc">Biblioteca virtual com acervo de livros didáticos, literatura e materiais de leitura continuada.</p>
                        <a href="https://leia.pr.gov.br" target="_blank" class="card-btn">Acessar Plataforma</a>
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="at box-card">
                    <div class="card-header">
                        <img src="img/edutech.png" alt="Edutech" class="at-img">
                    </div>
                    <div class="card-body">
                        <figcaption class="fig">Edutech & Robótica</figcaption>
                        <p class="card-desc">Cursos de programação, pensamento computacional, desenvolvimento de games e robótica.</p>
                        <a href="https://www.educacao.pr.gov.br/edutech" target="_blank" class="card-btn">Acessar Plataforma</a>
                    </div>
                </div>
            </div>
        </main>

        <!-- Rodapé -->
        <footer class="footer">
            <p><strong>© Governo do Estado do Paraná - Secretaria da Educação (SEED-PR)</strong></p>
        </footer>
    </body>
</html>