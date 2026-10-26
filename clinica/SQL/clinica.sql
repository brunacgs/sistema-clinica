-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 26/10/2026 às 23:49
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
-- Banco de dados: `clinica`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `consulta`
--

CREATE TABLE `consulta` (
  `id_consulta` int(11) NOT NULL,
  `id_paciente` int(11) NOT NULL,
  `id_medico` int(11) NOT NULL,
  `data_consulta` date NOT NULL,
  `horario` time NOT NULL,
  `motivo` varchar(200) NOT NULL,
  `observacoes` text DEFAULT NULL,
  `status` enum('Agendada','Confirmada','Realizada','Cancelada') NOT NULL DEFAULT 'Agendada'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `consulta`
--

INSERT INTO `consulta` (`id_consulta`, `id_paciente`, `id_medico`, `data_consulta`, `horario`, `motivo`, `observacoes`, `status`) VALUES
(1, 1, 1, '2026-10-27', '08:00:00', 'Dor no peito ao fazer esforço', 'Trazer exames anteriores', 'Confirmada'),
(2, 4, 1, '2026-10-27', '09:00:00', 'Retorno - pressão alta', NULL, 'Agendada'),
(3, 5, 1, '2026-10-27', '10:30:00', 'Check-up cardíaco', NULL, 'Agendada'),
(4, 1, 4, '2026-09-15', '14:00:00', 'Consulta de rotina', 'Paciente em jejum', 'Realizada'),
(5, 1, 3, '2026-11-05', '16:00:00', 'Manchas na pele', NULL, 'Agendada'),
(6, 2, 2, '2026-10-28', '11:00:00', 'Dor no joelho direito', 'Lesão no futebol', 'Agendada'),
(7, 3, 3, '2026-10-29', '15:00:00', 'Acne', NULL, 'Cancelada'),
(8, 2, 4, '2026-10-20', '09:00:00', 'Febre há 3 dias', NULL, 'Realizada');

-- --------------------------------------------------------

--
-- Estrutura para tabela `convenio`
--

CREATE TABLE `convenio` (
  `id_convenio` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `percentual_desconto` decimal(5,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `convenio`
--

INSERT INTO `convenio` (`id_convenio`, `nome`, `telefone`, `percentual_desconto`) VALUES
(1, 'SaúdeMais', '(11) 4000-1000', 30.00),
(2, 'Vida Total', '(11) 4000-2000', 50.00),
(3, 'Bem Estar', '(11) 4000-3000', 20.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `exame`
--

CREATE TABLE `exame` (
  `id_exame` int(11) NOT NULL,
  `id_paciente` int(11) NOT NULL,
  `id_consulta` int(11) DEFAULT NULL,
  `nome_exame` varchar(120) NOT NULL,
  `data_solicitacao` date NOT NULL,
  `data_realizacao` date DEFAULT NULL,
  `status` enum('Solicitado','Agendado','Realizado','Cancelado') NOT NULL DEFAULT 'Solicitado',
  `resultado` text DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `exame`
--

INSERT INTO `exame` (`id_exame`, `id_paciente`, `id_consulta`, `nome_exame`, `data_solicitacao`, `data_realizacao`, `status`, `resultado`, `valor`) VALUES
(1, 1, 4, 'Hemograma completo', '2026-09-15', '2026-09-18', 'Realizado', 'Valores dentro da normalidade', 45.00),
(2, 1, 4, 'Eletrocardiograma', '2026-09-15', NULL, 'Solicitado', NULL, 120.00),
(3, 1, 1, 'Teste ergométrico', '2026-10-27', NULL, 'Agendado', NULL, 250.00),
(4, 2, 8, 'Raio-X do tórax', '2026-10-20', NULL, 'Solicitado', NULL, 90.00),
(5, 4, 2, 'Colesterol total e frações', '2026-10-01', '2026-10-03', 'Realizado', 'LDL levemente elevado', 60.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `medico`
--

CREATE TABLE `medico` (
  `id_medico` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `crm` varchar(20) NOT NULL,
  `especialidade` varchar(80) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `valor_consulta` decimal(10,2) NOT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `medico`
--

INSERT INTO `medico` (`id_medico`, `nome`, `crm`, `especialidade`, `telefone`, `email`, `valor_consulta`, `foto`) VALUES
(1, 'Dra. Helena Costa', '123456-SP', 'Cardiologia', '(11) 3000-0001', 'helena@vidaplena.com', 350.00, 'helena-costa.jpg'),
(2, 'Dr. Ricardo Mendes', '234567-SP', 'Ortopedia', '(11) 3000-0002', 'ricardo@vidaplena.com', 300.00, 'ricardo-mendes.jpg'),
(3, 'Dra. Juliana Ferraz', '345678-SP', 'Dermatologia', '(11) 3000-0003', 'juliana@vidaplena.com', 280.00, 'juliana-ferraz.jpg'),
(4, 'Dr. Paulo Nogueira', '456789-SP', 'Clínica Geral', '(11) 3000-0004', 'paulo@vidaplena.com', 200.00, 'paulo-nogueira.jpg');

-- --------------------------------------------------------

--
-- Estrutura para tabela `paciente`
--

CREATE TABLE `paciente` (
  `id_paciente` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `cpf` char(14) NOT NULL,
  `data_nascimento` date NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `endereco` varchar(200) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `id_convenio` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `paciente`
--

INSERT INTO `paciente` (`id_paciente`, `nome`, `cpf`, `data_nascimento`, `telefone`, `email`, `endereco`, `cidade`, `id_convenio`) VALUES
(1, 'Maria Oliveira Santos', '123.456.789-00', '1985-03-12', '(11) 91234-5678', 'maria@email.com', 'Rua das Flores, 120', 'São Paulo', 1),
(2, 'João Pereira Lima', '234.567.890-11', '1990-07-25', '(11) 92345-6789', 'joao@email.com', 'Av. Brasil, 455', 'Osasco', NULL),
(3, 'Ana Clara Souza', '345.678.901-22', '2001-11-02', '(11) 93456-7890', 'ana@email.com', 'Rua Ipê, 88', 'São Paulo', 2),
(4, 'Carlos Eduardo Rocha', '456.789.012-33', '1972-01-30', '(11) 94567-8901', 'carlos@email.com', 'Rua Augusta, 900', 'São Paulo', 3),
(5, 'Beatriz Almeida', '567.890.123-44', '1995-09-18', '(11) 95678-9012', 'bia@email.com', 'Rua da Paz, 15', 'Barueri', 1);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `consulta`
--
ALTER TABLE `consulta`
  ADD PRIMARY KEY (`id_consulta`),
  ADD KEY `id_paciente` (`id_paciente`),
  ADD KEY `id_medico` (`id_medico`);

--
-- Índices de tabela `convenio`
--
ALTER TABLE `convenio`
  ADD PRIMARY KEY (`id_convenio`);

--
-- Índices de tabela `exame`
--
ALTER TABLE `exame`
  ADD PRIMARY KEY (`id_exame`),
  ADD KEY `id_paciente` (`id_paciente`),
  ADD KEY `id_consulta` (`id_consulta`);

--
-- Índices de tabela `medico`
--
ALTER TABLE `medico`
  ADD PRIMARY KEY (`id_medico`),
  ADD UNIQUE KEY `crm` (`crm`);

--
-- Índices de tabela `paciente`
--
ALTER TABLE `paciente`
  ADD PRIMARY KEY (`id_paciente`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD KEY `id_convenio` (`id_convenio`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `consulta`
--
ALTER TABLE `consulta`
  MODIFY `id_consulta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `convenio`
--
ALTER TABLE `convenio`
  MODIFY `id_convenio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `exame`
--
ALTER TABLE `exame`
  MODIFY `id_exame` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `medico`
--
ALTER TABLE `medico`
  MODIFY `id_medico` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `paciente`
--
ALTER TABLE `paciente`
  MODIFY `id_paciente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `consulta`
--
ALTER TABLE `consulta`
  ADD CONSTRAINT `consulta_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `paciente` (`id_paciente`),
  ADD CONSTRAINT `consulta_ibfk_2` FOREIGN KEY (`id_medico`) REFERENCES `medico` (`id_medico`);

--
-- Restrições para tabelas `exame`
--
ALTER TABLE `exame`
  ADD CONSTRAINT `exame_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `paciente` (`id_paciente`),
  ADD CONSTRAINT `exame_ibfk_2` FOREIGN KEY (`id_consulta`) REFERENCES `consulta` (`id_consulta`);

--
-- Restrições para tabelas `paciente`
--
ALTER TABLE `paciente`
  ADD CONSTRAINT `paciente_ibfk_1` FOREIGN KEY (`id_convenio`) REFERENCES `convenio` (`id_convenio`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
