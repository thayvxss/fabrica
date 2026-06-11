<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

$id = $_GET['id'] ?? null;
$erro = '';

if (!$id) {
    header('Location: index.php?mensagem=Pessoa não encontrada');
    exit;
}

$sql = "SELECT * FROM pessoas WHERE id = :id LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $id);
$stmt->execute();
$pessoa = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pessoa) {
    header('Location: index.php?mensagem=Pessoa não encontrada');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $documento = trim($_POST['documento'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $curso = trim($_POST['curso'] ?? '');
    $periodo = trim($_POST['periodo'] ?? '');
    $status = $_POST['status'] ?? 'ativo';

    if ($nome === '') {
        $erro = 'O nome é obrigatório.';
    } else {
        $sql = "UPDATE pessoas 
                SET nome = :nome,
                    documento = :documento,
                    email = :email,
                    telefone = :telefone,
                    curso = :curso,
                    periodo = :periodo,
                    status = :status
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':documento', $documento);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':curso', $curso);
        $stmt->bindParam(':periodo', $periodo);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);

        $stmt->execute();

        header('Location: index.php?mensagem=Pessoa atualizada com sucesso');
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Pessoa - AtendeLab</title>
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

        input, select {
            width: 100%;
            padding: 11px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            box-sizing: border-box;
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
        <span> | Editar pessoa</span>
    </div>

    <a href="../logout.php">Sair</a>
</header>

<main>
    <h1>Editar pessoa atendida</h1>
    <p>Atualize os dados da pessoa cadastrada.</p>

    <div class="card">
        <?php if ($erro): ?>
            <div class="erro">
                <?php echo htmlspecialchars($erro); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="nome">Nome *</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($pessoa['nome']); ?>" required>

            <label for="documento">Documento</label>
            <input type="text" id="documento" name="documento" value="<?php echo htmlspecialchars($pessoa['documento']); ?>">

            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($pessoa['email']); ?>">

            <label for="telefone">Telefone</label>
            <input type="text" id="telefone" name="telefone" value="<?php echo htmlspecialchars($pessoa['telefone']); ?>">

            <label for="curso">Curso</label>
            <input type="text" id="curso" name="curso" value="<?php echo htmlspecialchars($pessoa['curso']); ?>">

            <label for="periodo">Período</label>
            <input type="text" id="periodo" name="periodo" value="<?php echo htmlspecialchars($pessoa['periodo']); ?>">

            <label for="status">Status</label>
            <select id="status" name="status">
                <option value="ativo" <?php echo $pessoa['status'] === 'ativo' ? 'selected' : ''; ?>>Ativo</option>
                <option value="inativo" <?php echo $pessoa['status'] === 'inativo' ? 'selected' : ''; ?>>Inativo</option>
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