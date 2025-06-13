<?php
header('Content-Type: application/json');

// Inclui o arquivo de configuração do banco de dados
include(__DIR__ . '/../config/database.php');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // AQUI: Os dados são recebidos via $_POST (FormData do JavaScript)
    $id_item = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);

    if ($id_item === false || $id_item <= 0) {
        $response['message'] = 'ID do item inválido.';
        echo json_encode($response);
        exit();
    }

    // Prepara a query para excluir
    // ALTERADO: Usando id_estoque para exclusão
    $stmt = $mysqli->prepare("DELETE FROM estoque WHERE id_estoque = ?");

    if ($stmt) {
        $stmt->bind_param("i", $id_item); // 'i' para inteiro

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $response['success'] = true;
                $response['message'] = 'Item excluído com sucesso!';
            } else {
                $response['message'] = 'Item não encontrado ou já excluído.';
            }
        } else {
            $response['message'] = 'Erro ao executar a exclusão: ' . $stmt->error;
        }
        $stmt->close();
    } else {
        $response['message'] = 'Erro ao preparar a query de exclusão: ' . $mysqli->error;
    }
} else {
    $response['message'] = 'Método de requisição inválido.';
}

// Fecha a conexão com o banco de dados
if ($mysqli) {
    $mysqli->close();
}

echo json_encode($response);
