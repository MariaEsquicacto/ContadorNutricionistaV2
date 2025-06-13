<?php
session_start(); // Inicia a sessão para acesso a tokens, se necessário

// Mapeamento das categorias para os anos/séries com IDs de turma (PHP)
// ESTES SÃO EXEMPLOS. SUBSTITUA PELOS IDs REAIS DO SEU BANCO DE DADOS!
// Cada 'id' AQUI DEVE SER O ID REAL DA TURMA NO SEU BANCO DE DADOS.
$categoriasSalasPHP = [
    "Contagem Ensino Fundamental 1A" => [
        "titulo" => "Fundamental 1A",
        "anos" => [
            ["nome" => "1º Ano", "id" => 1], // **Mude para o ID REAL da turma "1º Ano" no seu DB**
            ["nome" => "2º Ano", "id" => 2]  // **Mude para o ID REAL da turma "2º Ano" no seu DB**
        ]
    ],
    "Contagem Ensino Fundamental 1B" => [
        "titulo" => "Fundamental 1B",
        "anos" => [
            ["nome" => "3º Ano", "id" => 3], // **Mude para o ID REAL da turma "3º Ano" no seu DB**
            ["nome" => "4º Ano", "id" => 4], // **Mude para o ID REAL da turma "4º Ano" no seu DB**
            ["nome" => "5º Ano", "id" => 5]  // **Mude para o ID REAL da turma "5º Ano" no seu DB**
        ]
    ],
    "Contagem Ensino Fundamental 2" => [
        "titulo" => "Fundamental 2",
        "anos" => [
            ["nome" => "6º Ano", "id" => 6], // **Mude para o ID REAL da turma "6º Ano" no seu DB**
            ["nome" => "7º Ano", "id" => 7],
            ["nome" => "8º Ano", "id" => 8],
            ["nome" => "9º Ano", "id" => 9]
        ]
    ],
    "Ensino Médio" => [
        "titulo" => "Ensino Médio",
        "anos" => [
            ["nome" => "1º Ano", "id" => 10], // **Mude para o ID REAL da turma "1º Ano" no seu DB**
            ["nome" => "2º Ano", "id" => 11],
            ["nome" => "3º Ano", "id" => 12]
        ]
    ]
];

// Pega a categoria e a data dos parâmetros GET
$categoriaSelecionada = $_GET['categoria'] ?? '';
$dataSelecionada = $_GET['data'] ?? ''; // Formato esperado: YYYY-MM-DD

$dadosCategoria = $categoriasSalasPHP[$categoriaSelecionada] ?? null;

// Redireciona se não houver categoria válida ou data
if (empty($categoriaSelecionada) || empty($dataSelecionada) || $dadosCategoria === null) {
    header("Location: contagem.php"); // Volta para a página de seleção de categoria
    exit;
}

$tituloPagina = htmlspecialchars($dadosCategoria['titulo']);
$anosDaCategoria = $dadosCategoria['anos'];
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Seleção de Salas - <?php echo $tituloPagina; ?></title>
</head>

