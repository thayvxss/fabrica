<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: index.php?mensagem=Pessoa não encontrada');
    exit;
}

$sql = "UPDATE pessoas SET status = 'inativo' WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $id);
$stmt->execute();

header('Location: index.php?mensagem=Pessoa inativada com sucesso');
exit;