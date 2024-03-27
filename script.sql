CREATE TABLE `agendamentos` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `data` date NOT NULL,
  `horario` time NOT NULL,
  `servico` varchar(255) NOT NULL,
  `barbeiro` varchar(255) NOT NULL,
  `mensagem` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
)
