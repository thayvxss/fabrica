<?php

class AtendimentosController
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
                    a.id,
                    a.pessoa_id,
                    p.nome AS pessoa_nome,
                    a.tipo_atendimento_id,
                    t.nome AS tipo_nome,
                    a.usuario_id,
                    u.nome AS usuario_nome,
                    a.descricao,
                    a.status,
                    a.data_atendimento,
                    COALESCE(a.horario_atendimento, a.hora_atendimento) AS horario_atendimento,
                    COALESCE(a.observacao_final, a.observacao) AS observacao_final,
                    a.criado_em,
                    a.atualizado_em
                FROM atendimentos a
                INNER JOIN pessoas p
                    ON p.id = a.pessoa_id
                INNER JOIN tipos_atendimentos t
                    ON t.id = a.tipo_atendimento_id
                INNER JOIN usuarios u
                    ON u.id = a.usuario_id
                ORDER BY a.id DESC";

        $stmt = $this->pdo->query($sql);
        $atendimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($atendimentos as &$atendimento) {
            $atendimento['protocolo'] =
                'ATD-' . str_pad(
                    (string) $atendimento['id'],
                    4,
                    '0',
                    STR_PAD_LEFT
                );
        }

        unset($atendimento);

        $this->responderJson($atendimentos);
    }

    public function buscarPorId(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $this->responderJson(['erro' => 'ID inválido.'], 422);
            return;
        }

        $sql = "SELECT
                    a.id,
                    a.pessoa_id,
                    p.nome AS pessoa_nome,
                    a.tipo_atendimento_id,
                    t.nome AS tipo_nome,
                    a.usuario_id,
                    u.nome AS usuario_nome,
                    a.descricao,
                    a.status,
                    a.data_atendimento,
                    COALESCE(a.horario_atendimento, a.hora_atendimento) AS horario_atendimento,
                    COALESCE(a.observacao_final, a.observacao) AS observacao_final,
                    a.criado_em,
                    a.atualizado_em
                FROM atendimentos a
                INNER JOIN pessoas p
                    ON p.id = a.pessoa_id
                INNER JOIN tipos_atendimentos t
                    ON t.id = a.tipo_atendimento_id
                INNER JOIN usuarios u
                    ON u.id = a.usuario_id
                WHERE a.id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $atendimento = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$atendimento) {
            $this->responderJson(
                ['erro' => 'Atendimento não encontrado.'],
                404
            );
            return;
        }

        $atendimento['protocolo'] =
            'ATD-' . str_pad(
                (string) $atendimento['id'],
                4,
                '0',
                STR_PAD_LEFT
            );

        $this->responderJson($atendimento);
    }

    public function criar(): void
    {
        $pessoaId = filter_input(
            INPUT_POST,
            'pessoa_id',
            FILTER_VALIDATE_INT
        );

        $tipoId = filter_input(
            INPUT_POST,
            'tipo_atendimento_id',
            FILTER_VALIDATE_INT
        );

        $usuarioId = filter_input(
            INPUT_POST,
            'usuario_id',
            FILTER_VALIDATE_INT
        );

        $descricao = trim($_POST['descricao'] ?? '');
        $dataAtendimento = trim($_POST['data_atendimento'] ?? '');
        $horarioAtendimento = trim(
            $_POST['horario_atendimento']
            ?? $_POST['hora_atendimento']
            ?? ''
        );

        $status = $_POST['status'] ?? 'aberto';

        if (
            !$pessoaId ||
            !$tipoId ||
            !$usuarioId ||
            $descricao === '' ||
            $dataAtendimento === '' ||
            $horarioAtendimento === ''
        ) {
            $this->responderJson(
                ['erro' => 'Preencha todos os campos obrigatórios.'],
                422
            );
            return;
        }

        if (
            !in_array(
                $status,
                ['aberto', 'em_andamento', 'concluido'],
                true
            )
        ) {
            $this->responderJson(
                ['erro' => 'Status inválido.'],
                422
            );
            return;
        }

        $stmt = $this->pdo->prepare(
            "SELECT id
             FROM pessoas
             WHERE id = :id
               AND status = 'ativo'"
        );

        $stmt->bindValue(':id', $pessoaId, PDO::PARAM_INT);
        $stmt->execute();

        if (!$stmt->fetch()) {
            $this->responderJson(
                ['erro' => 'Pessoa inexistente ou inativa.'],
                422
            );
            return;
        }

        $stmt = $this->pdo->prepare(
            "SELECT id
             FROM tipos_atendimentos
             WHERE id = :id
               AND status = 'ativo'"
        );

        $stmt->bindValue(':id', $tipoId, PDO::PARAM_INT);
        $stmt->execute();

        if (!$stmt->fetch()) {
            $this->responderJson(
                ['erro' => 'Tipo inexistente ou inativo.'],
                422
            );
            return;
        }

        $stmt = $this->pdo->prepare(
            "SELECT id
             FROM usuarios
             WHERE id = :id
               AND status = 'ativo'"
        );

        $stmt->bindValue(':id', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();

        if (!$stmt->fetch()) {
            $this->responderJson(
                ['erro' => 'Usuário inexistente ou inativo.'],
                422
            );
            return;
        }

        $sql = "INSERT INTO atendimentos
                (
                    pessoa_id,
                    tipo_atendimento_id,
                    usuario_id,
                    data_atendimento,
                    hora_atendimento,
                    horario_atendimento,
                    descricao,
                    observacao,
                    observacao_final,
                    status
                )
                VALUES
                (
                    :pessoa_id,
                    :tipo_atendimento_id,
                    :usuario_id,
                    :data_atendimento,
                    :hora_atendimento,
                    :horario_atendimento,
                    :descricao,
                    NULL,
                    NULL,
                    :status
                )";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':pessoa_id', $pessoaId, PDO::PARAM_INT);
        $stmt->bindValue(
            ':tipo_atendimento_id',
            $tipoId,
            PDO::PARAM_INT
        );
        $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $stmt->bindValue(':data_atendimento', $dataAtendimento);
        $stmt->bindValue(':hora_atendimento', $horarioAtendimento);
        $stmt->bindValue(
            ':horario_atendimento',
            $horarioAtendimento
        );
        $stmt->bindValue(':descricao', $descricao);
        $stmt->bindValue(':status', $status);
        $stmt->execute();

        $id = $this->pdo->lastInsertId();

        $this->responderJson([
            'mensagem' => 'Atendimento cadastrado com sucesso.',
            'id' => $id,
            'protocolo' => 'ATD-' . str_pad(
                (string) $id,
                4,
                '0',
                STR_PAD_LEFT
            )
        ], 201);
    }

    public function alterarStatus(): void
    {
        $id = filter_input(
            INPUT_POST,
            'id',
            FILTER_VALIDATE_INT
        );

        $status = $_POST['status'] ?? '';
        $observacaoFinal = trim(
            $_POST['observacao_final']
            ?? $_POST['observacao']
            ?? ''
        );

        if (!$id) {
            $this->responderJson(['erro' => 'ID inválido.'], 422);
            return;
        }

        if (
            !in_array(
                $status,
                ['aberto', 'em_andamento', 'concluido'],
                true
            )
        ) {
            $this->responderJson(
                ['erro' => 'Status inválido.'],
                422
            );
            return;
        }

        if ($status === 'concluido' && $observacaoFinal === '') {
            $this->responderJson(
                [
                    'erro' =>
                        'A observação final é obrigatória ao concluir.'
                ],
                422
            );
            return;
        }

        $sql = "UPDATE atendimentos
                SET status = :status,
                    observacao = :observacao,
                    observacao_final = :observacao_final
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(
            ':observacao',
            $observacaoFinal !== '' ? $observacaoFinal : null
        );
        $stmt->bindValue(
            ':observacao_final',
            $observacaoFinal !== '' ? $observacaoFinal : null
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            $this->responderJson(
                [
                    'erro' =>
                        'Atendimento não encontrado ou nenhum dado foi alterado.'
                ],
                404
            );
            return;
        }

        $this->responderJson([
            'mensagem' => 'Status do atendimento atualizado com sucesso.'
        ]);
    }
}