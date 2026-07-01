<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

$data_inicio = $_GET['data_inicio'] ?? '';
$data_fim = $_GET['data_fim'] ?? '';

$sql = "SELECT 
            atendimentos.id,
            atendimentos.data_atendimento,
            atendimentos.hora_atendimento,
            atendimentos.descricao,
            atendimentos.observacao,
            atendimentos.status,
            pessoas.nome AS pessoa_nome,
            tipos_atendimentos.nome AS tipo_nome,
            usuarios.nome AS usuario_nome
        FROM atendimentos
        INNER JOIN pessoas ON atendimentos.pessoa_id = pessoas.id
        INNER JOIN tipos_atendimentos ON atendimentos.tipo_atendimento_id = tipos_atendimentos.id
        INNER JOIN usuarios ON atendimentos.usuario_id = usuarios.id
        WHERE 1 = 1";

$parametros = [];

if ($data_inicio !== '') {
    $sql .= " AND atendimentos.data_atendimento >= :data_inicio";
    $parametros[':data_inicio'] = $data_inicio;
}

if ($data_fim !== '') {
    $sql .= " AND atendimentos.data_atendimento <= :data_fim";
    $parametros[':data_fim'] = $data_fim;
}

$sql .= " ORDER BY atendimentos.data_atendimento DESC, atendimentos.hora_atendimento DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($parametros);
$atendimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = count($atendimentos);

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
    <title>Relatório de Atendimentos - AtendeLab</title>
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
            display: inline-block;
        }

        .btn-secundario {
            background: #6b7280;
        }

        .btn-verde {
            background: #16a34a;
        }

        .filtros {
            background: #ffffff;
            padding: 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
        }

        .filtros form {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            align-items: end;
        }

        .filtros label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            color: #374151;
            font-size: 14px;
        }

        .filtros input {
            padding: 10px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
        }

        .resumo {
            background: #ffffff;
            padding: 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
        }

        .relatorio {
            background: #ffffff;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
        }

        .cabecalho-relatorio {
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 16px;
            margin-bottom: 18px;
        }

        .cabecalho-relatorio h2 {
            margin: 0 0 8px 0;
        }

        .cabecalho-relatorio p {
            margin: 4px 0;
            color: #4b5563;
            font-size: 14px;
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
            vertical-align: top;
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

        .descricao {
            max-width: 320px;
        }

        @media print {
            body {
                background: #ffffff;
            }

            header,
            .topo,
            .filtros,
            .botoes-impressao {
                display: none;
            }

            main {
                padding: 0;
            }

            .relatorio {
                box-shadow: none;
                border-radius: 0;
                padding: 0;
            }

            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
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
        <span> | Relatórios</span>
    </div>

    <a href="../logout.php">Sair</a>
</header>

<main>
    <div class="topo">
        <div>
            <h1>Relatório de atendimentos</h1>
            <p>Consulte os atendimentos por período e gere uma versão para impressão.</p>
        </div>

        <div>
            <a class="btn btn-secundario" href="../dashboard.php">Voltar</a>
            <button class="btn btn-verde" onclick="window.print()">Imprimir relatório</button>
        </div>
    </div>

    <section class="filtros">
        <form method="GET" action="">
            <div>
                <label for="data_inicio">Data inicial</label>
                <input type="date" id="data_inicio" name="data_inicio" value="<?php echo htmlspecialchars($data_inicio); ?>">
            </div>

            <div>
                <label for="data_fim">Data final</label>
                <input type="date" id="data_fim" name="data_fim" value="<?php echo htmlspecialchars($data_fim); ?>">
            </div>

            <div>
                <button class="btn" type="submit">Gerar relatório</button>
                <a class="btn btn-secundario" href="atendimentos.php">Limpar</a>
            </div>
        </form>
    </section>

    <section class="resumo">
        <strong>Total de registros encontrados:</strong>
        <?php echo $total; ?>
    </section>

    <section class="relatorio">
        <div class="cabecalho-relatorio">
            <h2>AtendeLab — Relatório de Atendimentos</h2>

            <p>
                <strong>Período:</strong>
                <?php 
                    if ($data_inicio && $data_fim) {
                        echo date('d/m/Y', strtotime($data_inicio)) . ' até ' . date('d/m/Y', strtotime($data_fim));
                    } elseif ($data_inicio) {
                        echo 'A partir de ' . date('d/m/Y', strtotime($data_inicio));
                    } elseif ($data_fim) {
                        echo 'Até ' . date('d/m/Y', strtotime($data_fim));
                    } else {
                        echo 'Todos os registros';
                    }
                ?>
            </p>

            <p>
                <strong>Emitido em:</strong>
                <?php echo date('d/m/Y H:i'); ?>
            </p>

            <p>
                <strong>Usuário responsável pela emissão:</strong>
                <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>
            </p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Hora</th>
                    <th>Pessoa</th>
                    <th>Tipo</th>
                    <th>Responsável</th>
                    <th>Status</th>
                    <th>Descrição</th>
                    <th>Observação</th>
                </tr>
            </thead>

            <tbody>
                <?php if (count($atendimentos) > 0): ?>
                    <?php foreach ($atendimentos as $atendimento): ?>
                        <tr>
                            <td><?php echo date('d/m/Y', strtotime($atendimento['data_atendimento'])); ?></td>
                            <td><?php echo substr($atendimento['hora_atendimento'], 0, 5); ?></td>
                            <td><?php echo htmlspecialchars($atendimento['pessoa_nome']); ?></td>
                            <td><?php echo htmlspecialchars($atendimento['tipo_nome']); ?></td>
                            <td><?php echo htmlspecialchars($atendimento['usuario_nome']); ?></td>
                            <td>
                                <span class="status-<?php echo htmlspecialchars($atendimento['status']); ?>">
                                    <?php echo formatarStatus($atendimento['status']); ?>
                                </span>
                            </td>
                            <td class="descricao"><?php echo htmlspecialchars($atendimento['descricao']); ?></td>
                            <td class="descricao">
                                <?php 
                                    echo $atendimento['observacao'] 
                                        ? htmlspecialchars($atendimento['observacao']) 
                                        : '-'; 
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">Nenhum atendimento encontrado para o período selecionado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
</main>

</body>
</html>