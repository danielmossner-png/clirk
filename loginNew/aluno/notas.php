<?php
// Inicia a sessão
session_start();

// Pega o ID correto que vem da sessão do login (`usuario_id`)
$codigoLogado = $_SESSION['usuario_id'] ?? 0; 
$nomeLogado = $_SESSION['usuario_nome'] ?? 'Aluno';

// Conexão com o banco de dados
$host = "localhost";
$usuario = "root"; 
$senha = "admin"; 
$banco = "dados"; 

$conexao = new mysqli($host, $usuario, $senha, $banco);
if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}

// Consulta unida utilizando a chave estrangeira ligando 'dados' a 'notas escolares'
$stmt = $conexao->prepare("SELECT d.`codigo`, d.`nome`, d.`sobrenome`, d.`email`, d.`escola`, d.`ano_escolar`, 
                                  n.`nota 1`, n.`nota 2`, n.`nota 3`, n.`media`, n.`situação` 
                           FROM `dados` d 
                           LEFT JOIN `notas escolares` n ON d.`codigo` = n.`idnotas escolares` 
                           WHERE d.`codigo` = ?");
$stmt->bind_param("i", $codigoLogado);
$stmt->execute();
$resultado = $stmt->get_result();

$alunoEncontrado = null;
if ($resultado && $resultado->num_rows > 0) {
    $alunoEncontrado = $resultado->fetch_assoc();
}
$stmt->close();
$conexao->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portal do Aluno - CLIRK ACADEMY</title>
  <style>
    :root {
      --navy: #002b49;
      --green: #009d57;
      --green-dark: #007a43;
      --bg-gray: #f4f6f8;
      --card-border: #e2e8f0;
      --text-main: #334155;
      --text-muted: #64748b;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    body { background-color: var(--bg-gray); color: var(--text-main); min-height: 100vh; display: flex; flex-direction: column; }

    header {
      background-color: var(--navy);
      color: white;
      padding: 12px 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .user-badge {
      background: rgba(255, 255, 255, 0.15);
      padding: 6px 14px;
      border-radius: 20px;
      font-size: 0.9rem;
      font-weight: 500;
    }

    .user-badge a { color: #ff8080; text-decoration: none; margin-left: 8px; font-weight: bold; }
    .logo-area { font-weight: 800; font-size: 1.4rem; letter-spacing: 1px; }

    nav { display: flex; gap: 12px; }
    nav a { color: white; text-decoration: none; padding: 6px 16px; border-radius: 4px; font-size: 0.9rem; font-weight: 500; transition: background 0.2s; }
    nav a.active { background-color: var(--green); }

    .hero-banner {
      background: linear-gradient(135deg, #007a43 0%, #002b49 100%);
      color: white;
      text-align: center;
      padding: 36px 16px;
    }
    .hero-banner h1 { font-size: 1.8rem; margin-bottom: 8px; }
    .hero-banner p { font-size: 0.95rem; opacity: 0.9; }

    main { max-width: 900px; margin: -20px auto 40px auto; padding: 0 16px; width: 100%; flex: 1; }

    .card {
      background: white;
      border-radius: 8px;
      border-top: 4px solid var(--green);
      box-shadow: 0 4px 6px rgba(0,0,0,0.05);
      padding: 24px;
      margin-bottom: 24px;
    }

    .card-title { color: var(--navy); font-size: 1.3rem; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 2px solid var(--bg-gray); }

    .student-info-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 16px;
    }

    .info-box { background: var(--bg-gray); padding: 12px; border-radius: 6px; }
    .info-box span { display: block; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600; margin-bottom: 4px; }
    .info-box strong { font-size: 1rem; color: var(--navy); }

    table { width: 100%; border-collapse: collapse; text-align: left; margin-top: 10px; }
    th, td { padding: 12px; border-bottom: 1px solid var(--card-border); }
    th { background-color: #f8fafc; color: var(--navy); font-size: 0.85rem; text-transform: uppercase; }

    .status-badge { padding: 6px 12px; border-radius: 12px; font-size: 0.8rem; font-weight: bold; text-transform: uppercase; }
    .status-aprovado { background: #dcfce7; color: #166534; }
    .status-recuperacao { background: #fee2e2; color: #991b1b; }

    footer { background-color: var(--navy); color: white; text-align: center; padding: 16px; font-size: 0.85rem; }
  </style>
</head>
<body>

  <header>
    <div class="user-badge">
      Olá, <?php echo htmlspecialchars($alunoEncontrado['nome'] ?? $nomeLogado); ?>
      <a href="../logout.php">(Sair)</a>
    </div>
    <div class="logo-area">CLIRK ACADEMY</div>
    <nav>
      <a href="index2.php">Início</a>
      <a href="notas.php" class="active">Notas</a>
    </nav>
  </header>

  <div class="hero-banner">
    <h1>Portal Unificado de Estudo do Paraná</h1>
    <p>Consulte suas notas e boletim escolar de forma rápida e segura.</p>
  </div>

  <main>
    <?php if ($alunoEncontrado): ?>
      <div class="card">
        <div class="card-title">Meus Dados Cadastrais</div>
        <div class="student-info-grid">
          <div class="info-box">
            <span>Nome Completo</span>
            <strong><?php echo htmlspecialchars($alunoEncontrado['nome'] . ' ' . $alunoEncontrado['sobrenome']); ?></strong>
          </div>
          <div class="info-box">
            <span>Escola</span>
            <strong><?php echo htmlspecialchars($alunoEncontrado['escola'] ?? 'Não informada'); ?></strong>
          </div>
          <div class="info-box">
            <span>Ano Escolar / Turma</span>
            <strong><?php echo htmlspecialchars($alunoEncontrado['ano_escolar'] ?? 'Não informado'); ?></strong>
          </div>
        </div>
      </div>

      <div class="card" style="border-top-color: var(--navy);">
        <div class="card-title">Boletim Escolar - Notas e Avaliações</div>
        <div style="overflow-x: auto;">
          <table>
            <thead>
              <tr>
                <th>Nota 1</th>
                <th>Nota 2</th>
                <th>Nota 3</th>
                <th>Média</th>
                <th>Situação</th>
              </tr>
            </thead>
            <tbody>
              <?php 
                $n1 = $alunoEncontrado['nota 1'] !== null ? floatval($alunoEncontrado['nota 1']) : 0.0;
                $n2 = $alunoEncontrado['nota 2'] !== null ? floatval($alunoEncontrado['nota 2']) : 0.0;
                $n3 = $alunoEncontrado['nota 3'] !== null ? floatval($alunoEncontrado['nota 3']) : 0.0;
                $media = $alunoEncontrado['media'] !== null ? floatval($alunoEncontrado['media']) : (($n1 + $n2 + $n3) / 3);
                $situacao = $alunoEncontrado['situação'] ?? ($media >= 6.0 ? 'Aprovado' : 'Recuperação');
                $aprovado = (strcasecmp($situacao, 'Aprovado') === 0);
              ?>
              <tr>
                <td><strong><?php echo number_format($n1, 1, '.', ''); ?></strong></td>
                <td><strong><?php echo number_format($n2, 1, '.', ''); ?></strong></td>
                <td><strong><?php echo number_format($n3, 1, '.', ''); ?></strong></td>
                <td><strong><?php echo number_format($media, 1, '.', ''); ?></strong></td>
                <td>
                  <span class="status-badge <?php echo $aprovado ? 'status-aprovado' : 'status-recuperacao'; ?>">
                    <?php echo htmlspecialchars($situacao); ?>
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    <?php else: ?>
      <div class="card">
        <p style="text-align: center; color: #991b1b;">Nenhum dado de notas encontrado para o aluno ID: <?php echo $codigoLogado; ?>.</p>
      </div>
    <?php endif; ?>
  </main>

  <footer>
    © Governo do Estado do Paraná - Secretaria da Educação (SEED-PR)
  </footer>

</body>
</html>