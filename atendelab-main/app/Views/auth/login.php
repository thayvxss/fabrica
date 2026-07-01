<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - AtendeLab</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background: #f3f5f7;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1f2937;
        }

        .login-card {
            width: 100%;
            max-width: 380px;
            background: #ffffff;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            box-sizing: border-box;
        }

        h1 {
            margin: 0 0 8px;
            color: #111827;
        }

        p {
            margin-top: 0;
            color: #6b7280;
            line-height: 1.5;
        }

        label {
            display: block;
            margin-top: 16px;
            margin-bottom: 6px;
            font-weight: bold;
            color: #374151;
        }

        input {
            width: 100%;
            padding: 11px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            margin-top: 20px;
            padding: 12px;
            background: #2563eb;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #1d4ed8;
        }

        .alerta {
            padding: 11px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 14px;
        }

        .erro {
            background: #fee2e2;
            color: #991b1b;
        }

        .sucesso {
            background: #dcfce7;
            color: #166534;
        }

        .voltar {
            display: block;
            margin-top: 16px;
            text-align: center;
            color: #2563eb;
            text-decoration: none;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="login-card">
    <h1>AtendeLab</h1>
    <p>Entre para acessar a área administrativa.</p>

    <?php if (!empty($erro)): ?>
        <div class="alerta erro">
            <?php echo htmlspecialchars($erro); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($mensagem)): ?>
        <div class="alerta sucesso">
            <?php echo htmlspecialchars($mensagem); ?>
        </div>
    <?php endif; ?>

    <form
        method="POST"
        action="/atendelab/public/?controller=auth&action=entrar"
    >
        <label for="email">E-mail</label>
        <input
            type="email"
            id="email"
            name="email"
            placeholder="admin@atendelab.com"
            required
        >

        <label for="senha">Senha</label>
        <input
            type="password"
            id="senha"
            name="senha"
            placeholder="Digite sua senha"
            required
        >

        <button type="submit">Entrar</button>
    </form>

    <a class="voltar" href="/atendelab/public/">
        Voltar para a página inicial
    </a>
</div>

</body>
</html>