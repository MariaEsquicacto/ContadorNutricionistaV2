-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 11/06/2025 às 15:20
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
-- Banco de dados: `contagem`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `nome_categoria` varchar(150) NOT NULL,
  `ativa_categoria` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nome_categoria`, `ativa_categoria`) VALUES
(1, 'Fund 1 A', 1),
(2, 'Fund 1 B', 1),
(3, 'Fund 2', 1),
(4, 'E.M', 1),
(5, 'Robótica', 1),
(6, 'personaliza', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `contagem`
--

CREATE TABLE `contagem` (
  `id_contagem` int(11) NOT NULL,
  `quant_contagem` int(11) NOT NULL,
  `criacao_contagem` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `update_contagem` timestamp NULL DEFAULT NULL,
  `usuarios_id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `contagem`
--

INSERT INTO `contagem` (`id_contagem`, `quant_contagem`, `criacao_contagem`, `update_contagem`, `usuarios_id_usuario`) VALUES
(1, 12, '2025-06-06 11:09:17', '2025-06-06 11:09:17', 2),
(2, 0, '2025-06-06 12:50:05', '2025-06-06 12:50:05', 2),
(3, 0, '2025-06-06 12:55:56', '2025-06-06 12:55:56', 2),
(4, 0, '2025-06-06 13:11:10', '2025-06-06 13:11:10', 2),
(5, 12, '2025-06-06 13:13:22', '2025-06-06 13:13:22', 2),
(6, 1248, '2025-06-06 13:17:01', '2025-06-06 13:17:01', 2),
(7, 12, '2025-06-11 12:43:40', '2025-06-11 12:43:40', 14),
(8, 38, '2025-06-11 12:58:36', '2025-06-11 12:58:36', 14),
(9, 0, '2025-06-11 13:01:24', '2025-06-11 13:01:24', 14);

-- --------------------------------------------------------

--
-- Estrutura para tabela `contagens_turmas`
--

