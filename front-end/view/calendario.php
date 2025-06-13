<?php
session_start();

// Pega os tokens da sessão PHP
// Usamos (string) para garantir que sejam strings ou vazias, para evitar notices.
$refreshToken = (string)($_SESSION['refresh_token'] ?? '');
$accessToken = (string)($_SESSION['access_token'] ?? '');

// Limpa os tokens da sessão PHP IMEDIATAMENTE após a primeira leitura
// Isso é importante para segurança: os tokens JWT devem ser gerenciados pelo JS no navegador
unset($_SESSION['refresh_token']);
unset($_SESSION['access_token']);

// --- Debugging de Sessão PHP no Servidor ---
error_log("DEBUG CALENDARIO PHP: Access Token lido da SESSION PHP: " . ($accessToken === '' ? 'NÃO ENCONTRADO' : $accessToken));
error_log("DEBUG CALENDARIO PHP: Refresh Token lido da SESSION PHP: " . ($refreshToken === '' ? 'NÃO ENCONTRADO' : $refreshToken));
// --- Fim Debugging de Sessão PHP ---

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Calendário</title>
    <style>
        #month-year {
            margin: 0 15px;
            font-size: 1.8em;
            font-weight: bold;
            color: #000000;
            font-family: 'Courier New', Courier, monospace;
        }
        th {
            background-color: #900000;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 10px;
            color: white;
        }
        td.selected {
            background-color: #900000;
            color: white;
        }
    </style>
</head>

