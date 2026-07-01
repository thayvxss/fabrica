<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verifica se existe um usuário autenticado.
 *
 * O sistema aceita tanto a estrutura MVC da Aula 03
 * quanto as variáveis usadas pelo frontend atual.
 */
function usuarioAutenticado(): bool
{
    if (
        isset($_SESSION['usuario']) &&
        is_array($_SESSION['usuario']) &&
        isset($_SESSION['usuario']['id'])
    ) {
        return true;
    }

    return isset($_SESSION['usuario_id']);
}

/**
 * Bloqueia páginas e rotas internas.
 */
function exigirAutenticacao(): void
{
    if (!usuarioAutenticado()) {
        $_SESSION['erro_login'] = 'Faça login para acessar a área restrita.';

        header(
            'Location: /atendelab/public/?controller=auth&action=login'
        );

        exit;
    }
}

/**
 * Retorna os dados do usuário autenticado.
 */
function usuarioAtual(): ?array
{
    if (
        isset($_SESSION['usuario']) &&
        is_array($_SESSION['usuario'])
    ) {
        return $_SESSION['usuario'];
    }

    if (!isset($_SESSION['usuario_id'])) {
        return null;
    }

    return [
        'id' => $_SESSION['usuario_id'],
        'nome' => $_SESSION['usuario_nome'] ?? '',
        'email' => $_SESSION['usuario_email'] ?? '',
        'perfil' => $_SESSION['usuario_perfil'] ?? ''
    ];
}