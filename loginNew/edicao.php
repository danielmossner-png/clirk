<?php
// Ativa a exibição de todos os erros para não ficar em branco
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "admin";
$dbname = "dados";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verifica se recebeu o código
if (isset($_GET['codigo']) && !empty($_GET['codigo'])) {
    $codigo = intval($_GET['codigo']);

    // Comando SQL para excluir
    $sql_delete = "DELETE FROM dados WHERE codigo = ?";
    $stmt = $conn->prepare($sql_delete);

    if ($stmt) {
        $stmt->bind_param("i", $codigo);
        
        if ($stmt->execute()) {
            echo "<script>
                    alert('Registro excluído com sucesso!');
                    window.location.href = 'consultaDSC.php';
                  </script>";
            exit;
        } else {
            echo "Erro ao excluir no banco: " . htmlspecialchars($conn->error);
        }
        $stmt->close();
    } else {
        echo "Erro na preparação da consulta: " . htmlspecialchars($conn->error);
    }
} else {
    echo "<script>
            alert('Nenhum código foi informado para excluir.');
            window.location.href = 'edicao.php';
          </script>";
    exit;
}

$conn->close();
?>