<?php
include(__DIR__ . '/../config/database.php');
require_once __DIR__ . '/../../vendor/autoload.php';
// carregando composer autoload para JWT
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header('Content-Type: application/json');
date_default_timezone_set('America/Sao_Paulo');

// Segredos para assinar tokens
$accessTokenSecret = 'seu_segredo_do_access_token';
$refreshTokenSecret = 'seu_segredo_de_refresh_aqui';

$dados = json_decode(file_get_contents('php://input'), true);

if (isset($dados['nome'], $dados['senha'])) {
    $nome = trim($dados['nome']);
    $senha = $dados['senha'];

    $stmt = $mysqli->prepare("SELECT * FROM usuarios WHERE nome_usuario = ?");
    $stmt->bind_param("s", $nome);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado && $resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        if (password_verify($senha, $usuario['senha_usuario'])) {
            $usuarioId = $usuario['id_usuario'];

            $issuedAt = time();
            $accessExp = $issuedAt + 3600; // 1 hora
            $refreshExp = $issuedAt + (7 * 24 * 60 * 60); // 7 dias

            // Payload do Access Token
            $accessTokenPayload = [
                'iat' => $issuedAt,
                'exp' => $accessExp,
                'id_usuario' => $usuarioId,
                'nome' => $usuario['nome_usuario'],
                'nivel' => $usuario['nivel_usuario']
            ];


            // Payload do Refresh Token
            $refreshTokenPayload = [
                'iat' => $issuedAt,
                'exp' => $accessExp,
                'id_usuario' => $usuarioId,
                'nome' => $usuario['nome_usuario'],
                'nivel' => $usuario['nivel_usuario']
            ];

            $accessToken = JWT::encode($accessTokenPayload, $accessTokenSecret, 'HS256');
            $refreshToken = JWT::encode($refreshTokenPayload, $refreshTokenSecret, 'HS256');

            // Salva o refresh token no banco
            $expira_em = date('Y-m-d H:i:s', $refreshExp);
            $stmtRefresh = $mysqli->prepare(
                "INSERT INTO refresh_tokens (id_usuario, token, expiracao) VALUES (?, ?, ?)"
            );
            $stmtRefresh->bind_param("iss", $usuarioId, $refreshToken, $expira_em);
            $stmtRefresh->execute();
            $stmtRefresh->close();

            echo json_encode([
                'mensagem' => 'Login realizado com sucesso!',
                'accessToken' => $accessToken,
                'refreshToken' => $refreshToken,
                'expira_em' => date('Y-m-d H:i:s', $accessExp)
            ]);
        } else {
            echo json_encode(['erro' => 'Senha incorreta']);
        }
    } else {
        echo json_encode(['erro' => 'Usuário não encontrado']);
    }

    $stmt->close();
} else {
    echo json_encode(['erro' => 'Dados incompletos']);
}
