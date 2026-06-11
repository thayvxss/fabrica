<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

$sql = "SELECT * FROM pessoas ORDER BY criado_em DESC";
$stmt = $pdo->query($sql);
$pessoas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$mensagem = $_GET['mensagem'] ?? '';

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Pessoas Atendidas - AtendeLab</title>
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
        <span> | Pessoas atendidas</span>
    </div>

    <a href="../logout.php">Sair</a>
</header>

<main>
    <div class="topo">
        <div>
            <h1>Pessoas atendidas</h1>
            <p>Cadastro de alunos ou pessoas que recebem atendimento acadêmico.</p>
        </div>

        <div>
            <a class="btn btn-secundario" href="../dashboard.php">Voltar</a>
            <a class="btn" href="criar.php">Nova pessoa</a>
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
                <th>Documento</th>
                <th>E-mail</th>
                <th>Telefone</th>
                <th>Curso</th>
                <th>Período</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>

        <tbody>
            <?php if (count($pessoas) > 0): ?>
                <?php foreach ($pessoas as $pessoa): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($pessoa['nome']); ?></td>
                        <td><?php echo htmlspecialchars($pessoa['documento']); ?></td>
                        <td><?php echo htmlspecialchars($pessoa['email']); ?></td>
                        <td><?php echo htmlspecialchars($pessoa['telefone']); ?></td>
                        <td><?php echo htmlspecialchars($pessoa['curso']); ?></td>
                        <td><?php echo htmlspecialchars($pessoa['periodo']); ?></td>
                        <td>
                            <span class="<?php echo $pessoa['status'] === 'ativo' ? 'status-ativo' : 'status-inativo'; ?>">
                                <?php echo htmlspecialchars($pessoa['status']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="acoes">
                                <a class="btn" href="editar.php?id=<?php echo $pessoa['id']; ?>">Editar</a>
                                <a class="btn btn-alerta" href="excluir.php?id=<?php echo $pessoa['id']; ?>" onclick="return confirm('Deseja realmente inativar esta pessoa?')">Inativar</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">Nenhuma pessoa cadastrada.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

</body>
</html>