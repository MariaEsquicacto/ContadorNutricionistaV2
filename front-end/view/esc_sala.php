<?php
session_start(); // Inicia a sessão para acesso a tokens, se necessário

// Mapeamento das categorias para os anos/séries com IDs de turma (PHP)
// ESTES SÃO EXEMPLOS. SUBSTITUA PELOS IDs REAIS DO SEU BANCO DE DADOS!
$categoriasSalasPHP = [
    "Contagem Ensino Fundamental 1A" => [
        "titulo" => "Fundamental 1A",
        "anos" => [
            ["nome" => "1º Ano", "id" => 1], // Mude para o ID real da turma "1º Ano"
            ["nome" => "2º Ano", "id" => 2]  // Mude para o ID real da turma "2º Ano"
        ]
    ],
    "Contagem Ensino Fundamental 1B" => [
        "titulo" => "Fundamental 1B",
        "anos" => [
            ["nome" => "3º Ano", "id" => 3],
            ["nome" => "4º Ano", "id" => 4],
            ["nome" => "5º Ano", "id" => 5]
        ]
    ],
    "Contagem Ensino Fundamental 2" => [
        "titulo" => "Fundamental 2",
        "anos" => [
            ["nome" => "6º Ano", "id" => 6],
            ["nome" => "7º Ano", "id" => 7],
            ["nome" => "8º Ano", "id" => 8],
            ["nome" => "9º Ano", "id" => 9]
        ]
    ],
    "Ensino Médio" => [
        "titulo" => "Ensino Médio",
        "anos" => [
            ["nome" => "1º Ano", "id" => 10],
            ["nome" => "2º Ano", "id" => 11],
            ["nome" => "3º Ano", "id" => 12]
        ]
    ]
];

