<?php
header('Content-Type: application/json'); // Define o cabeçalho para retornar JSON

// Inclui o arquivo de configuração do banco de dados
include(__DIR__ . '/../config/database.php');

$response = ['success' => false, 'message' => '', 'data' => null]; // Inclui 'data' para a resposta GET

// Lógica principal: Verifica o método da requisição
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // --- Lógica para BUSCAR um item (para preencher o formulário de edição) ---

    $id_estoque = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT);

    if ($id_estoque === false || $id_estoque <= 0) {
        $response['message'] = 'ID do item inválido ou não fornecido para busca.';
        echo json_encode($response);
        exit();
    }

    // Prepara a query para buscar o item
    $sql = "SELECT id_estoque, nome_item_estoque, tipo_movimentacao_estoque, quantidade_estoque, unidade_estoque FROM estoque WHERE id_estoque = ?";
    $stmt = $mysqli->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $id_estoque);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $item = $result->fetch_assoc();
            $response['success'] = true;
            $response['message'] = 'Item encontrado com sucesso.';
            $response['data'] = $item; // Retorna os dados do item aqui
        } else {
            $response['message'] = 'Item não encontrado no estoque.';
        }
        $stmt->close();
    } else {
        $response['message'] = 'Erro ao preparar a query de busca: ' . $mysqli->error;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- Lógica para ATUALIZAR um item (após a submissão do formulário de edição) ---

    $id_estoque = filter_var($_POST['id_estoque'] ?? '', FILTER_VALIDATE_INT);
    $nome_item_estoque = trim($_POST['nome_item_estoque'] ?? '');
    $tipo_movimentacao_estoque = trim($_POST['tipo_movimentacao_estoque'] ?? '');
    $quantidade_estoque = filter_var($_POST['quantidade_estoque'] ?? '', FILTER_VALIDATE_INT);
    $unidade_estoque = trim($_POST['unidade_estoque'] ?? '');

    // Verifica se os campos obrigatórios e o ID estão preenchidos e válidos
    if ($id_estoque === false || $id_estoque <= 0 || empty($nome_item_estoque) || empty($tipo_movimentacao_estoque) || $quantidade_estoque === false || $quantidade_estoque < 0 || empty($unidade_estoque)) {
        $response['message'] = 'Todos os campos obrigatórios e o ID devem ser preenchidos corretamente para atualização.';
        echo json_encode($response);
        exit();
    }

    // Prepara a query de atualização
    $sql = "UPDATE estoque SET nome_item_estoque = ?, tipo_movimentacao_estoque = ?, quantidade_estoque = ?, unidade_estoque = ? WHERE id_estoque = ?";
    $stmt = $mysqli->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssisi", $nome_item_estoque, $tipo_movimentacao_estoque, $quantidade_estoque, $unidade_estoque, $id_estoque);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $response['success'] = true;
                $response['message'] = 'Item atualizado com sucesso!';
            } else {
                $response['message'] = 'Nenhuma alteração foi feita ou item não encontrado.';
                $response['success'] = true; // Considerar sucesso mesmo se não houver linhas afetadas
            }
        } else {
            $response['message'] = 'Erro ao executar a atualização: ' . $stmt->error;
        }
        $stmt->close();
    } else {
        $response['message'] = 'Erro ao preparar a query de atualização: ' . $mysqli->error;
    }
} else {
    // Se não for GET nem POST, método inválido
    $response['message'] = 'Método de requisição inválido. Esperado GET ou POST.';
}

// Fecha a conexão com o banco de dados
if ($mysqli) {
    $mysqli->close();
}

echo json_encode($response);
