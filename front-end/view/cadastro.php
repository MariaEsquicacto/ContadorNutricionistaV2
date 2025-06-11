<?php
$mensagem = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['txtnome'];
    $senha = $_POST['txtsenha'];
    $confirmacao = $_POST['txtconfirmacao'];

    $dados = [
        'nome' => $nome,
        'senha' => $senha,
        'confirmacao' => $confirmacao
    ];

    $json = json_encode($dados);

    $opts = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => $json
        ]
    ];

    $context = stream_context_create($opts);
    $result = json_decode(file_get_contents('http://localhost/contador_phpv2/back-end/endpoints/post_user.php', false, $context), true);

    $mensagem = $result['mensagem'] ?? $result['erro'] ?? null;
}
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
    <title>Login</title>
</head>

<body>
    <header>
        <div id="fig_top"></div>
    </header>

    <main>
        <section id="section_form">
            <div id="logo_formulario">
                <img src="../assets/DevTheBlaze.png" alt="Logo DevTheBlaze">
            </div>
            <div>
                <h1 id="titulo_cadastro">Cadastre-se</h1>
            </div>
            <form method="POST">
                <div class="input-container">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" name="txtnome" id="txtnome" placeholder="Usuário">
                </div>

                <div class="input-container">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="txtsenha" id="txtsenha" placeholder="Senha">
                </div>
                <div class="input-container">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="txtconfirmacao" id="txtconfirmacao" placeholder="Confirmar Senha">
                </div>

                <button type="submit">Acessar</button>

                <a href="index.php">Faça seu Login</a>
            </form>
        </section>
    </main>

    <footer>
        <div id="fig_bottom"></div>
        <img src="../assets/losangos_bottom.png" alt="" id="losangos">
    </footer>

    <?php if ($mensagem):
        $icone = strpos($mensagem, 'sucesso') !== false ? 'success' : 'error';
        $titulo = strpos($mensagem, 'sucesso') !== false ? 'Sucesso' : 'Erro';
    ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                icon: '<?= $icone ?>',
                title: '<?= $titulo ?>',
                text: <?= json_encode($mensagem) ?>
            });
        </script>
    <?php endif; ?>

</body>

</html>