<body>
    <header>
        <div id="fig_top"></div>
    </header>
    <main>
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

        <section id="background_calendar">
            <div class="calendar-container">
                <div class="calendar-header">
                    <button onclick="changeMonth(-1)">&#9665;</button>
                    <span id="month-year"></span>
                    <button onclick="changeMonth(1)">&#9655;</button>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>DOMINGO</th>
                            <th>SEGUNDA-FEIRA</th>
                            <th>TERÇA-FEIRA</th>
                            <th>QUARTA-FEIRA</th>
                            <th>QUINTA-FEIRA</th>
                            <th>SEXTA-FEIRA</th>
                            <th>SÁBADO</th>
                        </tr>
                    </thead>
                    <tbody id="calendar-body"></tbody>
                </table>
            </div>
            <div class="selected-day" id="selected-day">Nenhum dia selecionado</div>
        </section>

        <div id="btn_confirmar_dia">
            <button>Concluído</button>
        </div>
    </main>

    <footer>
        <div id="fig_bottom"></div>
        <img src="assets/losangos_bottom.png" alt="" id="losangos">
    </footer>

    <script>
        // Transfere os tokens da sessão PHP para o sessionStorage do navegador
        // json_encode já retorna uma string JS válida (ex: "meutoken" ou null)
        const accessTokenFromPHP = <?= json_encode($accessToken) ?>;
        const refreshTokenFromPHP = <?= json_encode($refreshToken) ?>;

        console.log(accessTokenFromPHP)
        if (accessTokenFromPHP) { // Verifica se é null ou uma string vazia PHP
            sessionStorage.setItem('access_token', accessTokenFromPHP);
            console.log("DEBUG CALENDARIO JS: Access Token salvo no sessionStorage:", accessTokenFromPHP);
        } else {
            console.log("DEBUG CALENDARIO JS: Access Token NÃO encontrado na sessão PHP para salvar ou era vazio/nulo.");
            sessionStorage.removeItem('access_token'); // Garante que não há lixo no sessionStorage
        }
        if (refreshTokenFromPHP) { // Verifica se é null ou uma string vazia PHP
            sessionStorage.setItem('refresh_token', refreshTokenFromPHP);
            console.log("DEBUG CALENDARIO JS: Refresh Token salvo no sessionStorage:", refreshTokenFromPHP);
        } else {
            console.log("DEBUG CALENDARIO JS: Refresh Token NÃO encontrado na sessão PHP para salvar ou era vazio/nulo.");
            sessionStorage.removeItem('refresh_token'); // Garante que não há lixo no sessionStorage
        }

        // Funções do Menu Hambúrguer
        document.addEventListener('DOMContentLoaded', () => {
            const abrir_menu = document.querySelector('.hamburguer');
            const menu = document.querySelector('.menu');

            if (abrir_menu && menu) {
                abrir_menu.addEventListener('click', () => {
                    abrir_menu.classList.toggle('aberto');
                    menu.classList.toggle('ativo');
                });
            }

            // Variáveis e Funções do Calendário
            let currentMonth = new Date().getMonth();
            let currentYear = new Date().getFullYear();
            let today = new Date().getDate();
            let selectedCell = null;

            function renderCalendar(month, year) {
                const monthYearElement = document.getElementById("month-year");
                const calendarBody = document.getElementById("calendar-body");

                // Verifica se os elementos do calendário existem antes de tentar manipulá-los
                if (!monthYearElement || !calendarBody) {
                    console.error("Elementos do calendário (month-year ou calendar-body) não encontrados.");
                    return; // Sai da função se os elementos não existirem
                }

                monthYearElement.innerText = new Date(year, month).toLocaleDateString("pt-BR", {
                    month: "long",
                    year: "numeric"
                });
                calendarBody.innerHTML = "";
                const firstDay = new Date(year, month, 1).getDay();
                const daysInMonth = new Date(year, month + 1, 0).getDate();
                let row = document.createElement("tr");

                for (let i = 0; i < firstDay; i++) {
                    row.appendChild(document.createElement("td"));
                }

                for (let i = 1; i <= daysInMonth; i++) {
                    let cell = document.createElement("td");
                    cell.innerText = i;
                    cell.onclick = function() {
                        selectDay(this, i, month, year);
                    };

                    if (i === today && month === new Date().getMonth() && year === new Date().getFullYear()) {
                        cell.classList.add("selected");
                        selectedCell = cell;
                        const selectedDayElement = document.getElementById("selected-day");
                        if (selectedDayElement) {
                            selectedDayElement.innerText = `Dia ${i}/${String(month + 1).padStart(2, "0")}/${year}`;
                        }
                    }
                    row.appendChild(cell);

                    if ((firstDay + i) % 7 === 0) {
                        calendarBody.appendChild(row);
                        row = document.createElement("tr");
                    }
                }
                calendarBody.appendChild(row);
            }

            function changeMonth(step) {
                currentMonth += step;
                if (currentMonth < 0) {
                    currentMonth = 11;
                    currentYear--;
                } else if (currentMonth > 11) {
                    currentMonth = 0;
                    currentYear++;
                }
                renderCalendar(currentMonth, currentYear);
            }

            function selectDay(cell, day, month, year) {
                if (selectedCell) {
                    selectedCell.classList.remove("selected");
                }
                selectedCell = cell;
                selectedCell.classList.add("selected");
                const selectedDayElement = document.getElementById("selected-day");
                if (selectedDayElement) {
                    selectedDayElement.innerText = `Dia ${day}/${String(month + 1).padStart(2, "0")}/${year}`;
                }
            }

            // Inicializa o calendário ao carregar a página
            renderCalendar(currentMonth, currentYear);

            const btnConcluidoCalendario = document.querySelector('#btn_confirmar_dia button');
            if (btnConcluidoCalendario) {
                btnConcluidoCalendario.addEventListener('click', function(event) {
                    event.preventDefault();

                    const selectedDayElement = document.getElementById("selected-day");
                    let dataSelecionadaTexto = "Nenhum dia selecionado";
                    if (selectedDayElement) {
                        dataSelecionadaTexto = selectedDayElement.innerText;
                    }

                    if (dataSelecionadaTexto.includes("Nenhum dia selecionado") || selectedCell === null) {
                        alert("Por favor, selecione um dia no calendário antes de continuar.");
                        return;
                    }

                    const dataPartes = dataSelecionadaTexto.replace('Dia ', '').split('/');
                    const dataFormatadaParaURL = `${dataPartes[2]}-${dataPartes[1]}-${dataPartes[0]}`;

                    window.location.href = `contagem.php?data=${dataFormatadaParaURL}`;
                });
            }

            // Lógica do botão de logout
            const logoutBtn = document.getElementById('logout-btn');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', async function(e) {
                    e.preventDefault();
                    const currentRefreshToken = sessionStorage.getItem('refresh_token');

                    if (!currentRefreshToken) {
                        alert('Não há token para logout.');
                        sessionStorage.clear();
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
                        sessionStorage.clear();
                        window.location.href = 'index.php';
                    }
                });
            }
        });
    </script>
</body>

</html>