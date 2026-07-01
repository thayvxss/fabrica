-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 25, 2026 at 03:50 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `atendelab`
--

-- --------------------------------------------------------

--
-- Table structure for table `atendimentos`
--

CREATE TABLE `atendimentos` (
  `id` int(11) NOT NULL,
  `pessoa_id` int(11) NOT NULL,
  `tipo_atendimento_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `data_atendimento` date NOT NULL,
  `hora_atendimento` time NOT NULL,
  `descricao` text NOT NULL,
  `observacao` text DEFAULT NULL,
  `status` enum('aberto','em_andamento','concluido') DEFAULT 'aberto',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `horario_atendimento` time DEFAULT NULL,
  `observacao_final` text DEFAULT NULL,
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `atendimentos`
--

INSERT INTO `atendimentos` (`id`, `pessoa_id`, `tipo_atendimento_id`, `usuario_id`, `data_atendimento`, `hora_atendimento`, `descricao`, `observacao`, `status`, `criado_em`, `horario_atendimento`, `observacao_final`, `atualizado_em`) VALUES
(2, 3, 2, 1, '2026-05-30', '19:00:00', 'bugou tudo', 'oi', 'aberto', '2026-05-30 22:02:08', '19:00:00', 'oi', '2026-06-25 00:36:11'),
(3, 4, 5, 1, '2026-06-24', '14:30:00', 'teste', 'concluido', 'concluido', '2026-06-25 01:39:42', '14:30:00', 'concluido', '2026-06-25 01:47:37');

-- --------------------------------------------------------

--
-- Table structure for table `pessoas`
--

CREATE TABLE `pessoas` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `documento` varchar(30) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `curso` varchar(120) DEFAULT NULL,
  `periodo` varchar(20) DEFAULT NULL,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacoes` text DEFAULT NULL,
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pessoas`
--

INSERT INTO `pessoas` (`id`, `nome`, `documento`, `email`, `telefone`, `curso`, `periodo`, `status`, `criado_em`, `observacoes`, `atualizado_em`) VALUES
(1, 'João da Silva', '11111111111', 'joao@email.com', '47999990000', 'Engenharia de Software', '5º semestre', 'inativo', '2026-05-30 21:03:28', NULL, '2026-06-25 00:35:40'),
(2, 'Maria Oliveira', '22222222222', 'maria@email.com', '47988880000', 'Sistemas de Informação', '3º semestre', 'inativo', '2026-05-30 21:03:28', NULL, '2026-06-25 00:35:40'),
(3, 'Diogo Jacob', '33333333333', 'diogo.jacob@gmail.com', '47999999999', 'Sistemas de Informação', '5º Semestre', 'ativo', '2026-05-30 21:41:22', NULL, '2026-06-25 00:35:40'),
(4, 'diogo', '11122233344', 'diogo@gmail.com', '47911112222', 'sistemas', '5º', 'ativo', '2026-06-25 01:17:15', 'teste', '2026-06-25 01:17:15');

-- --------------------------------------------------------

--
-- Table structure for table `tipos_atendimentos`
--

CREATE TABLE `tipos_atendimentos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tipos_atendimentos`
--

INSERT INTO `tipos_atendimentos` (`id`, `nome`, `descricao`, `status`, `criado_em`, `atualizado_em`) VALUES
(1, 'Monitoria', 'Atendimento para dúvidas de conteúdo acadêmico.', 'ativo', '2026-05-30 21:04:08', '2026-06-25 00:35:40'),
(2, 'Suporte técnico', 'Atendimento relacionado a problemas em laboratório ou sistemas.', 'ativo', '2026-05-30 21:04:08', '2026-06-25 00:35:40'),
(3, 'Orientação acadêmica', 'Atendimento para orientação de atividades e projetos.', 'inativo', '2026-05-30 21:04:08', '2026-06-25 00:35:40'),
(4, 'Admistrativo', 'Atendimento relacionado a dúvidas administrativas, documentos e solicitações acadêmicas.', 'ativo', '2026-05-30 21:51:32', '2026-06-25 00:35:40'),
(5, 'orientação', 'teste', 'ativo', '2026-06-25 01:30:01', '2026-06-25 01:30:01');

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `perfil` enum('admin','aluno','atendente') DEFAULT 'atendente',
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `perfil`, `status`, `criado_em`, `atualizado_em`) VALUES
(1, 'Administrador', 'admin@atendelab.com', '$2y$12$hcg79UkymDoNBZmcb72CWO015qr5BfMtUqQ/QkOWwL98a6OK6LWt2', 'admin', 'ativo', '2026-05-29 00:54:10', '2026-06-25 00:35:40'),
(2, 'jalin', 'jalin@gmail.com', '$2y$10$akS7bj/TMo1ZzUIKiB6cCuMjDTFF10IE.Ls.IJiwAhgMsXh7.BLla', 'atendente', 'ativo', '2026-06-16 00:50:07', '2026-06-25 00:35:40');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `atendimentos`
--
ALTER TABLE `atendimentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_atendimentos_pessoas` (`pessoa_id`),
  ADD KEY `fk_atendimentos_tipos` (`tipo_atendimento_id`),
  ADD KEY `fk_atendimentos_usuarios` (`usuario_id`);

--
-- Indexes for table `pessoas`
--
ALTER TABLE `pessoas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `documento` (`documento`);

--
-- Indexes for table `tipos_atendimentos`
--
ALTER TABLE `tipos_atendimentos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `atendimentos`
--
ALTER TABLE `atendimentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pessoas`
--
ALTER TABLE `pessoas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tipos_atendimentos`
--
ALTER TABLE `tipos_atendimentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `atendimentos`
--
ALTER TABLE `atendimentos`
  ADD CONSTRAINT `fk_atendimentos_pessoas` FOREIGN KEY (`pessoa_id`) REFERENCES `pessoas` (`id`),
  ADD CONSTRAINT `fk_atendimentos_tipos` FOREIGN KEY (`tipo_atendimento_id`) REFERENCES `tipos_atendimentos` (`id`),
  ADD CONSTRAINT `fk_atendimentos_usuarios` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
