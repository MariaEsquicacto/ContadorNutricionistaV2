<?php
include(__DIR__ . '/../config/database.php');
require_once __DIR__ . '/../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header('Content-Type: application/json');

// Chave secreta do access token
$accessTokenSecret = 'seu_segredo_do_access_token';

// Recebe os dados JSON do body
$dados = json_decode(file_get_contents('php://input'), true);

// Pega o token do header Authorization
$headers = getallheaders();

if (!isset($headers['Authorization']) || empty($headers['Authorization'])) {
    echo json_encode(['erro' => 'Token não enviado']);
    exit;
}

$token = trim($headers['Authorization']);

// Decodifica o token JWT para pegar o id do usuário
try {
    $decoded = JWT::decode($token, new Key($accessTokenSecret, 'HS256'));
    $id_usuario = $decoded->id_usuario;
} catch (Exception $e) {
    echo json_encode(['erro' => 'Token inválido ou expirado']);
    exit;
}

// Valida se enviou as contagens das turmas
if (!isset($dados['contagens_turmas']) || !is_array($dados['contagens_turmas']) || count($dados['contagens_turmas']) == 0) {
    echo json_encode(['erro' => 'Campo contagens_turmas é obrigatório e deve ser um array']);
    exit;
}

// 1. Insere uma nova linha na tabela contagem com quant_contagem = 0 temporariamente
$stmt = $mysqli->prepare(
    "INSERT INTO contagem (quant_contagem, criacao_contagem, update_contagem, usuarios_id_usuario)
     VALUES (0, NOW(), NOW(), ?)"
);
if (!$stmt) {
    echo json_encode(['erro' => 'Erro ao preparar query de inserção na contagem']);
    exit;
}
$stmt->bind_param("i", $id_usuario);
$executado = $stmt->execute();

if (!$executado) {
    echo json_encode(['erro' => 'Erro ao inserir registro na tabela contagem']);
    exit;
}

$id_contagem = $mysqli->insert_id;
$stmt->close();

// 2. Insere as contagens nas turmas vinculadas a esse id_contagem
$total = 0;
foreach ($dados['contagens_turmas'] as $ct) {
    $turma_id = intval($ct['turma_id']);
    $quantidade = intval($ct['quantidade']);

    $stmtTurma = $mysqli->prepare(
        "INSERT INTO contagens_turmas (turmas_id_turma, contagem_id_contagem, quantidade_turma)
         VALUES (?, ?, ?)"
    );
    if (!$stmtTurma) {
        echo json_encode(['erro' => 'Erro ao preparar query para inserir contagem por turma']);
        exit;
    }
    $stmtTurma->bind_param("iii", $turma_id, $id_contagem, $quantidade);
    $stmtTurma->execute();
    $stmtTurma->close();

    $total += $quantidade;
}

// 3. Atualiza a tabela contagem com o total somado
$stmtAtualiza = $mysqli->prepare(
    "UPDATE contagem SET quant_contagem = ?, update_contagem = NOW() WHERE id_contagem = ?"
);
if (!$stmtAtualiza) {
    echo json_encode(['erro' => 'Erro ao preparar query para atualizar o total']);
    exit;
}
$stmtAtualiza->bind_param("ii", $total, $id_contagem);
$stmtAtualiza->execute();
$stmtAtualiza->close();

echo json_encode([
    'mensagem' => 'Contagem inserida com sucesso!',
    'id_contagem' => $id_contagem,
    'total_contagem' => $total
]);
