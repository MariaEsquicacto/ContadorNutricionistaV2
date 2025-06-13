<?php
// get_access_token.php
// Este endpoint é chamado por OUTROS scripts PHP para obter um access token válido
// usando um refresh token da sessão PHP.

include(__DIR__ . '/../config/database.php'); // Sua conexão com o banco
require_once __DIR__ . '/../../vendor/autoload.php'; // JWT Library

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Ativar exibição de erros para depuração (REMOVER EM PRODUÇÃO!)
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/get_access_token_debug.log');

session_start(); // Precisa da sessão para pegar o refresh token

header('Content-Type: application/json');

$accessTokenSecret = 'seu_segredo_do_access_token';
$refreshTokenSecret = 'seu_segredo_de_refresh_aqui';
$accessTokenExpiration = time() + 3600; // 1 hora de validade para o novo access token

// --- 1. Pega o refresh token da sessão PHP ---
$refresh_token_session = $_SESSION['refresh_token'] ?? null;

if (empty($refresh_token_session)) {
    error_log("DEBUG GET_ACCESS: Refresh token não encontrado na sessão PHP.");
    http_response_code(401); // Unauthorized
    echo json_encode(['erro' => 'Sessão inválida. Refresh token ausente.']);
    exit;
}

error_log("DEBUG GET_ACCESS: Refresh token encontrado na sessão: " . $refresh_token_session);

try {
    // --- 2. Decodifica e valida o refresh token ---
    $decoded_refresh_token = JWT::decode($refresh_token_session, new Key($refreshTokenSecret, 'HS256'));
    $id_usuario = $decoded_refresh_token->id_usuario ?? null;

    if (empty($id_usuario)) {
        throw new Exception("ID de usuário ausente no payload do refresh token.");
    }

    // --- 3. Verifica o refresh token no banco de dados (crucial para segurança) ---
    $stmt = $mysqli->prepare("SELECT token, expiracao FROM refresh_tokens WHERE id_usuario = ? AND token = ? AND expiracao > NOW()");
    if (!$stmt) {
        throw new Exception("Erro ao preparar query para verificar refresh token no DB: " . $mysqli->error);
    }
    $stmt->bind_param("is", $id_usuario, $refresh_token_session);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
        throw new Exception("Refresh token não encontrado, expirado ou inválido no banco de dados.");
    }
    $stmt->close();
    error_log("DEBUG GET_ACCESS: Refresh token verificado no DB com sucesso para id_usuario: " . $id_usuario);

    // --- 4. Gera um novo access token ---
    $newAccessTokenPayload = [
        'iat' => time(),
        'exp' => $accessTokenExpiration,
        'id_usuario' => $id_usuario,
        'nome' => $decoded_refresh_token->nome ?? '', // Reusa info do refresh token payload
        'nivel' => $decoded_refresh_token->nivel ?? ''
    ];
    $newAccessToken = JWT::encode($newAccessTokenPayload, $accessTokenSecret, 'HS256');

    error_log("DEBUG GET_ACCESS: Novo access token gerado para id_usuario: " . $id_usuario);

    echo json_encode([
        'success' => true,
        'accessToken' => $newAccessToken,
        'mensagem' => 'Access token renovado com sucesso.'
    ]);
} catch (Exception $e) {
    error_log("DEBUG GET_ACCESS: Erro ao obter/renovar access token: " . $e->getMessage());
    // Invalida o refresh token na sessão para forçar novo login se houver erro grave
    unset($_SESSION['refresh_token']);
    unset($_SESSION['access_token']);
    http_response_code(401); // Unauthorized
    echo json_encode(['erro' => 'Falha na autenticação: ' . $e->getMessage()]);
} finally {
    if (isset($mysqli) && $mysqli instanceof mysqli && !$mysqli->connect_error) {
        $mysqli->close();
    }
}
