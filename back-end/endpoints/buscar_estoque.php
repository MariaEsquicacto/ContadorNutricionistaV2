<?php
header('Content-Type: application/json'); // Define o cabeçalho para retornar JSON

// Inclui o arquivo de configuração do banco de dados
include(__DIR__ . '/../config/database.php');

$response = ['success' => false, 'message' => '', 'data' => null];

if (isset($_GET['id'])) {
    $id_estoque = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    if ($id_estoque === false) {
        $response['message'] = 'ID inválido.';
        echo json_encode($response);
        exit();
    }

    $sql = "SELECT id_estoque, nome_item_estoque, tipo_movimentacao_estoque, quantidade_estoque, unidade_estoque FROM estoque WHERE id_estoque = ?";
    $stmt = $mysqli->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $id_estoque);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($linha = $resultado->fetch_assoc()) {
            $response['success'] = true;
            $response['data'] = $linha; // Retorna os dados do item
        } else {
            $response['message'] = 'Item não encontrado.';
        }
        $stmt->close();
    } else {
        $response['message'] = 'Erro ao preparar a query: ' . $mysqli->error;
    }
} else {
    $response['message'] = 'ID do item não fornecido.';
}

// Fecha a conexão com o banco de dados
if ($mysqli) {
    $mysqli->close();
}

echo json_encode($response);
