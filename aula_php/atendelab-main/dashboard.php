<?php

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

$totalAtendimentos = $pdo->query("SELECT COUNT(*) FROM atendimentos")->fetchColumn();
$totalAbertos = $pdo->query("SELECT COUNT(*) FROM atendimentos WHERE status = 'aberto'")->fetchColumn();
$totalAndamento = $pdo->query("SELECT COUNT(*) FROM atendimentos WHERE status = 'em_andamento'")->fetchColumn();
$totalConcluidos = $pdo->query("SELECT COUNT(*) FROM atendimentos WHERE status = 'concluido'")->fetchColumn();
$totalPessoas = $pdo->query("SELECT COUNT(*) FROM pessoas WHERE status = 'ativo'")->fetchColumn();
$totalTipos = $pdo->query("SELECT COUNT(*) FROM tipos_atendimentos WHERE status = 'ativo'")->fetchColumn();

$sqlUltimos = "SELECT 
                    atendimentos.id,
                    atendimentos.data_atendimento,
                    atendimentos.status,
                    pessoas.nome AS pessoa_nome,
                    tipos_atendimentos.nome AS tipo_nome
                FROM atendimentos
                INNER JOIN pessoas ON atendimentos.pessoa_id = pessoas.id
                INNER JOIN tipos_atendimentos ON atendimentos.tipo_atendimento_id = tipos_atendimentos.id
                ORDER BY atendimentos.criado_em DESC
                LIMIT 5";

$stmtUltimos = $pdo->query($sqlUltimos);
$ultimosAtendimentos = $stmtUltimos->fetchAll(PDO::FETCH_ASSOC);

function formatarStatus($status) {
    if ($status === 'aberto') {
        return 'Aberto';
    }

    if ($status === 'em_andamento') {
        return 'Em andamento';
    }

    if ($status === 'concluido') {
        return 'Concluído';
    }

    return $status;
}

?>

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
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 14px;
        }

        main {
            padding: 32px;
        }

        .boas-vindas {
            margin-bottom: 26px;
        }

        .boas-vindas h1 {
            margin-bottom: 8px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 18px;
            margin-top: 24px;
        }

        .card {
            background: #ffffff;
            padding: 22px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
        }

        .card span {
            display: block;
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .card strong {
            font-size: 30px;
            color: #2563eb;
        }

        .menu {
            margin-top: 32px;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .menu a {
            background: #2563eb;
            color: #ffffff;
            padding: 12px 16px;
            border-radius: 8px;
            text-decoration: none;
        }

        .menu a:hover {
            background: #1d4ed8;
        }

        .tabela-card {
            margin-top: 32px;
            background: #ffffff;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            font-size: 14px;
        }

        th {
            background: #f9fafb;
            color: #374151;
        }

        .status-aberto {
            color: #b45309;
            font-weight: bold;
        }

        .status-em_andamento {
            color: #2563eb;
            font-weight: bold;
        }

        .status-concluido {
            color: #166534;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            main {
                padding: 18px;
            }

            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
    </style>
</head>
<body>

<header>
    <div>
        <strong>AtendeLab</strong>
        <span> | Olá, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></span>
    </div>

    <a href="logout.php">Sair</a>
</header>

<main>
    <section class="boas-vindas">
        <h1>Dashboard</h1>
        <p>Resumo geral dos atendimentos acadêmicos cadastrados no sistema.</p>
    </section>

    <section class="cards">
        <div class="card">
            <span>Total de atendimentos</span>
            <strong><?php echo $totalAtendimentos; ?></strong>
        </div>

        <div class="card">
            <span>Atendimentos abertos</span>
            <strong><?php echo $totalAbertos; ?></strong>
        </div>

        <div class="card">
            <span>Em andamento</span>
            <strong><?php echo $totalAndamento; ?></strong>
        </div>

        <div class="card">
            <span>Concluídos</span>
            <strong><?php echo $totalConcluidos; ?></strong>
        </div>

        <div class="card">
            <span>Pessoas ativas</span>
            <strong><?php echo $totalPessoas; ?></strong>
        </div>

        <div class="card">
            <span>Tipos ativos</span>
            <strong><?php echo $totalTipos; ?></strong>
        </div>
    </section>

    <section class="menu">
        <a href="pessoas/index.php">Pessoas atendidas</a>
        <a href="tipos/index.php">Tipos de atendimento</a>
        <a href="atendimentos/index.php">Atendimentos</a>
        <a href="relatorios/atendimentos.php">Relatórios</a>
    </section>

    <section class="tabela-card">
        <h2>Últimos atendimentos</h2>
        <p>Lista dos cinco registros mais recentes.</p>

        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Pessoa</th>
                    <th>Tipo</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                <?php if (count($ultimosAtendimentos) > 0): ?>
                    <?php foreach ($ultimosAtendimentos as $atendimento): ?>
                        <tr>
                            <td><?php echo date('d/m/Y', strtotime($atendimento['data_atendimento'])); ?></td>
                            <td><?php echo htmlspecialchars($atendimento['pessoa_nome']); ?></td>
                            <td><?php echo htmlspecialchars($atendimento['tipo_nome']); ?></td>
                            <td>
                                <span class="status-<?php echo htmlspecialchars($atendimento['status']); ?>">
                                    <?php echo formatarStatus($atendimento['status']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">Nenhum atendimento registrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
</main>

</body>
</html>