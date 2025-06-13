<?php
// Inclui o arquivo de configuração do banco de dados
// O caminho '__DIR__ . /../../back-end/config/database.php' assume que
// estoque.php está em 'front-end/pages/' e database.php em 'back-end/config/'
include(__DIR__ . '/../../back-end/config/database.php');

// Inicializa a variável $dados como um array vazio para evitar erros caso não haja resultados
$dados = [];
$erro_php = null; // Variável para armazenar mensagens de erro PHP

// Conexão com o banco
if ($mysqli->connect_error) {
    $erro_php = 'Erro na conexão com o banco de dados: ' . $mysqli->connect_error;
} else {
    // --- Lógica para buscar itens (GET) ---
    // Verifica se foi passado o parâmetro GET "alimento" do formulário
    // Usamos o operador null coalescing (?? '') para PHP 7+ para garantir que é uma string
    $nome_item_busca = isset($_GET['alimento']) ? trim($_GET['alimento']) : '';

    // Prepara a query base
    // Usando id_estoque no SELECT para corresponder à sua coluna real
    $sql = "SELECT id_estoque, nome_item_estoque, tipo_movimentacao_estoque, quantidade_estoque, unidade_estoque FROM estoque";
    $params = [];
    $types = '';

    if ($nome_item_busca !== '') { // Se houver um termo de busca
        $sql .= " WHERE nome_item_estoque LIKE ?";
        $params[] = '%' . $nome_item_busca . '%';
        $types .= 's'; // 's' para string
    }
    $sql .= " ORDER BY nome_item_estoque ASC"; // Adiciona ordenação para melhor visualização

    $stmt = $mysqli->prepare($sql);
    if ($stmt) {
        if (!empty($params)) {
            // Usa call_user_func_array para bind_param com array dinâmico (necessário para PHP < 8.1 com bind_param dinâmico)
            // Função refValues é necessária se o seu PHP for anterior ao 8.1 e você estiver usando array de parâmetros.
            call_user_func_array([$stmt, 'bind_param'], array_merge([$types], refValues($params)));
        }
        $stmt->execute();
        $resultado = $stmt->get_result();

        while ($linha = $resultado->fetch_assoc()) {
            $dados[] = $linha;
        }
        $stmt->close();
    } else {
        $erro_php = 'Erro ao preparar a query de busca: ' . $mysqli->error;
    }
}

// Função auxiliar para bind_param (necessária para passar referências em PHP < 8.1)
function refValues($arr)
{
    if (strnatcmp(phpversion(), '5.3') >= 0) // PHP 5.3+
    {
        $refs = array();
        foreach ($arr as $key => $value)
            $refs[$key] = &$arr[$key];
        return $refs;
    }
    return $arr;
}

// Fecha a conexão com o banco de dados (se ainda estiver aberta)
if ($mysqli && !$mysqli->connect_error) { // Garante que $mysqli existe e está conectado antes de tentar fechar
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css?v=1.5">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="../css/estoque.css">
    <title>Estoque</title>
    <style>
        /* (Seus estilos CSS existentes aqui. Mantenha-os como estão.) */
        /* style.css - ADIÇÕES E AJUSTES ESPECÍFICOS PARA A TELA DE ESTOQUE COM IDS */

        /* Variáveis de Cores (Verifique se já existem no seu :root e remova duplicatas) */
        :root {
            --cor-principal-gradiente: #C31818;
            /* Seu gradiente principal */
            --cor-botoes-hover: linear-gradient(to right, #A31515, #D05A20);
            /* Gradiente levemente mais escuro para hover */
            --cor-fundo-claro: #FFEEDA;
            /* Cor de fundo do seu body */
            --cor-fundo-secao: white;
            /* Cor de fundo para seções como o estoque */
            --cor-texto-claro: white;
            --cor-texto-escuro: #333;
            /* Cor para títulos e textos gerais */
            --borda-padrao: #ccc;
            /* Cor de borda padrão dos seus inputs */
            --sombra-leve: 0 4px 8px rgba(0, 0, 0, 0.1);
            --sombra-media: 6px 6px 15px rgba(0, 0, 0, 0.3);
            /* Sua sombra padrão */
            --cor-tabela-linha-par: #f8f8f8;
            /* Um cinza bem clarinho para linhas alternadas */
            --cor-tabela-hover: #D0E8F2;
            /* Sua cor de hover de tabela */
        }
        #estoque-section{
            height: 500px;
            max-width: 900px;
        }
        #estoque-section h1{
            font-size: 22px;
        }
        #estoque-section h2{
            font-size: 22px;
        }
        #fig_bottom {
            width: 800px;
            height: 250px;
            background: linear-gradient(to right, #c40000, #ff5733);
            clip-path: polygon(0% 0%, 30% 80%, 100% 100%, 0% 100%, 0% 100%);
        }
        .action-button i {
            pointer-events: none;
        }

        .edit-button {
            background-color: #85d285;
        }

        .edit-button:hover {
            /* background-color: #0056b3; */
            transform: scale(1.1);
        }

        .delete-button {
            background-color: #dc3545;
        }

        .delete-button:hover {
            background-color: #bd2130;
            transform: scale(1.1);
        }


        
    </style>
