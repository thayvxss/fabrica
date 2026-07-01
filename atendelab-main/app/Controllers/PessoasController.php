<?php

class PessoasController
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
                    documento,
                    telefone,
                    email,
                    curso,
                    periodo,
                    observacoes,
                    status,
                    criado_em,
                    atualizado_em
                FROM pessoas
                ORDER BY id DESC";

        $stmt = $this->pdo->query($sql);
        $pessoas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->responderJson($pessoas);
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
                    documento,
                    telefone,
                    email,
                    curso,
                    periodo,
                    observacoes,
                    status,
                    criado_em,
                    atualizado_em
                FROM pessoas
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $pessoa = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pessoa) {
            $this->responderJson(['erro' => 'Pessoa não encontrada.'], 404);
            return;
        }

        $this->responderJson($pessoa);
    }

    public function criar(): void
    {
        $nome = trim($_POST['nome'] ?? '');
        $documento = trim($_POST['documento'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $curso = trim($_POST['curso'] ?? '');
        $periodo = trim($_POST['periodo'] ?? '');
        $observacoes = trim($_POST['observacoes'] ?? '');
        $status = $_POST['status'] ?? 'ativo';

        if ($nome === '' || $documento === '' || $email === '') {
            $this->responderJson(
                ['erro' => 'Nome, documento e e-mail são obrigatórios.'],
                422
            );
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->responderJson(['erro' => 'E-mail inválido.'], 422);
            return;
        }

        if (!in_array($status, ['ativo', 'inativo'], true)) {
            $this->responderJson(['erro' => 'Status inválido.'], 422);
            return;
        }

        try {
            $sql = "INSERT INTO pessoas
                    (
                        nome,
                        documento,
                        telefone,
                        email,
                        curso,
                        periodo,
                        observacoes,
                        status
                    )
                    VALUES
                    (
                        :nome,
                        :documento,
                        :telefone,
                        :email,
                        :curso,
                        :periodo,
                        :observacoes,
                        :status
                    )";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':documento', $documento);
            $stmt->bindValue(':telefone', $telefone);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':curso', $curso);
            $stmt->bindValue(':periodo', $periodo);
            $stmt->bindValue(':observacoes', $observacoes);
            $stmt->bindValue(':status', $status);
            $stmt->execute();

            $this->responderJson([
                'mensagem' => 'Pessoa cadastrada com sucesso.',
                'id' => $this->pdo->lastInsertId()
            ], 201);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $this->responderJson(
                    ['erro' => 'Documento já cadastrado.'],
                    409
                );
                return;
            }

            $this->responderJson(
                ['erro' => 'Não foi possível cadastrar a pessoa.'],
                500
            );
        }
    }

    public function atualizar(): void
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nome = trim($_POST['nome'] ?? '');
        $documento = trim($_POST['documento'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $curso = trim($_POST['curso'] ?? '');
        $periodo = trim($_POST['periodo'] ?? '');
        $observacoes = trim($_POST['observacoes'] ?? '');
        $status = $_POST['status'] ?? 'ativo';

        if (!$id || $nome === '' || $documento === '' || $email === '') {
            $this->responderJson(
                ['erro' => 'ID, nome, documento e e-mail são obrigatórios.'],
                422
            );
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->responderJson(['erro' => 'E-mail inválido.'], 422);
            return;
        }

        if (!in_array($status, ['ativo', 'inativo'], true)) {
            $this->responderJson(['erro' => 'Status inválido.'], 422);
            return;
        }

        try {
            $sql = "UPDATE pessoas
                    SET nome = :nome,
                        documento = :documento,
                        telefone = :telefone,
                        email = :email,
                        curso = :curso,
                        periodo = :periodo,
                        observacoes = :observacoes,
                        status = :status
                    WHERE id = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':documento', $documento);
            $stmt->bindValue(':telefone', $telefone);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':curso', $curso);
            $stmt->bindValue(':periodo', $periodo);
            $stmt->bindValue(':observacoes', $observacoes);
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                $this->responderJson(
                    ['erro' => 'Pessoa não encontrada ou nenhum dado foi alterado.'],
                    404
                );
                return;
            }

            $this->responderJson([
                'mensagem' => 'Pessoa atualizada com sucesso.'
            ]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $this->responderJson(
                    ['erro' => 'Documento já cadastrado.'],
                    409
                );
                return;
            }

            $this->responderJson(
                ['erro' => 'Não foi possível atualizar a pessoa.'],
                500
            );
        }
    }

    public function inativar(): void
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $this->responderJson(['erro' => 'ID inválido.'], 422);
            return;
        }

        $sql = "UPDATE pessoas
                SET status = 'inativo'
                WHERE id = :id
                  AND status <> 'inativo'";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            $this->responderJson(
                ['erro' => 'Pessoa não encontrada ou já estava inativa.'],
                404
            );
            return;
        }

        $this->responderJson([
            'mensagem' => 'Pessoa inativada com sucesso.'
        ]);
    }
}