// Pega a categoria e a data dos parâmetros GET
$categoriaSelecionada = $_GET['categoria'] ?? '';
$dataSelecionada = $_GET['data'] ?? ''; // Formato YYYY-MM-DD

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
            <button>Concluído e Enviar</button>
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
        // Variável para armazenar as contagens das turmas antes de enviar
        let contagensPorTurma = {};
        let currentTurmaId = null; // Para armazenar o ID da turma do modal atual

        // A data selecionada do calendário, passada via PHP (Formato YYYY-MM-DD)
        const dataSelecionadaPHP = "<?php echo $dataSelecionada; ?>";

        // --- Funções Auxiliares para Tokens e Logout ---
        // Removida a tentativa de fetch para refresh_token.php, pois não existe esse endpoint.
        // Se o access_token não existe ou é inválido, o usuário deve relogar.
        const logoutBtn = document.getElementById('logout-btn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', async function(e) {
                e.preventDefault();
                const currentRefreshToken = sessionStorage.getItem('refresh_token');

                if (!currentRefreshToken) {
                    alert('Não há token para logout.');
                    sessionStorage.clear(); // Limpa a sessão local mesmo sem token
                    window.location.href = 'index.php';
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
                    sessionStorage.clear(); // Sempre limpa a sessão local após tentar logout
                    window.location.href = 'index.php';
                }
            });
        }
        // --- Fim Funções Auxiliares ---

        // Função para alterar o valor do contador no modal (global para onclick)
        window.alterarValor = function(delta) {
            const input = document.getElementById('valor');
            if (!input) return;

            const min = parseInt(input.min) || 0;
            const max = parseInt(input.max) || Infinity;
            let valor = parseInt(input.value) || 0;

            valor = Math.min(max, Math.max(min, valor + delta));
            input.value = valor;
        };

        document.addEventListener('DOMContentLoaded', () => {
            const botoesAnos = document.querySelectorAll('.botoes-anos .btn_contagens');
            const modal = document.getElementById('modal');
            const tituloModal = document.getElementById('titulo-modal');
            const inputValorModal = document.getElementById('valor');
            const btnFeitoTelaSalas = document.querySelector('#btn_feito button');
            const btnFeitoModal = modal ? modal.querySelector('.btn-feito') : null;

            // Adiciona listeners para os botões de ano/série (gerados pelo PHP)
            botoesAnos.forEach(button => {
                button.addEventListener('click', () => {
                    const nomeAno = button.innerText.trim();
                    const turmaId = parseInt(button.dataset.turmaId); // Pega o ID do data-attribute

                    if (tituloModal) {
                        tituloModal.innerText = nomeAno;
                    }
                    if (inputValorModal) {
                        // Carrega o valor salvo anteriormente para esta turma, se existir
                        inputValorModal.value = contagensPorTurma[nomeAno] ? contagensPorTurma[nomeAno].quantidade : 0;
                    }
                    currentTurmaId = turmaId; // Guarda o ID da turma selecionada no modal
                    if (modal) {
                        modal.showModal();
                    }
                });
            });

            // Lógica para fechar modal ao clicar fora dele
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

                        // Armazena a contagem para a sala atual
                        contagensPorTurma[nomeSala] = {
                            id: currentTurmaId,
                            quantidade: quantidade
                        };
                        console.log(`Contagem para "${nomeSala}" (ID: ${currentTurmaId}) salva localmente: ${quantidade}`);
                        console.log("Contagens atuais armazenadas:", contagensPorTurma);
                    }
                    modal.close();
                });
            }

            // Lógica do botão "Feito" DA TELA DE SELEÇÃO DE SALAS (para enviar ao backend)
            if (btnFeitoTelaSalas) {
                btnFeitoTelaSalas.addEventListener('click', async () => {
                    // Obtém o access token do sessionStorage
                    const accessToken = sessionStorage.getItem('access_token');
                    if (!accessToken) {
                        // Se não há access token, alerta e redireciona para o login.
                        alert("Sessão expirada ou token de acesso não encontrado. Faça login novamente.");
                        window.location.href = 'index.php';
                        return;
                    }

                    const dadosParaEnviar = {
                        data_contagem: dataSelecionadaPHP,
                        contagens_turmas: []
                    };

                    for (const nomeSala in contagensPorTurma) {
                        if (contagensPorTurma.hasOwnProperty(nomeSala)) {
                            const turmaData = contagensPorTurma[nomeSala];
                            dadosParaEnviar.contagens_turmas.push({
                                turma_id: turmaData.id,
                                quantidade: turmaData.quantidade
                            });
                        }
                    }

                    if (dadosParaEnviar.contagens_turmas.length === 0) {
                        alert("Nenhuma contagem de turma para enviar. Por favor, preencha as contagens.");
                        return;
                    }

                    console.log("Dados que serão enviados ao backend:", dadosParaEnviar);

                    try {
                        const response = await fetch('../../back-end/endpoints/post_contagem.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': `${accessToken}` // Envia o Access Token no cabeçalho
                            },
                            body: JSON.stringify(dadosParaEnviar)
                        });

                        const result = await response.json();

                        if (response.ok) {
                            alert(result.mensagem || "Contagem enviada com sucesso!");
                            contagensPorTurma = {}; // Limpa as contagens após o envio
                            window.location.href = "contagem.php"; // Redireciona
                        } else {
                            // Se a resposta não foi ok (ex: 401 Unauthorized), pode ser token inválido/expirado
                            if (response.status === 401 || response.status === 403) {
                                alert("Sessão inválida ou expirada. Faça login novamente.");
                                sessionStorage.clear(); // Limpa tokens locais
                                window.location.href = 'index.php'; // Redireciona para login
                            } else {
                                alert(`Erro ao enviar contagem: ${result.erro || "Erro desconhecido"}`);
                                console.error("Erro do servidor:", result);
                            }
                        }
                    } catch (error) {
                        alert("Erro na requisição: " + error.message + ". Verifique sua conexão ou o caminho do endpoint.");
                        console.error("Erro de rede ou processamento:", error);
                    }
                });
            }
        });
    </script>
</body>

</html>