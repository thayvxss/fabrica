<?php

class UsuariosController
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
        $sql = "SELECT id, nome, email, perfil, status, criado_em
                FROM usuarios
                ORDER BY id DESC";

        $stmt = $this->pdo->query($sql);
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->responderJson($usuarios);
    }

    public function buscarPorId(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $this->responderJson(['erro' => 'ID inválido.'], 400);
            return;
        }

        $sql = "SELECT id, nome, email, perfil, status, criado_em
                FROM usuarios
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            $this->responderJson(['erro' => 'Usuário não encontrado.'], 404);
            return;
        }

        $this->responderJson($usuario);
    }

    public function criar(): void
    {
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $perfil = $_POST['perfil'] ?? 'atendente';
        $status = $_POST['status'] ?? 'ativo';

        if ($nome === '' || $email === '' || $senha === '') {
            $this->responderJson(
                ['erro' => 'Nome, e-mail e senha são obrigatórios.'],
                400
            );
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->responderJson(['erro' => 'E-mail inválido.'], 400);
            return;
        }

        if (!in_array($perfil, ['admin', 'aluno', 'atendente'], true)) {
            $this->responderJson(['erro' => 'Perfil inválido.'], 400);
            return;
        }

        if (!in_array($status, ['ativo', 'inativo'], true)) {
            $this->responderJson(['erro' => 'Status inválido.'], 400);
            return;
        }

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        try {
            $sql = "INSERT INTO usuarios
                    (nome, email, senha, perfil, status)
                    VALUES
                    (:nome, :email, :senha, :perfil, :status)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':senha', $senhaHash);
            $stmt->bindValue(':perfil', $perfil);
            $stmt->bindValue(':status', $status);
            $stmt->execute();

            $this->responderJson([
                'mensagem' => 'Usuário cadastrado com sucesso.',
                'id' => $this->pdo->lastInsertId()
            ], 201);
        } catch (PDOException $e) {
            $this->responderJson([
                'erro' => 'Não foi possível cadastrar o usuário. Verifique se o e-mail já existe.'
            ], 500);
        }
    }

    public function atualizar(): void
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $perfil = $_POST['perfil'] ?? 'atendente';
        $status = $_POST['status'] ?? 'ativo';

        if (!$id || $nome === '' || $email === '') {
            $this->responderJson(
                ['erro' => 'ID, nome e e-mail são obrigatórios.'],
                400
            );
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->responderJson(['erro' => 'E-mail inválido.'], 400);
            return;
        }

        if (!in_array($perfil, ['admin', 'aluno', 'atendente'], true)) {
            $this->responderJson(['erro' => 'Perfil inválido.'], 400);
            return;
        }

        if (!in_array($status, ['ativo', 'inativo'], true)) {
            $this->responderJson(['erro' => 'Status inválido.'], 400);
            return;
        }

        try {
            $sql = "UPDATE usuarios
                    SET nome = :nome,
                        email = :email,
                        perfil = :perfil,
                        status = :status
                    WHERE id = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':perfil', $perfil);
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                $this->responderJson([
                    'erro' => 'Usuário não encontrado ou nenhum dado foi alterado.'
                ], 404);
                return;
            }

            $this->responderJson([
                'mensagem' => 'Usuário atualizado com sucesso.'
            ]);
        } catch (PDOException $e) {
            $this->responderJson([
                'erro' => 'Não foi possível atualizar o usuário.'
            ], 500);
        }
    }

    public function excluir(): void
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $this->responderJson(['erro' => 'ID inválido.'], 400);
            return;
        }

        /*
         * Para evitar problemas com atendimentos relacionados,
         * realizamos exclusão lógica em vez de DELETE físico.
         */
        $sql = "UPDATE usuarios
                SET status = 'inativo'
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            $this->responderJson([
                'erro' => 'Usuário não encontrado ou já estava inativo.'
            ], 404);
            return;
        }

        $this->responderJson([
            'mensagem' => 'Usuário inativado com sucesso.'
        ]);
    }
}