<body>
    <header>
        <div id="fig_top"></div>
    </header>

    <div class="menu">
        <button class="hamburguer">
            <div id="barra1" class="barra"></div>
            <div id="barra2" class="barra"></div>
            <div id="barra3" class="barra"></div>
        </button>

        <nav>
            <ul>
                <li><a href="estoque.php">Estoque</a></li>
                <li><a href="calendario.php">Calendário</a></li>
                <li><a href="relatorio.php">Contagem</a></li>
                <li><a href="#" id="logout-btn">Sair</a></li>
            </ul>
        </nav>
    </div>
    <main>
        <section id="conjunto-categorias">
            <div class="box-categorias">
                <h1><?php echo $tituloPagina; ?></h1>
                <div class="botoes-anos">
                    <?php foreach ($anosDaCategoria as $anoInfo): ?>
                        <button class="btn_contagens" data-turma-id="<?php echo htmlspecialchars($anoInfo['id']); ?>">
                            <?php echo htmlspecialchars($anoInfo['nome']); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <div id="btn_feito">
            <button>Enviar</button>
        </div>
    </main>

    <footer>
        <div id="fig_bottom"></div>
        <img src="../assets/losangos_bottom.png" alt="" id="losangos">
    </footer>

    <dialog id="modal" class="modal-contagem">
        <div class="content">
            <div class="titulo" id="titulo-modal"></div>
            <div class="contador">
                <button class="btn-icon" onclick="alterarValor(-1)">
                    <i class="fa-solid fa-circle-minus" style="color: red;"></i>
                </button>

                <input id="valor" type="number" value="0" min="0" max="100" style="width: 60px; text-align: center; font-weight: bold; font-size: 20px; border: none; background: transparent;" />

                <button class="btn-icon" onclick="alterarValor(1)">
                    <i class="fa-solid fa-circle-plus" style="color: green;"></i>
                </button>
            </div>

            <button class="btn-feito" onclick="document.getElementById('modal').close()">Feito</button>
        </div>
    </dialog>

    <script>
        console.log(accessTokenFromPHP)

        const abrir_menu = document.querySelector('.hamburguer');
        const menu = document.querySelector('.menu');

        // Verifica se os elementos do menu existem na página atual
        if (abrir_menu && menu) {
            abrir_menu.addEventListener('click', () => {
                abrir_menu.classList.toggle('aberto'); // Adiciona/remove a classe 'aberto' no botão
                menu.classList.toggle('ativo'); // Adiciona/remove a classe 'ativo' no menu (nav)
            });
        }

        // Variável para armazenar as contagens das turmas antes de enviar
        // A chave será o nome da sala (ex: "1º Ano"), o valor será um objeto {id: turma_id, quantidade: N}
        let contagensPorTurma = {};
        let currentTurmaId = null; // Para armazenar o ID da turma do modal atualmente aberto

        // A data selecionada do calendário, passada via PHP (Formato YYYY-MM-DD)
        const dataSelecionadaPHP = "<?php echo $dataSelecionada; ?>";

        // --- Funções Auxiliares para Tokens e Logout ---
        const logoutBtn = document.getElementById('logout-btn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', async function(e) {
                e.preventDefault();
                const currentRefreshToken = sessionStorage.getItem('refresh_token');

                if (!currentRefreshToken) {
                    alert('Não há token para logout.');
                    sessionStorage.clear();
                    window.location.href = 'index.php'; // Redireciona para sua página de login
                    return;
                }

                try {
                    const response = await fetch('../../back-end/endpoints/logout.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            refreshToken: currentRefreshToken
                        })
                    });
                    const data = await response.json();

                    if (data.mensagem || response.ok) {
                        alert(data.mensagem || "Logout realizado com sucesso.");
                    } else {
                        alert('Erro ao fazer logout: ' + (data.erro || "Erro desconhecido"));
                        console.error("Erro de logout:", data);
                    }
                } catch (err) {
                    alert('Erro na requisição de logout: ' + err.message);
                    console.error("Erro de rede no logout:", err);
                } finally {
                    sessionStorage.clear();
                    window.location.href = 'index.php'; // Redireciona para sua página de login
                }
            });
        }
        // --- Fim Funções Auxiliares ---

        // Função global para alterar o valor do contador no modal (chamada via onclick no HTML)
        window.alterarValor = function(delta) {
            const input = document.getElementById('valor');
            if (!input) return;

            const min = parseInt(input.min) || 0;
            const max = parseInt(input.max) || Infinity;
            let valor = parseInt(input.value) || 0;

            valor = Math.min(max, Math.max(min, valor + delta));
            input.value = valor;
        };

        // Lógica principal executada após o DOM ser completamente carregado
        document.addEventListener('DOMContentLoaded', () => {
            const botoesAnos = document.querySelectorAll('.botoes-anos .btn_contagens'); // Seleciona TODOS os botões de ano/série
            const modal = document.getElementById('modal');
            const tituloModal = document.getElementById('titulo-modal');
            const inputValorModal = document.getElementById('valor');
            const btnFeitoTelaSalas = document.querySelector('#btn_feito button'); // Botão "Concluído e Enviar" da tela
            const btnFeitoModal = modal ? modal.querySelector('.btn-feito') : null; // Botão "Feito" dentro do modal

            // Adiciona event listeners a CADA UM dos botões de ano/série gerados pelo PHP
            botoesAnos.forEach(button => {
                button.addEventListener('click', () => {
                    const nomeAno = button.innerText.trim();
                    // Captura o ID da turma do atributo data-turma-id que o PHP injetou
                    const turmaId = parseInt(button.dataset.turmaId);

                    console.log(`Botão de sala clicado: ${nomeAno}, Turma ID: ${turmaId}`); // Ajuda na depuração

                    if (tituloModal) {
                        tituloModal.innerText = nomeAno; // Define o título do modal
                    }
                    if (inputValorModal) {
                        // Carrega o valor que foi salvo localmente para esta sala, se existir; caso contrário, 0
                        inputValorModal.value = contagensPorTurma[nomeAno] ? contagensPorTurma[nomeAno].quantidade : 0;
                    }
                    currentTurmaId = turmaId; // Armazena o ID da turma atual para uso quando o modal for "Feito"
                    if (modal) {
                        modal.showModal(); // Abre o modal
                    }
                });
            });

            // Lógica para fechar o modal ao clicar fora dele (background)
            if (modal) {
                window.onclick = function(event) {
                    if (event.target === modal) {
                        modal.close();
                    }
                };
            }

            // Lógica do botão "Feito" DENTRO DO MODAL
            if (btnFeitoModal) {
                btnFeitoModal.addEventListener('click', () => {
                    if (currentTurmaId !== null && tituloModal && inputValorModal) {
                        const nomeSala = tituloModal.innerText.trim();
                        const quantidade = parseInt(inputValorModal.value);

                        // Armazena a contagem no objeto 'contagensPorTurma'
                        contagensPorTurma[nomeSala] = {
                            id: currentTurmaId,
                            quantidade: quantidade
                        };
                        console.log(`Contagem para "${nomeSala}" (ID: ${currentTurmaId}) salva localmente: ${quantidade}`);
                        console.log("Contagens atuais armazenadas:", contagensPorTurma);
                    }
                    modal.close(); // Fecha o modal
                });
            }

            // Lógica do botão "Concluído e Enviar" DA TELA DE SELEÇÃO DE SALAS (para enviar ao backend)
            if (btnFeitoTelaSalas) {
                btnFeitoTelaSalas.addEventListener('click', async () => {
                    // Obtém o access token do sessionStorage. Esta é a primeira linha de defesa.
                    const accessToken = sessionStorage.getItem('access_token');
                    if (!accessToken) {
                        alert("Sessão expirada ou token de acesso não encontrado. Faça login novamente.");
                        window.location.href = 'index.php'; // Redireciona para sua página de login
                        return;
                    }

                    // Prepara os dados no formato esperado pelo endpoint PHP
                    const dadosParaEnviar = {
                        data_contagem: dataSelecionadaPHP, // A data selecionada do calendário
                        contagens_turmas: [] // Array para as contagens de cada turma
                    };

                    // Itera sobre as contagens armazenadas localmente para preencher 'contagens_turmas'
                    for (const nomeSala in contagensPorTurma) {
                        if (contagensPorTurma.hasOwnProperty(nomeSala)) {
                            const turmaData = contagensPorTurma[nomeSala];
                            dadosParaEnviar.contagens_turmas.push({
                                turma_id: turmaData.id,
                                quantidade: turmaData.quantidade
                            });
                        }
                    }

                    // Verifica se há alguma contagem para enviar
                    if (dadosParaEnviar.contagens_turmas.length === 0) {
                        alert("Nenhuma contagem de turma para enviar. Por favor, preencha as contagens.");
                        return;
                    }

                    console.log("Dados que serão enviados ao backend:", dadosParaEnviar);

                    try {
                        // Envia os dados para o endpoint PHP
                        const response = await fetch('../../back-end/endpoints/post_contagem.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': `Bearer ${accessToken}` // IMPORTANTE: Adiciona "Bearer " antes do token
                            },
                            body: JSON.stringify(dadosParaEnviar) // Converte o objeto JavaScript em JSON
                        });

                        const result = await response.json(); // Analisa a resposta JSON do servidor

                        if (response.ok) { // Se a resposta HTTP for 2xx (Sucesso)
                            alert(result.mensagem || "Contagem enviada com sucesso!");
                            contagensPorTurma = {}; // Limpa as contagens após o envio
                            // Redireciona para a página de contagem de categorias,
                            // PASSANDO A DATA DE VOLTA para que ela não seja perdida
                            window.location.href = `contagem.php?data=${encodeURIComponent(dataSelecionadaPHP)}`;
                        } else {
                            // Se a resposta não foi OK (ex: 401 Unauthorized, 403 Forbidden, 500 Internal Server Error)
                            if (response.status === 401 || response.status === 403) {
                                alert("Sessão inválida ou expirada. Faça login novamente.");
                                sessionStorage.clear(); // Limpa tokens locais
                                window.location.href = 'index.php'; // Redireciona para sua página de login
                            } else {
                                alert(`Erro ao enviar contagem: ${result.erro || "Erro desconhecido"}`);
                                console.error("Erro do servidor:", result);
                            }
                        }
                    } catch (error) { // Erros de rede (sem conexão, DNS falhou, etc.) ou problemas de parsing JSON
                        alert("Erro na requisição: " + error.message + ". Verifique sua conexão ou o caminho do endpoint.");
                        console.error("Erro de rede ou processamento:", error);
                    }
                });
            }
        });
    </script>
</body>

</html>