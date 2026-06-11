<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

$id = $_GET['id'] ?? null;
$erro = '';

if (!$id) {
    header('Location: index.php?mensagem=Tipo de atendimento não encontrado');
    exit;
}

$sql = "SELECT * FROM tipos_atendimentos WHERE id = :id LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $id);
$stmt->execute();
$tipo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tipo) {
    header('Location: index.php?mensagem=Tipo de atendimento não encontrado');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $status = $_POST['status'] ?? 'ativo';

    if ($nome === '') {
        $erro = 'O nome do tipo de atendimento é obrigatório.';
    } else {
        $sql = "UPDATE tipos_atendimentos 
                SET nome = :nome,
                    descricao = :descricao,
                    status = :status
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        header('Location: index.php?mensagem=Tipo de atendimento atualizado com sucesso');
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Tipo de Atendimento - AtendeLab</title>
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
            max-width: 760px;
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

        input, textarea, select {
            width: 100%;
            padding: 11px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        textarea {
            min-height: 120px;
            resize: vertical;
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
        <span> | Editar tipo de atendimento</span>
    </div>

    <a href="../logout.php">Sair</a>
</header>

<main>
    <h1>Editar tipo de atendimento</h1>
    <p>Atualize os dados da categoria de atendimento.</p>

    <div class="card">
        <?php if ($erro): ?>
            <div class="erro">
                <?php echo htmlspecialchars($erro); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="nome">Nome *</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($tipo['nome']); ?>" required>

            <label for="descricao">Descrição</label>
            <textarea id="descricao" name="descricao"><?php echo htmlspecialchars($tipo['descricao']); ?></textarea>

            <label for="status">Status</label>
            <select id="status" name="status">
                <option value="ativo" <?php echo $tipo['status'] === 'ativo' ? 'selected' : ''; ?>>Ativo</option>
                <option value="inativo" <?php echo $tipo['status'] === 'inativo' ? 'selected' : ''; ?>>Inativo</option>
            </select>

            <div class="botoes">
                <button class="btn" type="submit">Salvar alterações</button>
                <a class="btn btn-secundario" href="index.php">Cancelar</a>
            </div>
        </form>
    </div>
</main>

</body>
</html>