</head>

<body>
    <header>
        <div id="fig_top" role="img" aria-label="Top decoration"></div>
    </header>

    <div class="menu">
        <button class="hamburguer" aria-expanded="false" aria-controls="main-navigation-list">
            <!-- <span class="sr-only">Abrir/Fechar Menu</span> -->
            <div id="barra1" class="barra"></div>
            <div id="barra2" class="barra"></div>
            <div id="barra3" class="barra"></div>
        </button>

        <nav>
            <ul id="main-navigation-list">
                <li><a href="estoque.php">Estoque</a></li>
                <li><a href="calendario.php">Calendário</a></li>
                <li><a href="relatorio.php">Contagem</a></li>
                <li><a href="index.php">Sair</a></li>
            </ul>
        </nav>
    </div>

    <main id="main-content">
        <section id="estoque-section">
            <h1>Gerenciamento de Estoque</h1>

            <div id="estoque-top-controls">
                <form id="estoque-search-form" action="estoque.php" method="GET" role="search">
                    <div id="estoque-input-group" class="input-container">
                        <!-- <label for="estoque-alimento-input" class="sr-only">Digite o alimento para buscar</label> -->
                        <i class="bi bi-search"></i>
                        <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                        <input type="search" name="alimento" id="estoque-alimento-input"
                            placeholder="Pesquisar item..."
                            value="<?php echo htmlspecialchars($nome_item_busca); ?>">
                       <button type="submit" id="estoque-search-button">Buscar</button>
                        <button id="estoque-add-button" class="btn-cadastro">
                            <i class="fa-solid fa-plus" aria-hidden="true"></i> Novo Item
                        </button>
                    </div>
                     
                </form>
            </div>

            <?php if ($erro_php): ?>
                <p id="estoque-error-message" class="mensagem-erro" role="alert" aria-live="assertive">
                    <?php echo $erro_php; ?>
                </p>
            <?php endif; ?>

            <div id="estoque-results-container">
                <?php if (!empty($dados)): ?>
                    <h2>Itens no Estoque:</h2>
                    <table id="estoque-data-table">
                        <thead>
                            <tr>
                                <th scope="col">Nome do Item</th>
                                <th scope="col">Tipo de Movimentação</th>
                                <th scope="col">Quantidade</th>
                                <th scope="col">Unidade</th>
                                <th scope="col">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dados as $item): ?>
                                <tr id="item-<?php echo htmlspecialchars($item['id_estoque']); ?>">
                                    <td><?php echo htmlspecialchars($item['nome_item_estoque']); ?></td>
                                    <td><?php echo htmlspecialchars($item['tipo_movimentacao_estoque']); ?></td>
                                    <td><?php echo htmlspecialchars($item['quantidade_estoque']); ?></td>
                                    <td><?php echo htmlspecialchars($item['unidade_estoque']); ?></td>
                                    <td>
                                        <button class="action-button edit-button" data-id="<?php echo htmlspecialchars($item['id_estoque']); ?>" title="Editar Item">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="action-button delete-button" data-id="<?php echo htmlspecialchars($item['id_estoque']); ?>" title="Excluir Item">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php elseif ($nome_item_busca !== ''): ?>
                    <p id="estoque-no-results-message" class="mensagem-info">Nenhum item encontrado com o nome "<?php echo htmlspecialchars($nome_item_busca); ?>".</p>
                <?php else: ?>
                    <p id="estoque-initial-message" class="mensagem-info">Não há itens no estoque. Comece buscando ou adicionando um item.</p>
                <?php endif; ?>
            </div>

        </section>
    </main>

    <footer>
        <div id="fig_bottom" role="img" aria-label="Bottom decoration"></div>
        <img src="../assets/losangos_bottom.png" alt="Padrão decorativo de losangos" id="losangos">
    </footer>

    <div id="delete-modal-overlay" class="modal-overlay" aria-hidden="true" role="dialog" aria-labelledby="modal-title" aria-describedby="modal-description">
        <div class="modal-content">
            <h3 id="modal-title">Confirmar Exclusão</h3>
            <p id="modal-description">Tem certeza de que deseja excluir este item do estoque?</p>
            <div class="modal-buttons">
                <button id="confirm-delete-button" class="modal-button confirm">Excluir</button>
                <button id="cancel-delete-button" class="modal-button cancel">Cancelar</button>
            </div>
        </div>
    </div>
    <div id="cadastro-modal-overlay" class="modal-overlay" aria-hidden="true" role="dialog" aria-labelledby="cadastro-modal-title">
        <div class="modal-content">
            <h3 id="cadastro-modal-title">Cadastrar Novo Item</h3>
            <form id="cadastro-item-form">
                <div class="form-group">
                    <label for="cadastro-nome">Nome do Item:</label>
                    <input type="text" id="cadastro-nome" name="nome_item_estoque" required>
                </div>
                <div class="form-group">
                    <label for="cadastro-tipo">Tipo de Movimentação:</label>
                    <select id="cadastro-tipo" name="tipo_movimentacao_estoque" required>
                        <option value="">Selecione</option>
                        <option value="entrada">Entrada</option>
                        <option value="saida">Saída</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="cadastro-quantidade">Quantidade:</label>
                    <input type="number" id="cadastro-quantidade" name="quantidade_estoque" min="0" required>
                </div>
                <div class="form-group">
                    <label for="cadastro-unidade">Unidade:</label>
                    <input type="text" id="cadastro-unidade" name="unidade_estoque" placeholder="Ex: kg, un, litros" required>
                </div>
                <div class="modal-buttons">
                    <button type="submit" id="cadastro-submit-button" class="modal-button confirm">Cadastrar</button>
                    <button type="button" id="cancel-cadastro-button" class="modal-button cancel">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
    <div id="edicao-modal-overlay" class="modal-overlay" aria-hidden="true" role="dialog" aria-labelledby="edicao-modal-title">
        <div class="modal-content">
            <h3 id="edicao-modal-title">Editar Item</h3>
            <form id="edicao-item-form">
                <input type="hidden" id="edicao-id" name="id_estoque">
                <div class="form-group">
                    <label for="edicao-nome">Nome do Item:</label>
                    <input type="text" id="edicao-nome" name="nome_item_estoque" required>
                </div>
                <div class="form-group">
                    <label for="edicao-tipo">Tipo de Movimentação:</label>
                    <select id="edicao-tipo" name="tipo_movimentacao_estoque" required>
                        <option value="">Selecione</option>
                        <option value="entrada">Entrada</option>
                        <option value="saida">Saída</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edicao-quantidade">Quantidade:</label>
                    <input type="number" id="edicao-quantidade" name="quantidade_estoque" min="0" required>
                </div>
                <div class="form-group">
                    <label for="edicao-unidade">Unidade:</label>
                    <input type="text" id="edicao-unidade" name="unidade_estoque" placeholder="Ex: kg, un, litros" required>
                </div>
                <div class="modal-buttons">
                    <button type="submit" class="modal-button confirm">Salvar </button>
                    <button type="button" id="cancel-edicao-button" class="modal-button cancel">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        const abrir_menu = document.getElementsByClassName('hamburguer')[0];
        const menu = document.getElementsByClassName('menu')[0];
        abrir_menu.addEventListener('click', () => {
            abrir_menu.classList.toggle('aberto');
            menu.classList.toggle('ativo');
        });

        // --- Lógica do Modal de Confirmação de Exclusão ---
        const deleteModalOverlay = document.getElementById('delete-modal-overlay');
        const confirmDeleteButton = document.getElementById('confirm-delete-button');
        const cancelDeleteButton = document.getElementById('cancel-delete-button');
        let itemIdToDelete = null;

        function showDeleteModal(itemId) {
            itemIdToDelete = itemId;
            deleteModalOverlay.classList.add('active');
            deleteModalOverlay.setAttribute('aria-hidden', 'false');
            cancelDeleteButton.focus();
        }

        function hideDeleteModal() {
            deleteModalOverlay.classList.remove('active');
            deleteModalOverlay.setAttribute('aria-hidden', 'true');
            itemIdToDelete = null;
        }

        // Delegar evento para botões de exclusão (importante para elementos adicionados dinamicamente)
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('delete-button') || event.target.closest('.delete-button')) {
                const button = event.target.closest('.delete-button');
                const itemId = button.dataset.id;
                showDeleteModal(itemId);
            }
        });

        cancelDeleteButton.addEventListener('click', hideDeleteModal);
        deleteModalOverlay.addEventListener('click', function(event) {
            if (event.target === deleteModalOverlay) {
                hideDeleteModal();
            }
        });

        confirmDeleteButton.addEventListener('click', async () => {
            if (itemIdToDelete) {
                try {
                    const formData = new FormData();
                    formData.append('id', itemIdToDelete);

                    // ATENÇÃO: CAMINHO CORRIGIDO PARA excluir_estoque.php
                    const response = await fetch('../../back-end/endpoints/excluir_estoque.php', {
                        method: 'POST',
                        body: formData
                    });

                    if (!response.ok) {
                        throw new Error(`Erro HTTP! Status: ${response.status}`);
                    }

                    const result = await response.json();

                    if (result.success) {
                        alert(result.message);
                        const rowToRemove = document.getElementById(`item-${itemIdToDelete}`);
                        if (rowToRemove) {
                            rowToRemove.remove();
                        }
                        updateTableEmptyState(); // Atualiza a mensagem se a tabela ficar vazia
                    } else {
                        alert('Erro ao excluir item: ' + result.message);
                    }
                } catch (error) {
                    console.error('Erro ao fazer a requisição de exclusão:', error);
                    alert('Ocorreu um erro ao tentar excluir o item. Por favor, tente novamente.');
                } finally {
                    hideDeleteModal();
                }
            }
        });

        // --- Lógica para o Modal de Cadastro ---
        const cadastroModalOverlay = document.getElementById('cadastro-modal-overlay');
        const estoqueAddButton = document.getElementById('estoque-add-button'); // O botão "Novo Item"
        const cadastroItemForm = document.getElementById('cadastro-item-form');
        const cancelCadastroButton = document.getElementById('cancel-cadastro-button');

        function showCadastroModal() {
            cadastroModalOverlay.classList.add('active');
            cadastroModalOverlay.setAttribute('aria-hidden', 'false');
            document.getElementById('cadastro-nome').focus();
        }

        function hideCadastroModal() {
            cadastroModalOverlay.classList.remove('active');
            cadastroModalOverlay.setAttribute('aria-hidden', 'true');
            cadastroItemForm.reset(); // Limpa o formulário ao fechar
        }

        estoqueAddButton.addEventListener('click', showCadastroModal);
        cancelCadastroButton.addEventListener('click', hideCadastroModal);
        cadastroModalOverlay.addEventListener('click', function(event) {
            if (event.target === cadastroModalOverlay) {
                hideCadastroModal();
            }
        });

        cadastroItemForm.addEventListener('submit', async function(event) {
            event.preventDefault();

            const formData = new FormData(this);

            try {
                // ATENÇÃO: CAMINHO CORRIGIDO PARA cadastrar_estoque.php
                const response = await fetch('../../back-end/endpoints/cadastrar_estoque.php', { // MUDADO DE /api/ PARA /endpoints/
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    throw new Error(`Erro HTTP! Status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success) {
                    alert(result.message);
                    hideCadastroModal();
                    reloadTableData(); // Recarrega a tabela para mostrar o novo item
                } else {
                    alert('Erro ao cadastrar item: ' + result.message);
                }
            } catch (error) {
                console.error('Erro ao cadastrar item:', error);
                alert('Ocorreu um erro ao tentar cadastrar o item. Por favor, tente novamente.');
            }
        });

        // --- Lógica para o Modal de Edição ---
        const edicaoModalOverlay = document.getElementById('edicao-modal-overlay');
        const edicaoItemForm = document.getElementById('edicao-item-form');
        const cancelEdicaoButton = document.getElementById('cancel-edicao-button');
        const edicaoIdInput = document.getElementById('edicao-id');
        const edicaoNomeInput = document.getElementById('edicao-nome');
        const edicaoTipoSelect = document.getElementById('edicao-tipo');
        const edicaoQuantidadeInput = document.getElementById('edicao-quantidade');
        const edicaoUnidadeInput = document.getElementById('edicao-unidade');

        function showEdicaoModal(itemData) {
            // Preenche o formulário do modal com os dados do item
            edicaoIdInput.value = itemData.id_estoque;
            edicaoNomeInput.value = itemData.nome_item_estoque;
            edicaoTipoSelect.value = itemData.tipo_movimentacao_estoque;
            edicaoQuantidadeInput.value = itemData.quantidade_estoque;
            edicaoUnidadeInput.value = itemData.unidade_estoque;

            edicaoModalOverlay.classList.add('active');
            edicaoModalOverlay.setAttribute('aria-hidden', 'false');
            edicaoNomeInput.focus();
        }

        function hideEdicaoModal() {
            edicaoModalOverlay.classList.remove('active');
            edicaoModalOverlay.setAttribute('aria-hidden', 'true');
            edicaoItemForm.reset();
        }

        // Delegar evento para botões de edição (importante para elementos adicionados dinamicamente)
        document.addEventListener('click', async function(event) {
            if (event.target.classList.contains('edit-button') || event.target.closest('.edit-button')) {
                const button = event.target.closest('.edit-button');
                const itemId = button.dataset.id;

                try {
                    // *** CAMINHO CORRIGIDO: CHAMANDO atualizar_estoque.php VIA GET PARA BUSCAR ***
                    const response = await fetch(`../../back-end/endpoints/atualizar_estoque.php?id=${itemId}`);
                    if (!response.ok) {
                        throw new Error(`Erro HTTP! Status: ${response.status}`);
                    }
                    const itemData = await response.json();

                    if (itemData.success) {
                        showEdicaoModal(itemData.data);
                    } else {
                        alert('Erro ao buscar dados do item: ' + itemData.message);
                    }
                } catch (error) {
                    console.error('Erro ao buscar item para edição:', error);
                    alert('Ocorreu um erro ao buscar os dados do item para edição. Por favor, verifique o console para mais detalhes.');
                }
            }
        });

        cancelEdicaoButton.addEventListener('click', hideEdicaoModal);
        edicaoModalOverlay.addEventListener('click', function(event) {
            if (event.target === edicaoModalOverlay) {
                hideEdicaoModal();
            }
        });

        edicaoItemForm.addEventListener('submit', async function(event) {
            event.preventDefault();

            const formData = new FormData(this);

            try {
                // CAMINHO CORRIGIDO PARA atualizar_estoque.php (já estava correto para POST)
                const response = await fetch('../../back-end/endpoints/atualizar_estoque.php', { // Mantém endpoints/
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    throw new Error(`Erro HTTP! Status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success) {
                    alert(result.message);
                    hideEdicaoModal();
                    reloadTableData(); // Recarrega a tabela para mostrar as alterações
                } else {
                    alert('Erro ao atualizar item: ' + result.message);
                }
            } catch (error) {
                console.error('Erro ao atualizar item:', error);
                alert('Ocorreu um erro ao tentar atualizar o item. Por favor, tente novamente.');
            }
        });

        // --- Função para recarregar os dados da tabela via AJAX ---
        async function reloadTableData() {
            const searchInput = document.getElementById('estoque-alimento-input');
            const searchTerm = searchInput ? searchInput.value : '';

            try {
                // Adiciona um parâmetro 'ajax=1' para que o PHP saiba que é uma requisição AJAX
                const response = await fetch(`estoque.php?alimento=${encodeURIComponent(searchTerm)}&ajax=1`);
                if (!response.ok) {
                    throw new Error(`Erro HTTP! Status: ${response.status}`);
                }
                const html = await response.text();

                // Criar um elemento temporário para parsear o HTML
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;

                // Encontrar o container da tabela e substituir seu conteúdo
                const newTableContainer = tempDiv.querySelector('#estoque-results-container');
                const currentTableContainer = document.getElementById('estoque-results-container');

                if (newTableContainer && currentTableContainer) {
                    currentTableContainer.innerHTML = newTableContainer.innerHTML;
                    // Os event listeners para os botões delete e edit agora são delegados,
                    // então não precisam ser re-adicionados aqui após a substituição do HTML.
                } else {
                    console.error('Não foi possível encontrar o container da tabela para atualização.');
                }
            } catch (error) {
                console.error('Erro ao recarregar dados da tabela:', error);
                alert('Ocorreu um erro ao recarregar a tabela de estoque.');
            }
        }

        // Função para atualizar o estado da tabela (se está vazia ou não)
        function updateTableEmptyState() {
            const tableBody = document.getElementById('estoque-data-table') ? document.getElementById('estoque-data-table').querySelector('tbody') : null;
            const resultsContainer = document.getElementById('estoque-results-container');

            if (tableBody && tableBody.children.length === 0) {
                resultsContainer.innerHTML = '<p id="estoque-initial-message" class="mensagem-info">Não há itens no estoque. Comece buscando ou adicionando um item.</p>';
            } else if (!tableBody) {
                // Se a tabela nem existir, significa que já está na mensagem de "não há itens"
            }
        }
        // Chamar ao carregar a página para garantir que a mensagem inicial esteja correta
        updateTableEmptyState();

        // Opcional: Ajuste para que a busca também recarregue a tabela via AJAX
        document.getElementById('estoque-search-form').addEventListener('submit', async function(event) {
            event.preventDefault(); // Impede o envio padrão que recarrega a página
            await reloadTableData(); // Recarrega a tabela usando a função AJAX
        });
    </script>
</body>

</html>