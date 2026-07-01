<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: index.php?mensagem=Atendimento não encontrado');
    exit;
}

$sql = "DELETE FROM atendimentos WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $id);
$stmt->execute();

header('Location: index.php?mensagem=Atendimento excluído com sucesso');
exit;