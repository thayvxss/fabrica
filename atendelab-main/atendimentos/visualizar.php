<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: index.php?mensagem=Atendimento não encontrado');
    exit;
}

$sql = "SELECT 
            atendimentos.*,
            pessoas.nome AS pessoa_nome,
            pessoas.documento AS pessoa_documento,
            pessoas.email AS pessoa_email,
            pessoas.telefone AS pessoa_telefone,
            pessoas.curso AS pessoa_curso,
            pessoas.periodo AS pessoa_periodo,
            tipos_atendimentos.nome AS tipo_nome,
            tipos_atendimentos.descricao AS tipo_descricao,
            usuarios.nome AS usuario_nome,
            usuarios.email AS usuario_email
        FROM atendimentos
        INNER JOIN pessoas ON atendimentos.pessoa_id = pessoas.id
        INNER JOIN tipos_atendimentos ON atendimentos.tipo_atendimento_id = tipos_atendimentos.id
        INNER JOIN usuarios ON atendimentos.usuario_id = usuarios.id
        WHERE atendimentos.id = :id
        LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $id);
$stmt->execute();

$atendimento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$atendimento) {
    header('Location: index.php?mensagem=Atendimento não encontrado');
    exit;
}

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
    <title>Visualizar Atendimento - AtendeLab</title>
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
            max-width: 980px;
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

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .card {
            background: #ffffff;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
        }

        .card-full {
            grid-column: 1 / -1;
        }

        h2 {
            margin-top: 0;
            font-size: 20px;
            color: #111827;
        }

        .linha {
            margin-bottom: 12px;
        }

        .linha strong {
            display: block;
            color: #374151;
            margin-bottom: 4px;
        }

        .linha span, .linha p {
            color: #4b5563;
            margin: 0;
            line-height: 1.5;
        }

        .status {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            font-weight: bold;
            font-size: 13px;
            background: #e0e7ff;
            color: #3730a3;
        }
    </style>
</head>
<body>

<header>
    <div>
        <strong>AtendeLab</strong>
        <span> | Detalhes do atendimento</span>
    </div>

    <a href="../logout.php">Sair</a>
</header>

<main>
    <div class="topo">
        <div>
            <h1>Detalhes do atendimento</h1>
            <p>Visualização completa do atendimento registrado.</p>
        </div>

        <div>
            <a class="btn btn-secundario" href="index.php">Voltar</a>
            <a class="btn" href="editar.php?id=<?php echo $atendimento['id']; ?>">Editar</a>
        </div>
    </div>

    <section class="grid">
        <div class="card">
            <h2>Dados do atendimento</h2>

            <div class="linha">
                <strong>Data</strong>
                <span><?php echo date('d/m/Y', strtotime($atendimento['data_atendimento'])); ?></span>
            </div>

            <div class="linha">
                <strong>Hora</strong>
                <span><?php echo substr($atendimento['hora_atendimento'], 0, 5); ?></span>
            </div>

            <div class="linha">
                <strong>Status</strong>
                <span class="status"><?php echo formatarStatus($atendimento['status']); ?></span>
            </div>

            <div class="linha">
                <strong>Responsável</strong>
                <span><?php echo htmlspecialchars($atendimento['usuario_nome']); ?></span>
            </div>

            <div class="linha">
                <strong>E-mail do responsável</strong>
                <span><?php echo htmlspecialchars($atendimento['usuario_email']); ?></span>
            </div>
        </div>

        <div class="card">
            <h2>Pessoa atendida</h2>

            <div class="linha">
                <strong>Nome</strong>
                <span><?php echo htmlspecialchars($atendimento['pessoa_nome']); ?></span>
            </div>

            <div class="linha">
                <strong>Documento</strong>
                <span><?php echo htmlspecialchars($atendimento['pessoa_documento']); ?></span>
            </div>

            <div class="linha">
                <strong>E-mail</strong>
                <span><?php echo htmlspecialchars($atendimento['pessoa_email']); ?></span>
            </div>

            <div class="linha">
                <strong>Telefone</strong>
                <span><?php echo htmlspecialchars($atendimento['pessoa_telefone']); ?></span>
            </div>

            <div class="linha">
                <strong>Curso</strong>
                <span><?php echo htmlspecialchars($atendimento['pessoa_curso']); ?></span>
            </div>

            <div class="linha">
                <strong>Período</strong>
                <span><?php echo htmlspecialchars($atendimento['pessoa_periodo']); ?></span>
            </div>
        </div>

        <div class="card">
            <h2>Tipo de atendimento</h2>

            <div class="linha">
                <strong>Tipo</strong>
                <span><?php echo htmlspecialchars($atendimento['tipo_nome']); ?></span>
            </div>

            <div class="linha">
                <strong>Descrição do tipo</strong>
                <p><?php echo htmlspecialchars($atendimento['tipo_descricao']); ?></p>
            </div>
        </div>

        <div class="card">
            <h2>Observação final</h2>

            <div class="linha">
                <strong>Observação</strong>
                <p>
                    <?php 
                        echo $atendimento['observacao'] 
                            ? htmlspecialchars($atendimento['observacao']) 
                            : 'Nenhuma observação final registrada.';
                    ?>
                </p>
            </div>
        </div>

        <div class="card card-full">
            <h2>Descrição do atendimento</h2>

            <div class="linha">
                <p><?php echo htmlspecialchars($atendimento['descricao']); ?></p>
            </div>
        </div>
    </section>
</main>

</body>
</html>