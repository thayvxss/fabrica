<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

$erro = '';

$pessoas = $pdo->query("SELECT id, nome FROM pessoas WHERE status = 'ativo' ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);
$tipos = $pdo->query("SELECT id, nome FROM tipos_atendimentos WHERE status = 'ativo' ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pessoa_id = $_POST['pessoa_id'] ?? '';
    $tipo_atendimento_id = $_POST['tipo_atendimento_id'] ?? '';
    $data_atendimento = $_POST['data_atendimento'] ?? '';
    $hora_atendimento = $_POST['hora_atendimento'] ?? '';
    $descricao = trim($_POST['descricao'] ?? '');
    $status = $_POST['status'] ?? 'aberto';
    $usuario_id = $_SESSION['usuario_id'];

    if ($pessoa_id === '' || $tipo_atendimento_id === '' || $data_atendimento === '' || $hora_atendimento === '' || $descricao === '') {
        $erro = 'Preencha todos os campos obrigatórios.';
    } else {
        $sql = "INSERT INTO atendimentos 
                (pessoa_id, tipo_atendimento_id, usuario_id, data_atendimento, hora_atendimento, descricao, status)
                VALUES
                (:pessoa_id, :tipo_atendimento_id, :usuario_id, :data_atendimento, :hora_atendimento, :descricao, :status)";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':pessoa_id', $pessoa_id);
        $stmt->bindParam(':tipo_atendimento_id', $tipo_atendimento_id);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':data_atendimento', $data_atendimento);
        $stmt->bindParam(':hora_atendimento', $hora_atendimento);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':status', $status);

        $stmt->execute();

        header('Location: index.php?mensagem=Atendimento cadastrado com sucesso');
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Novo Atendimento - AtendeLab</title>
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
            max-width: 820px;
        }

        .card {
            background: #ffffff;
            padding: 28px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
        }

        label {
            display: block;
            margin-top: 14px;
            margin-bottom: 6px;
            font-weight: bold;
            color: #374151;
        }

        input, select, textarea {
            width: 100%;
            padding: 11px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        textarea {
            min-height: 130px;
            resize: vertical;
        }

        .linha {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .botoes {
            margin-top: 22px;
            display: flex;
            gap: 10px;
        }

        .btn {
            background: #2563eb;
            color: #ffffff;
            padding: 11px 16px;
            border-radius: 8px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-secundario {
            background: #6b7280;
        }

        .erro {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 18px;
        }
    </style>
</head>
<body>

<header>
    <div>
        <strong>AtendeLab</strong>
        <span> | Novo atendimento</span>
    </div>

    <a href="../logout.php">Sair</a>
</header>

<main>
    <h1>Novo atendimento</h1>
    <p>Registre um atendimento vinculando pessoa, tipo de atendimento e responsável.</p>

    <div class="card">
        <?php if ($erro): ?>
            <div class="erro">
                <?php echo htmlspecialchars($erro); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="pessoa_id">Pessoa atendida *</label>
            <select id="pessoa_id" name="pessoa_id" required>
                <option value="">Selecione uma pessoa</option>
                <?php foreach ($pessoas as $pessoa): ?>
                    <option value="<?php echo $pessoa['id']; ?>">
                        <?php echo htmlspecialchars($pessoa['nome']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="tipo_atendimento_id">Tipo de atendimento *</label>
            <select id="tipo_atendimento_id" name="tipo_atendimento_id" required>
                <option value="">Selecione um tipo</option>
                <?php foreach ($tipos as $tipo): ?>
                    <option value="<?php echo $tipo['id']; ?>">
                        <?php echo htmlspecialchars($tipo['nome']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div class="linha">
                <div>
                    <label for="data_atendimento">Data *</label>
                    <input type="date" id="data_atendimento" name="data_atendimento" value="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div>
                    <label for="hora_atendimento">Hora *</label>
                    <input type="time" id="hora_atendimento" name="hora_atendimento" value="<?php echo date('H:i'); ?>" required>
                </div>
            </div>

            <label for="status">Status *</label>
            <select id="status" name="status" required>
                <option value="aberto">Aberto</option>
                <option value="em_andamento">Em andamento</option>
                <option value="concluido">Concluído</option>
            </select>

            <label for="descricao">Descrição do atendimento *</label>
            <textarea id="descricao" name="descricao" required></textarea>

            <div class="botoes">
                <button class="btn" type="submit">Salvar atendimento</button>
                <a class="btn btn-secundario" href="index.php">Cancelar</a>
            </div>
        </form>
    </div>
</main>

</body>
</html>