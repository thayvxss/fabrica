<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - AtendeLab</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3f5f7;
            color: #1f2937;
        }

        header {
            background: #111827;
            color: #ffffff;
            padding: 18px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header a {
            color: #ffffff;
            text-decoration: none;
            background: #dc2626;
            padding: 9px 14px;
            border-radius: 7px;
        }

        main {
            padding: 32px;
        }

        .card {
            max-width: 760px;
            background: #ffffff;
            padding: 26px;
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.07);
        }

        h1 {
            margin-top: 0;
        }

        .dados {
            margin: 22px 0;
            padding: 16px;
            background: #f9fafb;
            border-radius: 10px;
        }

        .dados p {
            margin: 8px 0;
        }

        .botoes {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .btn {
            display: inline-block;
            background: #2563eb;
            color: #ffffff;
            padding: 11px 15px;
            border-radius: 8px;
            text-decoration: none;
        }

        .btn-secundario {
            background: #6b7280;
        }
    </style>
</head>
<body>

<header>
    <strong>AtendeLab | Área restrita</strong>

    <a href="/atendelab/public/?controller=auth&action=logout">
        Sair
    </a>
</header>

<main>
    <section class="card">
        <h1>Dashboard protegido</h1>

        <p>
            A sessão foi criada com sucesso e o usuário possui acesso
            à área administrativa.
        </p>

        <div class="dados">
            <p>
                <strong>Nome:</strong>
                <?php echo htmlspecialchars($usuario['nome'] ?? ''); ?>
            </p>

            <p>
                <strong>E-mail:</strong>
                <?php echo htmlspecialchars($usuario['email'] ?? ''); ?>
            </p>

            <p>
                <strong>Perfil:</strong>
                <?php echo htmlspecialchars($usuario['perfil'] ?? ''); ?>
            </p>
        </div>

        <div class="botoes">
            <a
                class="btn"
                href="/atendelab/public/?controller=usuarios&action=listar"
            >
                Testar rota protegida de usuários
            </a>

            <a
                class="btn btn-secundario"
                href="/atendelab/dashboard.php"
            >
                Abrir dashboard completo
            </a>
        </div>
    </section>
</main>

</body>
</html>