CREATE TABLE `contagens_turmas` (
  `id_contagem_turma` int(11) NOT NULL,
  `turmas_id_turma` int(11) NOT NULL,
  `contagem_id_contagem` int(11) NOT NULL,
  `quantidade_turma` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `contagens_turmas`
--

INSERT INTO `contagens_turmas` (`id_contagem_turma`, `turmas_id_turma`, `contagem_id_contagem`, `quantidade_turma`) VALUES
(62, 1, 8, 21),
(63, 2, 8, 17);

-- --------------------------------------------------------

--
-- Estrutura para tabela `estoque`
--

CREATE TABLE `estoque` (
  `id_estoque` int(11) NOT NULL,
  `nome_item_estoque` varchar(150) NOT NULL,
  `tipo_movimentacao_estoque` enum('entrada','saida') NOT NULL,
  `quantidade_estoque` int(11) NOT NULL,
  `unidade_estoque` enum('kg','gramas','litro','ml','unidade') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `estoque`
--

INSERT INTO `estoque` (`id_estoque`, `nome_item_estoque`, `tipo_movimentacao_estoque`, `quantidade_estoque`, `unidade_estoque`) VALUES
(1, 'Pão Francês', 'entrada', 200, 'unidade'),
(2, 'Leite Integral', 'entrada', 15, 'litro'),
(3, 'Manteiga', 'entrada', 3, ''),
(4, 'Pão Francês', 'saida', 120, 'unidade'),
(5, 'Leite Integral', 'saida', 5, 'litro'),
(6, 'Arroz Integral', 'entrada', 50, 'kg');

-- --------------------------------------------------------

--
-- Estrutura para tabela `refresh_tokens`
--

CREATE TABLE `refresh_tokens` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `token` varchar(500) NOT NULL,
  `expiracao` datetime NOT NULL,
  `criado_em` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `refresh_tokens`
--

INSERT INTO `refresh_tokens` (`id`, `id_usuario`, `token`, `expiracao`, `criado_em`) VALUES
(10, 2, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NDg2MjEzNzUsImV4cCI6MTc0OTIyNjE3NSwic3ViIjoyLCJub21lIjoiYXJ0aHVyIn0.Uy4aZFf8lhcM_GF_fmXeMOcyHWCZ5qD7KgWzOQOYiXE', '2025-06-06 13:09:35', '2025-05-30 13:09:35'),
(11, 2, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NDkyMDcxNjIsImV4cCI6MTc0OTgxMTk2Miwic3ViIjoyLCJub21lIjoiYXJ0aHVyIn0.BmIbj3BIfaZJMhy_3gxMgNWtAJ8ClzonK66nmAS9JHk', '2025-06-13 07:52:42', '2025-06-06 07:52:42'),
(12, 2, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NDkyMDc0ODQsImV4cCI6MTc0OTgxMjI4NCwic3ViIjoyLCJub21lIjoiYXJ0aHVyIn0.buSEUbXZZF1Kre3zF5ee_K8cjOqJVqlwTmRaFQSd_7I', '2025-06-13 07:58:04', '2025-06-06 07:58:04'),
(13, 2, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NDkyMDgxNDYsImV4cCI6MTc0OTIxMTc0NiwiaWRfdXN1YXJpbyI6Miwibm9tZSI6ImFydGh1ciIsIm5pdmVsIjoiMSJ9.sPi2y3xeiQ2LCq2SnTsTBHwAe6LVY_BASxFR74-htNQ', '2025-06-13 08:09:06', '2025-06-06 08:09:06'),
(14, 2, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NDkyMTQxOTEsImV4cCI6MTc0OTIxNzc5MSwiaWRfdXN1YXJpbyI6Miwibm9tZSI6ImFydGh1ciIsIm5pdmVsIjoiMSJ9.Zxq-V3YCEhxev_Zac3S0dOxEB5TgPVJZVs2k3bs1WaY', '2025-06-13 09:49:51', '2025-06-06 09:49:51'),
(15, 14, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NDk2Mzc1MDUsImV4cCI6MTc0OTY0MTEwNSwiaWRfdXN1YXJpbyI6MTQsIm5vbWUiOiJEYXZpIiwibml2ZWwiOiIxIn0._NcREOVpLB8aSBOVG1L-cWXCwkLCBLqaEMdM0UE9-wk', '2025-06-18 07:25:05', '2025-06-11 07:25:05'),
(16, 14, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NDk2NDIxNzYsImV4cCI6MTc0OTY0NTc3NiwiaWRfdXN1YXJpbyI6MTQsIm5vbWUiOiJEYXZpIiwibml2ZWwiOiIxIn0.KRn_Vkvkq9nXH-yee5D2zPqjNVBH656eYY2klibMZm0', '2025-06-18 08:42:56', '2025-06-11 08:42:56'),
(17, 14, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NDk2NDI1NDcsImV4cCI6MTc0OTY0NjE0NywiaWRfdXN1YXJpbyI6MTQsIm5vbWUiOiJEYXZpIiwibml2ZWwiOiIxIn0.kjxPbLNS3Jqe-vmiveu9mHB09yj5qD3ALd8QmREQP-w', '2025-06-18 08:49:07', '2025-06-11 08:49:07'),
(18, 14, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NDk2NDUwNDEsImV4cCI6MTc0OTY0ODY0MSwiaWRfdXN1YXJpbyI6MTQsIm5vbWUiOiJEYXZpIiwibml2ZWwiOiIxIn0.W893HL_B67QyPJiv6pVMX5mPoVGEFwLT5YTcSQ-pS2w', '2025-06-18 09:30:41', '2025-06-11 09:30:41'),
(19, 14, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NDk2NDUyNDEsImV4cCI6MTc0OTY0ODg0MSwiaWRfdXN1YXJpbyI6MTQsIm5vbWUiOiJEYXZpIiwibml2ZWwiOiIxIn0.kx0DbOYzYeU1q_xD2Le1aPOJxF510uvvFYEFYwwyde0', '2025-06-18 09:34:01', '2025-06-11 09:34:01'),
(20, 14, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NDk2NDU4MDksImV4cCI6MTc0OTY0OTQwOSwiaWRfdXN1YXJpbyI6MTQsIm5vbWUiOiJEYXZpIiwibml2ZWwiOiIxIn0.sZGLpc6L6UNbDB1yBt1enGqMvD30eQbiWonXzjN4ZgE', '2025-06-18 09:43:29', '2025-06-11 09:43:29'),
(21, 14, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NDk2NDY2OTMsImV4cCI6MTc0OTY1MDI5MywiaWRfdXN1YXJpbyI6MTQsIm5vbWUiOiJEYXZpIiwibml2ZWwiOiIxIn0.MTRM1lnLchiyKSFP7wychDJLeR6VGW29zFPw-G304JA', '2025-06-18 09:58:13', '2025-06-11 09:58:13'),
(22, 14, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NDk2NDY4NTYsImV4cCI6MTc0OTY1MDQ1NiwiaWRfdXN1YXJpbyI6MTQsIm5vbWUiOiJEYXZpIiwibml2ZWwiOiIxIn0.dC1xre4WSDqVGbpF_8UIHt2U335mWJKVR2kysJIt8uI', '2025-06-18 10:00:56', '2025-06-11 10:00:56');

-- --------------------------------------------------------

--
-- Estrutura para tabela `session`
--

CREATE TABLE `session` (
  `id_session` int(11) NOT NULL,
  `token` varchar(300) NOT NULL,
  `criado_em` datetime NOT NULL,
  `expira_em` datetime NOT NULL,
  `status` enum('0','1') NOT NULL,
  `user368_id_user368` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `session`
--

INSERT INTO `session` (`id_session`, `token`, `criado_em`, `expira_em`, `status`, `user368_id_user368`) VALUES
(18, 'c7906f438b84968af2be3ae62548b8715569825a7d5d4814d7124d92c4d87735', '2025-05-28 13:40:41', '2025-05-28 14:40:41', '1', 2);

-- --------------------------------------------------------

--
-- Estrutura para tabela `turmas`
--

CREATE TABLE `turmas` (
  `id_turma` int(11) NOT NULL,
  `nome_turma` varchar(150) NOT NULL,
  `categorias_id_categoria` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `turmas`
--

INSERT INTO `turmas` (`id_turma`, `nome_turma`, `categorias_id_categoria`) VALUES
(1, '1° ano', 1),
(2, '2° ano', 1),
(3, '3° ano', 2),
(4, '4° ano', 2),
(5, '5° ano', 2),
(6, '6° ano', 3),
(7, '7° ano', 3),
(8, '8° ano', 3),
(9, '9° ano', 3),
(10, '1° ano', 4),
(11, '2° ano', 4),
(12, '3° ano', 4);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nome_usuario` varchar(150) NOT NULL,
  `senha_usuario` varchar(150) NOT NULL,
  `nivel_usuario` enum('1','2','3') NOT NULL,
  `ativo_usuario` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nome_usuario`, `senha_usuario`, `nivel_usuario`, `ativo_usuario`) VALUES
(2, 'arthur', '$2y$10$byIm14vM2MsU8m9j9CE6muUu7e/sn0H4UcOewrrT4vbN2wxQldd8C', '1', 1),
(3, 'Cheregas', '$2y$10$.ukGjN5aGQ90pKe4VY8b1ums4DvfM2LjuK5AOqHq0v1hsZ2fKBKLi', '1', 1),
(4, 'Eric', '$2y$10$/lk2dACDUWLSqi/UpEJuH.90wAZa2tSigUgbCZtGHg.HBAVazYXyu', '1', 1),
(11, 'ArthurTeste', '$2y$10$IRss6o.H4BIYTug8DonmYOCEBjvO3ahYCL6KEyJ04Xo0kgS9n99RO', '1', 1),
(12, 'EricTeste', '$2y$10$C.Ot..o6sMeKob/fJGzrQOv76ascDT04nidL5jNrQnHClOLQSjrC.', '1', 1),
(13, 'Maria Clara', '$2y$10$wix6cVHj/fLh6Yk1vQFzlOgSWKUeLEwolmD2UB1Gm47Uhmk85Dgmi', '1', 1),
(14, 'Davi', '$2y$10$I8oFOZf68qRoHNZAx2uFnelFWgm98UxG9idO9JksogmbaE3X.xpGa', '1', 1);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Índices de tabela `contagem`
--
ALTER TABLE `contagem`
  ADD PRIMARY KEY (`id_contagem`),
  ADD KEY `fk_contagem_usuarios_idx` (`usuarios_id_usuario`);

--
-- Índices de tabela `contagens_turmas`
--
ALTER TABLE `contagens_turmas`
  ADD PRIMARY KEY (`id_contagem_turma`),
  ADD KEY `fk_turma` (`turmas_id_turma`),
  ADD KEY `fk_contagem` (`contagem_id_contagem`);

--
-- Índices de tabela `estoque`
--
ALTER TABLE `estoque`
  ADD PRIMARY KEY (`id_estoque`);

--
-- Índices de tabela `refresh_tokens`
--
ALTER TABLE `refresh_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Índices de tabela `session`
--
ALTER TABLE `session`
  ADD PRIMARY KEY (`id_session`);

--
-- Índices de tabela `turmas`
--
ALTER TABLE `turmas`
  ADD PRIMARY KEY (`id_turma`),
  ADD KEY `fk_turmas_categorias1_idx` (`categorias_id_categoria`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `contagem`
--
ALTER TABLE `contagem`
  MODIFY `id_contagem` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `contagens_turmas`
--
ALTER TABLE `contagens_turmas`
  MODIFY `id_contagem_turma` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT de tabela `estoque`
--
ALTER TABLE `estoque`
  MODIFY `id_estoque` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `refresh_tokens`
--
ALTER TABLE `refresh_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de tabela `session`
--
ALTER TABLE `session`
  MODIFY `id_session` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `turmas`
--
ALTER TABLE `turmas`
  MODIFY `id_turma` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `contagem`
--
ALTER TABLE `contagem`
  ADD CONSTRAINT `fk_contagem_usuarios` FOREIGN KEY (`usuarios_id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `contagens_turmas`
--
ALTER TABLE `contagens_turmas`
  ADD CONSTRAINT `fk_contagem` FOREIGN KEY (`contagem_id_contagem`) REFERENCES `contagem` (`id_contagem`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_turma` FOREIGN KEY (`turmas_id_turma`) REFERENCES `turmas` (`id_turma`) ON DELETE CASCADE;

--
-- Restrições para tabelas `refresh_tokens`
--
ALTER TABLE `refresh_tokens`
  ADD CONSTRAINT `refresh_tokens_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Restrições para tabelas `turmas`
--
ALTER TABLE `turmas`
  ADD CONSTRAINT `fk_turmas_categorias1` FOREIGN KEY (`categorias_id_categoria`) REFERENCES `categorias` (`id_categoria`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
