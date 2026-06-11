<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

$sql = "SELECT * FROM tipos_atendimentos ORDER BY criado_em DESC";
$stmt = $pdo->query($sql);
$tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$mensagem = $_GET['mensagem'] ?? '';

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Tipos de Atendimento - AtendeLab</title>
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
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 14px;
        }

        main {
            padding: 32px;
        }

        .topo {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .btn {
            background: #2563eb;
            color: #ffffff;
            padding: 10px 14px;
            border-radius: 8px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-secundario {
            background: #6b7280;
        }

        .btn-alerta {
            background: #dc2626;
        }

        .mensagem {
            background: #dcfce7;
            color: #166534;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
        }

        th, td {
            padding: 14px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            font-size: 14px;
        }

        th {
            background: #f9fafb;
            color: #374151;
        }

        .acoes {
            display: flex;
            gap: 8px;
        }

        .status-ativo {
            color: #166534;
            font-weight: bold;
        }

        .status-inativo {
            color: #991b1b;
            font-weight: bold;
        }
    </style>
</head>
<body>

<header>
    <div>
        <strong>AtendeLab</strong>
        <span> | Tipos de atendimento</span>
    </div>

    <a href="../logout.php">Sair</a>
</header>

<main>
    <div class="topo">
        <div>
            <h1>Tipos de atendimento</h1>
            <p>Cadastro das categorias utilizadas para classificar os atendimentos.</p>
        </div>

        <div>
            <a class="btn btn-secundario" href="../dashboard.php">Voltar</a>
            <a class="btn" href="criar.php">Novo tipo</a>
        </div>
    </div>

    <?php if ($mensagem): ?>
        <div class="mensagem">
            <?php echo htmlspecialchars($mensagem); ?>
        </div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>

        <tbody>
            <?php if (count($tipos) > 0): ?>
                <?php foreach ($tipos as $tipo): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($tipo['nome']); ?></td>
                        <td><?php echo htmlspecialchars($tipo['descricao']); ?></td>
                        <td>
                            <span class="<?php echo $tipo['status'] === 'ativo' ? 'status-ativo' : 'status-inativo'; ?>">
                                <?php echo htmlspecialchars($tipo['status']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="acoes">
                                <a class="btn" href="editar.php?id=<?php echo $tipo['id']; ?>">Editar</a>
                                <a class="btn btn-alerta" href="excluir.php?id=<?php echo $tipo['id']; ?>" onclick="return confirm('Deseja realmente inativar este tipo de atendimento?')">Inativar</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">Nenhum tipo de atendimento cadastrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

</body>
</html>