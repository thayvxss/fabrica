<?php

class TiposAtendimentosController
{
    private PDO $pdo;

    public function __construct()
    {
        require __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    private function responderJson(array $dados, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode(
            $dados,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );
    }

    public function listar(): void
    {
        $sql = "SELECT
                    id,
                    nome,
                    descricao,
                    status,
                    criado_em,
                    atualizado_em
                FROM tipos_atendimentos
                ORDER BY id DESC";

        $stmt = $this->pdo->query($sql);
        $tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->responderJson($tipos);
    }

    public function buscarPorId(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $this->responderJson(['erro' => 'ID inválido.'], 422);
            return;
        }

        $sql = "SELECT
                    id,
                    nome,
                    descricao,
                    status,
                    criado_em,
                    atualizado_em
                FROM tipos_atendimentos
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $tipo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tipo) {
            $this->responderJson(
                ['erro' => 'Tipo de atendimento não encontrado.'],
                404
            );
            return;
        }

        $this->responderJson($tipo);
    }

    public function criar(): void
    {
        $nome = trim($_POST['nome'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $status = $_POST['status'] ?? 'ativo';

        if ($nome === '') {
            $this->responderJson(
                ['erro' => 'O nome é obrigatório.'],
                422
            );
            return;
        }

        if (!in_array($status, ['ativo', 'inativo'], true)) {
            $this->responderJson(
                ['erro' => 'Status inválido.'],
                422
            );
            return;
        }

        try {
            $sql = "INSERT INTO tipos_atendimentos
                    (nome, descricao, status)
                    VALUES
                    (:nome, :descricao, :status)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':descricao', $descricao);
            $stmt->bindValue(':status', $status);
            $stmt->execute();

            $this->responderJson([
                'mensagem' => 'Tipo de atendimento cadastrado com sucesso.',
                'id' => $this->pdo->lastInsertId()
            ], 201);
        } catch (PDOException $e) {
            $this->responderJson(
                ['erro' => 'Não foi possível cadastrar o tipo de atendimento.'],
                500
            );
        }
    }

    public function atualizar(): void
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nome = trim($_POST['nome'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $status = $_POST['status'] ?? 'ativo';

        if (!$id || $nome === '') {
            $this->responderJson(
                ['erro' => 'ID e nome são obrigatórios.'],
                422
            );
            return;
        }

        if (!in_array($status, ['ativo', 'inativo'], true)) {
            $this->responderJson(
                ['erro' => 'Status inválido.'],
                422
            );
            return;
        }

        $sql = "UPDATE tipos_atendimentos
                SET nome = :nome,
                    descricao = :descricao,
                    status = :status
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':descricao', $descricao);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            $this->responderJson(
                ['erro' => 'Tipo não encontrado ou nenhum dado foi alterado.'],
                404
            );
            return;
        }

        $this->responderJson([
            'mensagem' => 'Tipo de atendimento atualizado com sucesso.'
        ]);
    }

    public function inativar(): void
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $this->responderJson(['erro' => 'ID inválido.'], 422);
            return;
        }

        $sql = "UPDATE tipos_atendimentos
                SET status = 'inativo'
                WHERE id = :id
                  AND status <> 'inativo'";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            $this->responderJson(
                ['erro' => 'Tipo não encontrado ou já estava inativo.'],
                404
            );
            return;
        }

        $this->responderJson([
            'mensagem' => 'Tipo de atendimento inativado com sucesso.'
        ]);
    }
}