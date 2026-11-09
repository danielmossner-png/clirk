<?php
session_start();
$codigoLogado = $_SESSION['usuario_id'] ?? 0;
$nomeLogado = $_SESSION['usuario_nome'] ?? 'Aluno';

$host = "localhost";
$usuario = "root"; 
$senha = "admin"; 
$banco = "dados"; 

$conexao = new mysqli($host, $usuario, $senha, $banco);
if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}

// Pega o mês e o ano atuais (ou os selecionados via URL)
$mesAtual = isset($_GET['mes']) ? intval($_GET['mes']) : date('n');
$anoAtual = isset($_GET['ano']) ? intval($_GET['ano']) : date('Y');

// Navegação de meses
if ($mesAtual < 1) { $mesAtual = 12; $anoAtual--; }
if ($mesAtual > 12) { $mesAtual = 1; $anoAtual++; }

// Buscar toda a frequência do aluno no mês/ano selecionado
$stmt = $conexao->prepare("SELECT `data`, `disciplina`, `status` FROM `frequencia` 
                           WHERE `id_aluno` = ? AND MONTH(`data`) = ? AND YEAR(`data` ) = ?");
$stmt->bind_param("iii", $codigoLogado, $mesAtual, $anoAtual);
$stmt->execute();
$resultadoFrequencia = $stmt->get_result();

$registrosPorDia = [];
if ($resultadoFrequencia && $resultadoFrequencia->num_rows > 0) {
    while ($row = $resultadoFrequencia->fetch_assoc()) {
        $data = $row['data'];
        $registrosPorDia[$data][] = [
            'disciplina' => $row['disciplina'],
            'status' => $row['status']
        ];
    }
}
$stmt->close();
$conexao->close();

// Função para retornar feriados fixos nacionais e do Paraná
function obterFeriadosDoAno($ano) {
    return [
        "$ano-01-01" => "Confraternização Universal (Ano Novo)",
        "$ano-04-21" => "Tiradentes",
        "$ano-05-01" => "Dia do Trabalho",
        "$ano-09-07" => "Independência do Brasil",
        "$ano-10-12" => "Nossa Senhora Aparecida",
        "$ano-11-02" => "Finados",
        "$ano-11-15" => "Proclamação da República",
        "$ano-12-25" => "Natal",
        // Feriado Estadual do Paraná (Emancipação Política do Paraná - 19 de Dezembro)
        "$ano-12-19" => "Emancipação Política do Paraná"
    ];
}

$feriadosDoMes = obterFeriadosDoAno($anoAtual);

$diasNoMes = cal_days_in_month(CAL_GREGORIAN, $mesAtual, $anoAtual);
$primeiroDiaSemana = date('w', strtotime("$anoAtual-$mesAtual-01"));
$nomeMeses = [1=>'Janeiro', 2=>'Fevereiro', 3=>'Março', 4=>'Abril', 5=>'Maio', 6=>'Junho', 7=>'Julho', 8=>'Agosto', 9=>'Setembro', 10=>'Outubro', 11=>'Novembro', 12=>'Dezembro'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Calendário de Frequência - CLIRK ACADEMY</title>
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

    .card-title { color: var(--navy); font-size: 1.3rem; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 2px solid var(--bg-gray); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }

    .calendar-nav { display: flex; align-items: center; gap: 15px; font-size: 1.1rem; color: var(--navy); font-weight: bold; }
    .calendar-nav a { background: var(--bg-gray); padding: 5px 12px; border-radius: 4px; color: var(--navy); text-decoration: none; font-size: 0.9rem; border: 1px solid var(--card-border); }
    .calendar-nav a:hover { background: #e2e8f0; }

    .calendar-grid {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      gap: 6px;
      margin-top: 15px;
    }

    .calendar-day-header {
      text-align: center;
      font-weight: bold;
      color: var(--text-muted);
      font-size: 0.85rem;
      padding: 8px 0;
      text-transform: uppercase;
    }

    .calendar-cell {
      background: #f8fafc;
      border: 1px solid var(--card-border);
      border-radius: 6px;
      min-height: 85px;
      padding: 8px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      cursor: pointer;
      transition: all 0.2s;
    }
    .calendar-cell:hover { border-color: var(--navy); box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .calendar-cell.empty { background: transparent; border: none; cursor: default; }

    /* Cores personalizadas dos dias */
    .calendar-cell.tem-falta { background-color: #fee2e2; border-color: #fca5a5; }
    .calendar-cell.fim-de-semana { background-color: #f1f5f9; border-color: #cbd5e1; }
    .calendar-cell.feriado { background-color: #fef3c7; border-color: #fde68a; }

    .day-number {
      font-weight: bold;
      font-size: 0.95rem;
      color: var(--navy);
    }

    .day-status-indicator {
      font-size: 0.7rem;
      font-weight: bold;
      padding: 3px 6px;
      border-radius: 4px;
      text-align: center;
      text-transform: uppercase;
    }
    .indicator-falta { background: #dc2626; color: white; }
    .indicator-feriado { background: #d97706; color: white; }
    .indicator-fds { color: var(--text-muted); font-size: 0.65rem; font-weight: normal; }

    .legend { display: flex; gap: 15px; margin-top: 15px; font-size: 0.82rem; align-items: center; flex-wrap: wrap; }
    .legend-item { display: flex; align-items: center; gap: 6px; }
    .legend-color { width: 14px; height: 14px; border-radius: 3px; }

    /* Modal */
    .modal-overlay {
      display: none;
      position: fixed;
      top: 0; left: 0; width: 100%; height: 100%;
      background: rgba(0, 0, 0, 0.5);
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }
    .modal-content {
      background: white;
      padding: 24px;
      border-radius: 8px;
      width: 100%;
      max-width: 400px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.2);
      border-top: 4px solid var(--navy);
    }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 1px solid var(--card-border); padding-bottom: 8px; }
    .modal-header h3 { color: var(--navy); font-size: 1.1rem; }
    .close-btn { background: none; border: none; font-size: 1.2rem; cursor: pointer; color: var(--text-muted); font-weight: bold; }
    
    .modal-aula-item { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; }
    .badge-p { color: #166534; font-weight: bold; }
    .badge-a { color: #991b1b; font-weight: bold; }

    footer { background-color: var(--navy); color: white; text-align: center; padding: 16px; font-size: 0.85rem; }
  </style>
</head>
<body>

  <header>
    <div class="user-badge">
      Olá, <?php echo htmlspecialchars($nomeLogado); ?>
      <a href="../logout.php">(Sair)</a>
    </div>
    <div class="logo-area">CLIRK ACADEMY</div>
    <nav>
      <a href="index2.php">Início</a>
      <a href="notas.php">Notas</a>
      <a href="frequencia.php" class="active">Frequência</a>
    </nav>
  </header>

  <div class="hero-banner">
    <h1>Portal Unificado de Estudo do Paraná</h1>
    <p>Calendário escolar com destaque para finais de semana e feriados.</p>
  </div>

  <main>
    <div class="card">
      <div class="card-title">
        <span>Calendário de Frequência</span>
        <div class="calendar-nav">
          <a href="frequencia.php?mes=<?php echo $mesAtual - 1; ?>&ano=<?php echo $anoAtual; ?>">&#9664;</a>
          <span><?php echo $nomeMeses[$mesAtual] . ' / ' . $anoAtual; ?></span>
          <a href="frequencia.php?mes=<?php echo $mesAtual + 1; ?>&ano=<?php echo $anoAtual; ?>">&#9654;</a>
        </div>
      </div>

      <div class="calendar-grid">
        <div class="calendar-day-header">Dom</div>
        <div class="calendar-day-header">Seg</div>
        <div class="calendar-day-header">Ter</div>
        <div class="calendar-day-header">Qua</div>
        <div class="calendar-day-header">Qui</div>
        <div class="calendar-day-header">Sex</div>
        <div class="calendar-day-header">Sáb</div>

        <?php
        for ($i = 0; $i < $primeiroDiaSemana; $i++) {
            echo '<div class="calendar-cell empty"></div>';
        }

        for ($dia = 1; $dia <= $diasNoMes; $dia++) {
            $dataIso = sprintf('%04d-%02d-%02d', $anoAtual, $mesAtual, $dia);
            $aulasDoDia = $registrosPorDia[$dataIso] ?? [];
            
            // Descobre o dia da semana (0 = Domingo, 6 = Sábado)
            $diaDaSemana = date('w', strtotime($dataIso));
            $ehFimDeSemana = ($diaDaSemana == 0 || $diaDaSemana == 6);
            $nomeFeriado = $feriadosDoMes[$dataIso] ?? null;

            $temFalta = false;
            foreach ($aulasDoDia as $aula) {
                if (strcasecmp($aula['status'], 'Ausente') === 0) {
                    $temFalta = true;
                }
            }

            // Prioridade de classes visuais: Falta (Vermelho) > Feriado (Amarelo) > Fim de semana (Cinza claro)
            $classeCelula = '';
            if ($temFalta) {
                $classeCelula = 'tem-falta';
            } elseif ($nomeFeriado) {
                $classeCelula = 'feriado';
            } elseif ($ehFimDeSemana) {
                $classeCelula = 'fim-de-semana';
            }

            // Prepara dados para o pop-up
            $infoExtra = ['ehFiltroFeriado' => $nomeFeriado, 'aulas' => $aulasDoDia];
            $dadosJson = htmlspecialchars(json_encode($infoExtra), ENT_QUOTES, 'UTF-8');
            $dataExibicao = sprintf('%02d/%02d/%04d', $dia, $mesAtual, $anoAtual);

            echo '<div class="calendar-cell ' . $classeCelula . '" onclick="abrirDetalhes(\'' . $dataExibicao . '\', \'' . $dadosJson . '\')">';
            echo '<span class="day-number">' . $dia . '</span>';

            if ($temFalta) {
                echo '<span class="day-status-indicator indicator-falta">Falta(s)</span>';
            } elseif ($nomeFeriado) {
                echo '<span class="day-status-indicator indicator-feriado">Feriado</span>';
            } elseif ($ehFimDeSemana) {
                echo '<span class="day-status-indicator indicator-fds">Fim de semana</span>';
            }

            echo '</div>';
        }
        ?>
      </div>

      <div class="legend">
        <div class="legend-item"><div class="legend-color" style="background:#fee2e2; border: 1px solid #fca5a5;"></div> Falta (Vermelho)</div>
        <div class="legend-item"><div class="legend-color" style="background:#fef3c7; border: 1px solid #fde68a;"></div> Feriado (Amarelo)</div>
        <div class="legend-item"><div class="legend-color" style="background:#f1f5f9; border: 1px solid #cbd5e1;"></div> Fim de Semana (Cinza)</div>
      </div>
    </div>
  </main>

  <!-- Modal -->
  <div id="modalDetalhes" class="modal-overlay" onclick="fecharFora(event)">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="modalDataTitulo">Detalhes do Dia</h3>
        <button class="close-btn" onclick="fecharDetalhes()">&times;</button>
      </div>
      <div id="modalCorpo"></div>
    </div>
  </div>

  <script>
    function abrirDetalhes(dataStr, dadosObj) {
      document.getElementById('modalDataTitulo').innerText = "Dia " + dataStr;
      let info = JSON.parse(dadosObj);
      let corpoHTML = "";

      if (info.ehFiltroFeriado) {
        corpoHTML += `<div style="background:#fef3c7; color:#92400e; padding:10px; border-radius:4px; margin-bottom:12px; font-weight:600; text-align:center;">🎉 ${info.ehFiltroFeriado}</div>`;
      }

      if (info.aulas.length === 0) {
        corpoHTML += "<p style='text-align:center; color:#64748b; padding:10px 0;'>Nenhum registro de chamada neste dia.</p>";
      } else {
        corpoHTML += "<div style='display:flex; flex-direction:column; gap:6px;'>";
        info.aulas.forEach(aula => {
          let ehAusente = aula.status.toLowerCase() === 'ausente';
          let classeBadge = ehAusente ? 'badge-a' : 'badge-p';
          corpoHTML += `
            <div class="modal-aula-item">
              <span><strong>${aula.disciplina}</strong></span>
              <span class="${classeBadge}">${aula.status}</span>
            </div>`;
        });
        corpoHTML += "</div>";
      }

      document.getElementById('modalCorpo').innerHTML = corpoHTML;
      document.getElementById('modalDetalhes').style.display = 'flex';
    }

    function fecharDetalhes() {
      document.getElementById('modalDetalhes').style.display = 'none';
    }

    function fecharFora(event) {
      if (event.target === document.getElementById('modalDetalhes')) {
        fecharDetalhes();
      }
    }
  </script>

  <footer>
    © Governo do Estado do Paraná - Secretaria da Educação (SEED-PR)
  </footer>

</body>
</html>