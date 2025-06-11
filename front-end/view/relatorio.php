<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Estoque</title>
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
                <li><a href="index.php">Sair</a></li>
            </ul>
        </nav>
    </div>

    <main>
        <section id="conjunto-categorias">
            <div>
                <h1>Relatório</h1> <!-- ou 'Relatório', dependendo da página -->
            </div>
            <div id="btns-categorias">
                <button class="btn_contagens">Contagem Ensino Fundamental 1A</button>
                <button class="btn_contagens">Contagem Ensino Fundamental 1B</button>
                <button class="btn_contagens">Contagem Ensino Fundamental 2A</button>
                <button class="btn_contagens">Contagem Ensino Fundamental 2B</button>
                <button class="btn_contagens">Contagem Ensino Médio</button>
            </div>
        </section>
    </main>

    <footer>
        <div id="fig_bottom"></div>
        <img src="assets/losangos_bottom.png" alt="Rodapé decorativo" id="losangos">
    </footer>

    <script>
        const abrir_menu = document.querySelector('.hamburguer');
        const menu = document.querySelector('.menu');

        abrir_menu.addEventListener('click', () => {
            abrir_menu.classList.toggle('aberto');
            menu.classList.toggle('ativo');
        });
    </script>
</body>

</html>