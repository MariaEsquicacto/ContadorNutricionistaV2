<?php
header('Content-Type: application/json'); // Define o cabeçalho para retornar JSON

// Inclui o arquivo de configuração do banco de dados
include(__DIR__ . '/../config/database.php');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // AQUI: Os dados são recebidos via $_POST (FormData do JavaScript)
    $nome_item_estoque = trim($_POST['nome_item_estoque'] ?? '');
    $tipo_movimentacao_estoque = trim($_POST['tipo_movimentacao_estoque'] ?? '');
    $quantidade_estoque = filter_var($_POST['quantidade_estoque'] ?? '', FILTER_VALIDATE_INT);
    $unidade_estoque = trim($_POST['unidade_estoque'] ?? '');

    // Verifica se os campos obrigatórios estão preenchidos e válidos
    if (empty($nome_item_estoque) || empty($tipo_movimentacao_estoque) || $quantidade_estoque === false || $quantidade_estoque < 0 || empty($unidade_estoque)) {
        $response['message'] = 'Todos os campos obrigatórios devem ser preenchidos corretamente.';
        echo json_encode($response);
        exit();
    }

    // Prepara a query de inserção
    $sql = "INSERT INTO estoque (nome_item_estoque, tipo_movimentacao_estoque, quantidade_estoque, unidade_estoque) VALUES (?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssis", $nome_item_estoque, $tipo_movimentacao_estoque, $quantidade_estoque, $unidade_estoque);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Item cadastrado com sucesso!';
            // Opcional: retornar o ID do novo item (se precisar para algo no front-end)
            // $response['id_novo_item'] = $mysqli->insert_id;
        } else {
            $response['message'] = 'Erro ao executar a inserção: ' . $stmt->error;
        }
        $stmt->close();
    } else {
        $response['message'] = 'Erro ao preparar a query: ' . $mysqli->error;
    }
} else {
    $response['message'] = 'Método de requisição inválido.';
}

// Fecha a conexão com o banco de dados
if ($mysqli) { // Verifica se $mysqli foi inicializado
    $mysqli->close();
}

echo json_encode($response);
