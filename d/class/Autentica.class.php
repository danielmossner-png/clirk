<?php
class Autentica {
    public $email;
    public $pass;

    public function Validar_Usuario() {
        // Tenta incluir a conexao.php que está na pasta anterior
        $caminhoConexao = __DIR__ . '/../conexao.php';
        if (file_exists($caminhoConexao)) {
            require_once($caminhoConexao);
        }

        global $conn;

        // Se a conexão não existir ou falhar, retorna falso
        if (!$conn) {
            return false;
        }

        $email_digitado = strtolower(trim($this->email));
        $senha_digitada = trim($this->pass);

        // Procura o utilizador na base de dados
        $sql = "SELECT * FROM dados WHERE LOWER(TRIM(email)) = ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $email_digitado);
            mysqli_stmt_execute($stmt);
            $resultado = mysqli_stmt_get_result($stmt);

            if ($usuario = mysqli_fetch_assoc($resultado)) {
                $senha_banco = trim($usuario['pass']);

                // 1. Comparação direta (para senhas salvas como texto puro como "sadas123")
                if ($senha_digitada === $senha_banco) {
                    $this->iniciarSessao($usuario);
                    mysqli_stmt_close($stmt);
                    return true;
                }

                // 2. Comparação com hash (para senhas criptografadas)
                if (password_verify($senha_digitada, $senha_banco)) {
                    $this->iniciarSessao($usuario);
                    mysqli_stmt_close($stmt);
                    return true;
                }
            }
            mysqli_stmt_close($stmt);
        }

        return false;
    }

    private function iniciarSessao($usuario) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['usuario_id']    = $usuario['codigo'] ?? $usuario['id'] ?? null;
        $_SESSION['usuario_nome']  = $usuario['nome'];
        $_SESSION['usuario_email'] = $usuario['email'];
    }
}
?>