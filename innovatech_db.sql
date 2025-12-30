-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 30/12/2025 às 04:00
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `innovatech_db`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `prioridade_usuario`
--

CREATE TABLE `prioridade_usuario` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `projeto_id` int(11) NOT NULL,
  `prioridade` enum('Baixa','Média','Alta') NOT NULL DEFAULT 'Média'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `projetos`
--

CREATE TABLE `projetos` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `descricao` text NOT NULL,
  `categoria` enum('TCC','Pesquisa','Extensão','Pessoal','Outro') DEFAULT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date NOT NULL,
  `prioridade` enum('Baixa','Média','Alta') DEFAULT 'Média',
  `status` enum('Planejamento','Andamento','Pendente','Concluído') DEFAULT 'Planejamento',
  `criador_id` int(11) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `arquivado` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `projetos`
--

INSERT INTO `projetos` (`id`, `nome`, `descricao`, `categoria`, `data_inicio`, `data_fim`, `prioridade`, `status`, `criador_id`, `criado_em`, `arquivado`) VALUES
(1, 'teste', 'test1', 'TCC', '2025-12-26', '2025-12-27', 'Média', 'Planejamento', 1, '2025-12-27 00:56:34', 1),
(2, 'teste3', 'tt', 'Outro', '2025-12-25', '2025-12-30', NULL, 'Concluído', 1, '2025-12-27 02:59:58', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `projeto_aluno`
--

CREATE TABLE `projeto_aluno` (
  `id` int(11) NOT NULL,
  `projeto_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `projeto_aluno`
--

INSERT INTO `projeto_aluno` (`id`, `projeto_id`, `usuario_id`) VALUES
(6, 2, 128),
(7, 2, 111),
(8, 1, 128),
(9, 1, 111),
(10, 1, 131);

-- --------------------------------------------------------

--
-- Estrutura para tabela `projeto_orientador`
--

CREATE TABLE `projeto_orientador` (
  `id` int(11) NOT NULL,
  `projeto_id` int(11) NOT NULL,
  `professor_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `projeto_orientador`
--

INSERT INTO `projeto_orientador` (`id`, `projeto_id`, `professor_id`) VALUES
(6, 2, 199),
(7, 2, 197),
(8, 1, 196),
(9, 1, 199);

-- --------------------------------------------------------

--
-- Estrutura para tabela `projeto_usuario`
--

CREATE TABLE `projeto_usuario` (
  `id` int(11) NOT NULL,
  `projeto_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `papel` enum('Criador','Aluno','Orientador') NOT NULL,
  `prioridade` enum('Baixa','Média','Alta') DEFAULT NULL,
  `arquivado` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `projeto_usuario`
--

INSERT INTO `projeto_usuario` (`id`, `projeto_id`, `usuario_id`, `papel`, `prioridade`, `arquivado`) VALUES
(1, 2, 1, 'Aluno', 'Média', 0),
(12, 1, 1, 'Criador', 'Baixa', 0),
(23, 2, 1, 'Aluno', 'Média', 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tarefas`
--

CREATE TABLE `tarefas` (
  `id` int(11) NOT NULL,
  `projeto_id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `descricao` text DEFAULT NULL,
  `responsavel_id` int(11) DEFAULT NULL,
  `prioridade` enum('Baixa','Média','Alta') DEFAULT 'Média',
  `status` enum('Planejamento','Em Andamento','Concluído') DEFAULT 'Planejamento',
  `data_inicio` date DEFAULT NULL,
  `data_fim` date DEFAULT NULL,
  `arquivado` tinyint(1) DEFAULT 0,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tarefas`
--

INSERT INTO `tarefas` (`id`, `projeto_id`, `nome`, `descricao`, `responsavel_id`, `prioridade`, `status`, `data_inicio`, `data_fim`, `arquivado`, `criado_em`) VALUES
(1, 1, 'aa', 'aaa', 131, 'Média', 'Planejamento', '2025-12-29', '2025-12-30', 0, '2025-12-30 02:58:17'),
(2, 2, 'aa2', 'aaa2', 111, 'Média', 'Planejamento', '2025-12-29', '2025-12-30', 0, '2025-12-30 02:59:38');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tarefa_usuario`
--

CREATE TABLE `tarefa_usuario` (
  `id` int(11) NOT NULL,
  `tarefa_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `prioridade` enum('Baixa','Média','Alta') DEFAULT 'Média',
  `arquivado` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo_solicitado` enum('Admin','Aluno','Professor','Coordenador') NOT NULL,
  `aprovado` tinyint(1) DEFAULT 0,
  `ativo` tinyint(1) DEFAULT 1,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `tipo_solicitado`, `aprovado`, `ativo`, `criado_em`) VALUES
(1, 'admin', 'admin@admin.com', '$2y$10$SvBUlM/rgEEatOD169zppuvPG3GBXF1DdcZaeLkozA3R.AK9Z5J4S', 'Admin', 1, 1, '2025-12-24 02:48:51'),
(111, 'Ana Clara Souza', 'ana.clara.souza@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:17'),
(112, 'Ana Paula Ribeiro', 'ana.paula.ribeiro@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:17'),
(113, 'Beatriz Martins', 'beatriz.martins@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:17'),
(114, 'Bruna Azevedo', 'bruna.azevedo@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:17'),
(115, 'Camila Nogueira', 'camila.nogueira@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:18'),
(116, 'Carolina Pacheco', 'carolina.pacheco@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:18'),
(117, 'Daniela Freitas', 'daniela.freitas@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:18'),
(118, 'Eduarda Lima', 'eduarda.lima@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:18'),
(119, 'Fernanda Rocha', 'fernanda.rocha@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:18'),
(120, 'Gabriela Torres', 'gabriela.torres@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:18'),
(121, 'Helena Barros', 'helena.barros@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:18'),
(122, 'Isabela Farias', 'isabela.farias@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:18'),
(123, 'Juliana Batista', 'juliana.batista@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:18'),
(124, 'Larissa Teixeira', 'larissa.teixeira@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:18'),
(125, 'Letícia Guedes', 'letícia.guedes@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:19'),
(126, 'Luana Moreira', 'luana.moreira@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:19'),
(127, 'Mariana Costa', 'mariana.costa@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:19'),
(128, 'Natalia Rangel', 'natalia.rangel@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:19'),
(129, 'Patricia Lemos', 'patricia.lemos@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:19'),
(130, 'Renata Pinho', 'renata.pinho@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:19'),
(131, 'Aline Macedo', 'aline.macedo@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:19'),
(132, 'Bianca Peixoto', 'bianca.peixoto@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:19'),
(133, 'Clara Menezes', 'clara.menezes@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:19'),
(134, 'Debora Siqueira', 'debora.siqueira@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:19'),
(135, 'Elaine Cunha', 'elaine.cunha@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:19'),
(136, 'Fabiana Paes', 'fabiana.paes@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:19'),
(137, 'Flavia Antunes', 'flavia.antunes@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:20'),
(138, 'Giovana Coelho', 'giovana.coelho@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:20'),
(139, 'Heloisa Neves', 'heloisa.neves@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:20'),
(140, 'Ingrid Lopes', 'ingrid.lopes@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:20'),
(141, 'Jéssica Almeida', 'jéssica.almeida@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:20'),
(142, 'Karen Tavares', 'karen.tavares@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:20'),
(143, 'Livia Rezende', 'livia.rezende@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:20'),
(144, 'Marcela Fonseca', 'marcela.fonseca@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:20'),
(145, 'Monique Silveira', 'monique.silveira@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:20'),
(146, 'Nathalia Prado', 'nathalia.prado@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:20'),
(147, 'Paula Viana', 'paula.viana@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:20'),
(148, 'Raquel Falcão', 'raquel.falcão@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:20'),
(149, 'Sabrina Leite', 'sabrina.leite@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:20'),
(150, 'Tatiane Borges', 'tatiane.borges@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:20'),
(151, 'Vanessa Moura', 'vanessa.moura@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:21'),
(152, 'Vitoria Assis', 'vitoria.assis@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:21'),
(153, 'Yasmin Cardoso', 'yasmin.cardoso@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:21'),
(154, 'Amanda Queiroz', 'amanda.queiroz@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:21'),
(155, 'Cintia Abreu', 'cintia.abreu@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:21'),
(156, 'Denise Rios', 'denise.rios@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:21'),
(157, 'Elisa Ventura', 'elisa.ventura@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:21'),
(158, 'Francine Portela', 'francine.portela@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:21'),
(159, 'Gisele Brandão', 'gisele.brandão@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:21'),
(160, 'Ivana Araujo', 'ivana.araujo@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:21'),
(161, 'Joana Correia', 'joana.correia@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:21'),
(162, 'Kelly Paredes', 'kelly.paredes@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:21'),
(163, 'Luciana Paiva', 'luciana.paiva@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:21'),
(164, 'Milena Duarte', 'milena.duarte@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:21'),
(165, 'Nicole Brito', 'nicole.brito@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:21'),
(166, 'Olivia Sampaio', 'olivia.sampaio@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:22'),
(167, 'Priscila Mendonça', 'priscila.mendonça@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:22'),
(168, 'Rafaela Cunha', 'rafaela.cunha@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:22'),
(169, 'Samara Bastos', 'samara.bastos@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:22'),
(170, 'Tais Montenegro', 'tais.montenegro@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:22'),
(171, 'Ursula Neri', 'ursula.neri@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:22'),
(172, 'Valeria Drumond', 'valeria.drumond@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:22'),
(173, 'Wendy Xavier', 'wendy.xavier@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:22'),
(174, 'Ximena Ortiz', 'ximena.ortiz@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:22'),
(175, 'Afonso Pimentel', 'afonso.pimentel@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:22'),
(176, 'Bruno Cavalcante', 'bruno.cavalcante@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:22'),
(177, 'Carlos Henrique Lopes', 'carlos.henrique.lopes@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:22'),
(178, 'Diego Amaral', 'diego.amaral@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:22'),
(179, 'Eduardo Magalhães', 'eduardo.magalhães@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:22'),
(180, 'Felipe Moretti', 'felipe.moretti@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:23'),
(181, 'Gustavo Pinheiro', 'gustavo.pinheiro@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:23'),
(182, 'Henrique Salgado', 'henrique.salgado@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:23'),
(183, 'Igor Nascimento', 'igor.nascimento@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:23'),
(184, 'João Pedro Matos', 'joão.pedro.matos@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:23'),
(185, 'Leonardo Rangel', 'leonardo.rangel@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:23'),
(186, 'Lucas Ferraz', 'lucas.ferraz@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:23'),
(187, 'Matheus Diniz', 'matheus.diniz@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:23'),
(188, 'Nathan Pires', 'nathan.pires@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:23'),
(189, 'Otavio Barreto', 'otavio.barreto@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:23'),
(190, 'Pedro Afonso Luz', 'pedro.afonso.luz@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:23'),
(191, 'Rafael Seabra', 'rafael.seabra@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:23'),
(192, 'Rodrigo Valente', 'rodrigo.valente@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:24'),
(193, 'Thiago Bittencourt', 'thiago.bittencourt@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:24'),
(194, 'Vinicius Goulart', 'vinicius.goulart@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:24'),
(195, 'William Figueiredo', 'william.figueiredo@fatec.com.br', '$2y$10$3PDJ.4fS1OFta4hhErcdqeOnpdUGTBriOO3KApTNOT4S0veM8gKsy', 'Aluno', 1, 1, '2025-12-24 04:29:24'),
(196, 'Alexandre Marcelino da Silva', 'alexandre.silva102@fatec.sp.gov.br', '$2y$10$4nqz9Yd3Jf70SxpVqhS/iu/4iaY4qKq82de3fMK45e4XMiiiHljzS', 'Professor', 1, 1, '2025-12-24 04:32:17'),
(197, 'Célia Regina Nugoli Estevam', 'celia.estevam01@fatec.sp.gov.br', '$2y$10$4nqz9Yd3Jf70SxpVqhS/iu/4iaY4qKq82de3fMK45e4XMiiiHljzS', 'Professor', 1, 1, '2025-12-24 04:32:18'),
(198, 'Euclides Teixeira Neto', 'euclides.teixeira01@fatec.sp.gov.br', '$2y$10$4nqz9Yd3Jf70SxpVqhS/iu/4iaY4qKq82de3fMK45e4XMiiiHljzS', 'Professor', 1, 1, '2025-12-24 04:32:18'),
(199, 'Gabriela Cristiane Mendes Rahal', 'gabriela.rahal@fatec.sp.gov.br', '$2y$10$4nqz9Yd3Jf70SxpVqhS/iu/4iaY4qKq82de3fMK45e4XMiiiHljzS', 'Professor', 1, 1, '2025-12-24 04:32:18'),
(200, 'Luciana Passos Marcondes Scarsiotta', 'luciana.marcondes01@fatec.sp.gov.br', '$2y$10$4nqz9Yd3Jf70SxpVqhS/iu/4iaY4qKq82de3fMK45e4XMiiiHljzS', 'Professor', 1, 1, '2025-12-24 04:32:18'),
(201, 'Luciane Berti Ribeiro', 'luciane.ribeiro01@fatec.sp.gov.br', '$2y$10$4nqz9Yd3Jf70SxpVqhS/iu/4iaY4qKq82de3fMK45e4XMiiiHljzS', 'Professor', 1, 1, '2025-12-24 04:32:18'),
(202, 'Lucilena de Lima', 'lucilena.lima@fatec.sp.gov.br', '$2y$10$4nqz9Yd3Jf70SxpVqhS/iu/4iaY4qKq82de3fMK45e4XMiiiHljzS', 'Professor', 1, 1, '2025-12-24 04:32:18'),
(203, 'Rafael Marcelino de Jesus', 'rafael.jesus18@fatec.sp.gov.br', '$2y$10$4nqz9Yd3Jf70SxpVqhS/iu/4iaY4qKq82de3fMK45e4XMiiiHljzS', 'Professor', 1, 1, '2025-12-24 04:32:18'),
(204, 'Renata de Freitas Góis Comparoni', 'renata.comparoni3@fatec.sp.gov.br', '$2y$10$4nqz9Yd3Jf70SxpVqhS/iu/4iaY4qKq82de3fMK45e4XMiiiHljzS', 'Professor', 1, 1, '2025-12-24 04:32:18'),
(205, 'Ronnie Marcos Rillo', 'ronnie.rillo@fatec.sp.gov.br', '$2y$10$4nqz9Yd3Jf70SxpVqhS/iu/4iaY4qKq82de3fMK45e4XMiiiHljzS', 'Professor', 1, 1, '2025-12-24 04:32:18'),
(206, 'Samuel Stábile', 'samuel.stabile@fatec.sp.gov.br', '$2y$10$4nqz9Yd3Jf70SxpVqhS/iu/4iaY4qKq82de3fMK45e4XMiiiHljzS', 'Professor', 1, 1, '2025-12-24 04:32:18'),
(207, 'Saulo Felício Fernandes Zambotti', 'saulo.zambotti@fatec.sp.gov.br', '$2y$10$4nqz9Yd3Jf70SxpVqhS/iu/4iaY4qKq82de3fMK45e4XMiiiHljzS', 'Professor', 1, 1, '2025-12-24 04:32:18');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `prioridade_usuario`
--
ALTER TABLE `prioridade_usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario_id` (`usuario_id`,`projeto_id`);

--
-- Índices de tabela `projetos`
--
ALTER TABLE `projetos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_projeto_criador` (`criador_id`);

--
-- Índices de tabela `projeto_aluno`
--
ALTER TABLE `projeto_aluno`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pa_projeto` (`projeto_id`),
  ADD KEY `fk_pa_usuario` (`usuario_id`);

--
-- Índices de tabela `projeto_orientador`
--
ALTER TABLE `projeto_orientador`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_po_projeto` (`projeto_id`),
  ADD KEY `fk_po_professor` (`professor_id`);

--
-- Índices de tabela `projeto_usuario`
--
ALTER TABLE `projeto_usuario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pu_projeto` (`projeto_id`),
  ADD KEY `fk_pu_usuario` (`usuario_id`);

--
-- Índices de tabela `tarefas`
--
ALTER TABLE `tarefas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `projeto_id` (`projeto_id`),
  ADD KEY `responsavel_id` (`responsavel_id`);

--
-- Índices de tabela `tarefa_usuario`
--
ALTER TABLE `tarefa_usuario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tarefa_id` (`tarefa_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `prioridade_usuario`
--
ALTER TABLE `prioridade_usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `projetos`
--
ALTER TABLE `projetos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `projeto_aluno`
--
ALTER TABLE `projeto_aluno`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `projeto_orientador`
--
ALTER TABLE `projeto_orientador`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `projeto_usuario`
--
ALTER TABLE `projeto_usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de tabela `tarefas`
--
ALTER TABLE `tarefas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `tarefa_usuario`
--
ALTER TABLE `tarefa_usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=208;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `projetos`
--
ALTER TABLE `projetos`
  ADD CONSTRAINT `fk_projeto_criador` FOREIGN KEY (`criador_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `projeto_aluno`
--
ALTER TABLE `projeto_aluno`
  ADD CONSTRAINT `fk_pa_projeto` FOREIGN KEY (`projeto_id`) REFERENCES `projetos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pa_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `projeto_orientador`
--
ALTER TABLE `projeto_orientador`
  ADD CONSTRAINT `fk_po_professor` FOREIGN KEY (`professor_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_po_projeto` FOREIGN KEY (`projeto_id`) REFERENCES `projetos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `projeto_usuario`
--
ALTER TABLE `projeto_usuario`
  ADD CONSTRAINT `fk_pu_projeto` FOREIGN KEY (`projeto_id`) REFERENCES `projetos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pu_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `tarefas`
--
ALTER TABLE `tarefas`
  ADD CONSTRAINT `tarefas_ibfk_1` FOREIGN KEY (`projeto_id`) REFERENCES `projetos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tarefas_ibfk_2` FOREIGN KEY (`responsavel_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `tarefa_usuario`
--
ALTER TABLE `tarefa_usuario`
  ADD CONSTRAINT `tarefa_usuario_ibfk_1` FOREIGN KEY (`tarefa_id`) REFERENCES `tarefas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tarefa_usuario_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
