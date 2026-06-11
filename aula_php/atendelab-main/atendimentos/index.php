<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

$mensagem = $_GET['mensagem'] ?? '';

$status = $_GET['status'] ?? '';
$pessoa_id = $_GET['pessoa_id'] ?? '';
$tipo_atendimento_id = $_GET['tipo_atendimento_id'] ?? '';
$data_inicio = $_GET['data_inicio'] ?? '';
$data_fim = $_GET['data_fim'] ?? '';

$pessoas = $pdo->query("SELECT id, nome FROM pessoas ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);
$tipos = $pdo->query("SELECT id, nome FROM tipos_atendimentos ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT 
            atendimentos.id,
            atendimentos.data_atendimento,
            atendimentos.hora_atendimento,
            atendimentos.descricao,
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

if ($status !== '') {
    $sql .= " AND atendimentos.status = :status";
    $parametros[':status'] = $status;
}

if ($pessoa_id !== '') {
    $sql .= " AND atendimentos.pessoa_id = :pessoa_id";
    $parametros[':pessoa_id'] = $pessoa_id;
}

if ($tipo_atendimento_id !== '') {
    $sql .= " AND atendimentos.tipo_atendimento_id = :tipo_atendimento_id";
    $parametros[':tipo_atendimento_id'] = $tipo_atendimento_id;
}

if ($data_inicio !== '') {
    $sql .= " AND atendimentos.data_atendimento >= :data_inicio";
    $parametros[':data_inicio'] = $data_inicio;
}

if ($data_fim !== '') {
    $sql .= " AND atendimentos.data_atendimento <= :data_fim";
    $parametros[':data_fim'] = $data_fim;
}

$sql .= " ORDER BY atendimentos.criado_em DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($parametros);
$atendimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Atendimentos - AtendeLab</title>
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

        .btn-alerta {
            background: #dc2626;
        }

        .btn-verde {
            background: #16a34a;
        }

        .mensagem {
            background: #dcfce7;
            color: #166534;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 18px;
        }

        .filtros {
            background: #ffffff;
            padding: 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
        }

        .filtros form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
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

        .filtros input,
        .filtros select {
            width: 100%;
            padding: 10px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            box-sizing: border-box;
        }

        .filtros .botoes-filtro {
            display: flex;
            gap: 8px;
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
            vertical-align: top;
        }

        th {
            background: #f9fafb;
            color: #374151;
        }

        .acoes {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
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
            max-width: 280px;
        }

        @media (max-width: 768px) {
            main {
                padding: 18px;
            }

            .topo {
                flex-direction: column;
                align-items: flex-start;
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
        <span> | Atendimentos</span>
    </div>

    <a href="../logout.php">Sair</a>
</header>

<main>
    <div class="topo">
        <div>
            <h1>Atendimentos</h1>
            <p>Registro e acompanhamento dos atendimentos acadêmicos realizados.</p>
        </div>

        <div>
            <a class="btn btn-secundario" href="../dashboard.php">Voltar</a>
            <a class="btn" href="criar.php">Novo atendimento</a>
        </div>
    </div>

    <section class="filtros">
        <form method="GET" action="">
            <div>
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="">Todos</option>
                    <option value="aberto" <?php echo $status === 'aberto' ? 'selected' : ''; ?>>Aberto</option>
                    <option value="em_andamento" <?php echo $status === 'em_andamento' ? 'selected' : ''; ?>>Em andamento</option>
                    <option value="concluido" <?php echo $status === 'concluido' ? 'selected' : ''; ?>>Concluído</option>
                </select>
            </div>

            <div>
                <label for="pessoa_id">Pessoa</label>
                <select id="pessoa_id" name="pessoa_id">
                    <option value="">Todas</option>
                    <?php foreach ($pessoas as $pessoa): ?>
                        <option value="<?php echo $pessoa['id']; ?>" <?php echo $pessoa_id == $pessoa['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($pessoa['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="tipo_atendimento_id">Tipo</label>
                <select id="tipo_atendimento_id" name="tipo_atendimento_id">
                    <option value="">Todos</option>
                    <?php foreach ($tipos as $tipo): ?>
                        <option value="<?php echo $tipo['id']; ?>" <?php echo $tipo_atendimento_id == $tipo['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($tipo['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="data_inicio">Data inicial</label>
                <input type="date" id="data_inicio" name="data_inicio" value="<?php echo htmlspecialchars($data_inicio); ?>">
            </div>

            <div>
                <label for="data_fim">Data final</label>
                <input type="date" id="data_fim" name="data_fim" value="<?php echo htmlspecialchars($data_fim); ?>">
            </div>

            <div class="botoes-filtro">
                <button class="btn" type="submit">Filtrar</button>
                <a class="btn btn-secundario" href="index.php">Limpar</a>
            </div>
        </form>
    </section>

    <?php if ($mensagem): ?>
        <div class="mensagem">
            <?php echo htmlspecialchars($mensagem); ?>
        </div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Hora</th>
                <th>Pessoa</th>
                <th>Tipo</th>
                <th>Responsável</th>
                <th>Descrição</th>
                <th>Status</th>
                <th>Ações</th>
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
                        <td class="descricao"><?php echo htmlspecialchars($atendimento['descricao']); ?></td>
                        <td>
                            <span class="status-<?php echo htmlspecialchars($atendimento['status']); ?>">
                                <?php echo formatarStatus($atendimento['status']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="acoes">
                                <a class="btn btn-verde" href="visualizar.php?id=<?php echo $atendimento['id']; ?>">Ver</a>
                                <a class="btn" href="editar.php?id=<?php echo $atendimento['id']; ?>">Editar</a>
                                <a class="btn btn-alerta" href="excluir.php?id=<?php echo $atendimento['id']; ?>" onclick="return confirm('Deseja realmente remover este atendimento?')">Excluir</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">Nenhum atendimento encontrado para os filtros selecionados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

</body>
</html>