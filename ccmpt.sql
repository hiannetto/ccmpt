-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 08/10/2026 às 17:06
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
-- Banco de dados: `ccmpt`
--
CREATE DATABASE IF NOT EXISTS `ccmpt` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `ccmpt`;

-- --------------------------------------------------------

--
-- Estrutura para tabela `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `type` varchar(50) DEFAULT NULL COMMENT 'Ex: decada, origem, tipo_objeto',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `type`, `created_at`) VALUES
(1, 'década de 1950', 'decada-de-1950', 'decada', '2026-10-06 01:41:23'),
(2, 'década de 1960', 'decada-de-1960', 'decada', '2026-10-06 01:41:23'),
(3, 'década de 1970', 'decada-de-1970', 'decada', '2026-10-06 01:41:23'),
(4, 'década de 1980', 'decada-de-1980', 'decada', '2026-10-06 01:41:23'),
(5, 'década de 1990', 'decada-de-1990', 'decada', '2026-10-06 01:41:23'),
(6, 'década de 2000', 'decada-de-2000', 'decada', '2026-10-06 01:41:23'),
(7, 'Liturgia', 'liturgia', 'categoria', '2026-10-06 01:41:23'),
(8, 'Objetos Pessoais', 'objetos-pessoais', 'categoria', '2026-10-06 01:41:23'),
(9, 'Documentos', 'documentos', 'categoria', '2026-10-06 01:41:23'),
(10, 'Vestuário', 'vestuario', 'tipo_objeto', '2026-10-06 01:41:23'),
(11, 'Livro', 'livro', 'tipo_objeto', '2026-10-06 01:41:23'),
(12, 'Mobiliário', 'mobiliario', 'tipo_objeto', '2026-10-06 01:41:23');

-- --------------------------------------------------------

--
-- Estrutura para tabela `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `subject` varchar(150) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('pendente','atendido') NOT NULL DEFAULT 'pendente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `phone`, `subject`, `message`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Hian Monteiro', 'hiannetto123@gmail.com', '32988442520', 'Teste', 'Posso testar seu sistema??????????', 'pendente', '2026-10-06 19:51:46', '2026-10-06 19:51:46');

-- --------------------------------------------------------

--
-- Estrutura para tabela `memorial_images`
--

CREATE TABLE `memorial_images` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `image_url` varchar(500) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `memorial_items`
--

CREATE TABLE `memorial_items` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `dating_label` varchar(100) DEFAULT NULL COMMENT 'Datação em texto livre (ex.: c. 1965, década de 1970)',
  `year` smallint(6) DEFAULT NULL COMMENT 'Ano de referência para ordenação e sugestão de década',
  `inventory_number` varchar(50) DEFAULT NULL COMMENT 'Nº de inventário / tombo',
  `historical_description` mediumtext NOT NULL,
  `material` varchar(255) DEFAULT NULL,
  `dimensions` varchar(255) DEFAULT NULL,
  `provenance` varchar(255) DEFAULT NULL COMMENT 'Procedência',
  `conservation_state` varchar(20) DEFAULT NULL COMMENT 'otimo, bom, regular, ruim',
  `main_image_url` varchar(500) NOT NULL COMMENT 'Caminho local da foto principal em WebP',
  `main_image_caption` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `memorial_items`
--

INSERT INTO `memorial_items` (`id`, `title`, `dating_label`, `year`, `inventory_number`, `historical_description`, `material`, `dimensions`, `provenance`, `conservation_state`, `main_image_url`, `main_image_caption`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Garrafa D\'água', NULL, 2024, NULL, '<p>Garrafa de água vermelha que eu roubei da minha mãe depois de ela acidentalmente quebrar a minha.</p>', NULL, NULL, 'Roubado por Hian Monteiro', 'ruim', '/uploads/memorial/batch_1/27ed54e6b1c5_1791312459.jpg', 'Foto da garrafa', '2026-10-06 18:47:32', '2026-10-06 18:47:39', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `memorial_item_category`
--

CREATE TABLE `memorial_item_category` (
  `item_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` mediumtext NOT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `show_in_menu` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pages`
--

INSERT INTO `pages` (`id`, `title`, `slug`, `content`, `is_published`, `show_in_menu`, `updated_at`) VALUES
(1, 'A Instituição', 'instituicao', '<h2><span style=\"color: rgb(31, 31, 31); background-color: rgba(0, 0, 0, 0);\">Centro Cultural e Memorial Padre Tiago: Cultura, Memória e Transformação Social</span></h2><p><span style=\"color: rgb(31, 31, 31); background-color: rgba(0, 0, 0, 0);\">O Centro Cultural e Memorial Padre Tiago é um espaço de fomento à cultura, à educação e à cidadania, localizado no coração do bairro que carrega o nome de seu grande inspirador. Nascido do desejo de dar continuidade ao trabalho social e humanitário de Jacobus Adrianus Sigfridus Prins — o saudoso Padre Tiago —, a instituição atua como um polo de desenvolvimento humano e acolhimento para crianças, jovens e famílias de Muriaé.</span></p><p><br></p><h3><span style=\"color: rgb(31, 31, 31); background-color: rgba(0, 0, 0, 0);\">Nosso Propósito</span></h3><p><span style=\"color: rgb(31, 31, 31); background-color: rgba(0, 0, 0, 0);\">Se no passado o Projeto Pró-Moradia ergueu lares para garantir a segurança física da comunidade, hoje o Centro Cultural e Memorial atua na construção da dignidade intelectual e cidadã. Nossa missão é preservar a memória e os ideais do \"revolucionário do amor\", garantindo que seu legado de transformação social se mantenha vivo e ativo através do acesso democrático à arte, ao conhecimento e ao convívio comunitário.</span></p><p><br></p><h3><span style=\"color: rgb(31, 31, 31); background-color: rgba(0, 0, 0, 0);\">Cultura e Cidadania em Movimento</span></h3><p><span style=\"color: rgb(31, 31, 31); background-color: rgba(0, 0, 0, 0);\">O espaço oferece uma infraestrutura dedicada ao aprendizado e à valorização de talentos, promovendo a integração social por meio de diversas frentes:</span></p><p><br></p><ol><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong style=\"color: rgb(31, 31, 31); background-color: rgba(0, 0, 0, 0);\">Educação e Arte:</strong><span style=\"color: rgb(31, 31, 31); background-color: rgba(0, 0, 0, 0);\"> Aulas de música e dança, proporcionando novas perspectivas, disciplina e oportunidades para os jovens da comunidade.</span></li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><br></li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong style=\"color: rgb(31, 31, 31); background-color: rgba(0, 0, 0, 0);\">Incentivo à Leitura:</strong><span style=\"color: rgb(31, 31, 31); background-color: rgba(0, 0, 0, 0);\"> Uma sala de leitura estruturada para democratizar o acesso aos livros, estimular a imaginação e apoiar o desenvolvimento escolar.</span></li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><br></li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong style=\"color: rgb(31, 31, 31); background-color: rgba(0, 0, 0, 0);\">Acolhimento Comunitário:</strong><span style=\"color: rgb(31, 31, 31); background-color: rgba(0, 0, 0, 0);\"> O centro orgulha-se de ser a casa de importantes iniciativas locais, servindo como base para os encontros do </span><strong style=\"color: rgb(31, 31, 31); background-color: rgba(0, 0, 0, 0);\">Clube das Mães</strong><span style=\"color: rgb(31, 31, 31); background-color: rgba(0, 0, 0, 0);\"> e como sede de ensaios e atividades da </span><strong style=\"color: rgb(31, 31, 31); background-color: rgba(0, 0, 0, 0);\">Banda Marcial Bernadete Carneiro</strong><span style=\"color: rgb(31, 31, 31); background-color: rgba(0, 0, 0, 0);\">.</span></li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><br></li></ol><h3><span style=\"color: rgb(31, 31, 31); background-color: rgba(0, 0, 0, 0);\">Nossa Trajetória</span></h3><p><span style=\"color: rgb(31, 31, 31); background-color: rgba(0, 0, 0, 0);\">Formalmente constituída como entidade no final do ano de 2020, a organização alcançou um marco histórico em setembro de 2025, quando suas instalações físicas foram oficialmente entregues e inauguradas com o apoio da Prefeitura de Muriaé. Esse passo consolidou definitivamente a infraestrutura necessária para que o espaço se tornasse a principal referência de cultura e preservação histórica no bairro.</span></p><p><br></p><h3><span style=\"color: rgb(31, 31, 31); background-color: rgba(0, 0, 0, 0);\">Visão de Futuro</span></h3><p><span style=\"color: rgb(31, 31, 31); background-color: rgba(0, 0, 0, 0);\">Acreditamos que a cultura é o alicerce para uma sociedade mais justa e com oportunidades igualitárias. O Centro Cultural e Memorial Padre Tiago de portas abertas reafirma, todos os dias, o compromisso de ser um ambiente onde o passado de luta é honrado, o presente é transformado pela arte e o futuro é construído com esperança.</span></p><p><br></p>', 1, 1, '2026-10-06 19:56:35'),
(2, 'História do Padre Tiago', 'historia', '<h3>Quem Foi Padre Tiago?</h3><p>Jacobus Adrianus Sigfridus Prins, carinhosamente adotado pela nossa região como Padre Tiago, foi um líder espiritual e missionário que dedicou sua vida à transformação social. Nascido em Voorhout, na Holanda, em 29 de janeiro de 1930, ele chegou ao Brasil em 1962 pela Congregação dos Missionários do Sagrado Coração de Jesus. Sua trajetória é marcada pela profunda união entre o ministério religioso e a defesa ativa da dignidade humana.</p><h3>A Educação e o Caminho na Fé</h3><p>Antes de se tornar um símbolo de ação social, Padre Tiago atuou na formação intelectual da comunidade. Em seus primeiros anos no país, trabalhou como professor de latim, francês e história, acreditando na educação como ferramenta de emancipação. No campo religioso, liderou paróquias, reformou e construiu capelas e dedicou grande parte de seus últimos anos de ministério pastoral ao distrito de Itamuri, sempre próximo e acessível aos moradores.</p><h3>Projeto Pró-Moradia: Construindo Dignidade</h3><p>O maior símbolo de sua passagem por Muriaé é o Projeto Pró-Moradia. Incomodado com a desigualdade e a falta de infraestrutura para os mais vulneráveis, Padre Tiago organizou a sociedade civil, mobilizou doações e liderou mutirões para construir e entregar dezenas de casas populares. O projeto não apenas ergueu lares seguros, mas resgatou a cidadania de famílias inteiras que viviam em situação de risco, provando que a fé verdadeira se materializa em obras concretas.</p><h3>Um Revolucionário do Amor</h3><p>Falecido em 22 de junho de 2010, Padre Tiago deixou um vazio na comunidade, mas também um exemplo imortal. Seu trabalho contínuo e silencioso em prol dos mais pobres lhe rendeu o título de \"revolucionário do amor\" entre os paroquianos. Hoje, sua memória permanece viva nos bairros que ajudou a erguer e no coração de cada família que teve sua realidade transformada por sua compaixão e coragem.</p><h3>O Centro Cultural: O Legado em Movimento</h3><p>Para além das fundações de tijolo e cimento do Projeto Pró-Moradia, o compromisso de Padre Tiago com o desenvolvimento humano se perpetua de forma vibrante através do Centro Cultural que leva o seu nome. Situado no coração da comunidade que ele ajudou a erguer, o espaço é a prova de que sua missão ia muito além da infraestrutura física. Hoje, o centro atua como um polo de transformação e acolhimento, oferecendo acesso à arte, educação, convivência e cidadania. É nesse ambiente, que abraça crianças, jovens e famílias, que a visão transformadora do padre se mantém viva, garantindo que as novas gerações tenham não apenas um teto seguro, mas também o espaço e o estímulo necessários para crescerem intelectual e culturalmente.</p>', 1, 1, '2026-10-06 19:55:14'),
(3, 'Nossos Projetos', 'projetos', '<h2>Projetos Sociais: O Coração do Nosso Centro Cultural</h2><p>No Centro Cultural e Memorial Padre Tiago, acreditamos que a verdadeira transformação social acontece na prática, no dia a dia, e em comunidade. Nossos projetos sociais são a alma da instituição e o reflexo vivo dos ideais de acolhimento e desenvolvimento humano deixados pelo Padre Tiago.</p><p>Através da arte, do esporte e da preservação das nossas tradições, oferecemos um ambiente seguro e estimulante onde crianças, jovens e adultos do bairro e de toda Muriaé podem descobrir seus talentos, fortalecer laços e construir novas perspectivas de vida.</p><p>Conheça as iniciativas que movimentam o nosso espaço:</p><ol><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Banda Marcial Bernadete Carneiro:</strong> Muito mais do que o ensino musical, a banda promove a disciplina, o trabalho em equipe e o orgulho de pertencer a um grupo histórico, levando a cultura e o nome da nossa comunidade para apresentações em toda a região.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Folia de Reis:</strong> Um compromisso profundo com a preservação da cultura popular e da fé. Apoiamos e mantemos viva essa tradição secular, garantindo que as raízes folclóricas de Minas Gerais sejam passadas de geração em geração.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Quadrilha:</strong> Celebrando a alegria das nossas raízes, o grupo de quadrilha movimenta a comunidade, resgata as tradições festivas e promove a integração de todas as idades com muita dança, cores e ritmos populares.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Capoeira:</strong> Uma poderosa ferramenta de inclusão e resistência. As aulas de capoeira unem esporte, arte, música e história afro-brasileira, ensinando aos alunos o respeito, a coordenação motora e o valor da nossa ancestralidade.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Aulas de Dança:</strong> Um espaço dedicado à expressão corporal e à criatividade. A dança no Centro Cultural atende a diferentes estilos e idades, promovendo saúde física, bem-estar mental e autoconfiança.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Escolinha de Futebol:</strong> Muito além das quatro linhas, o esporte atua como uma escola de cidadania. Nossa escolinha de futebol incentiva a prática esportiva, afasta os jovens das ruas e ensina valores fundamentais como cooperação, respeito às regras e superação.</li></ol><h3>Transformando Vidas</h3><p>Cada um desses projetos é uma porta aberta para o futuro. O Centro Cultural e Memorial Padre Tiago se orgulha de ser o ponto de encontro onde a cultura popular é celebrada, o corpo é movimentado e a cidadania é construída todos os dias.</p>', 1, 1, '2026-10-06 19:58:29');

-- --------------------------------------------------------

--
-- Estrutura para tabela `photos`
--

CREATE TABLE `photos` (
  `id` int(11) NOT NULL,
  `gallery_id` int(11) NOT NULL,
  `image_path` varchar(500) NOT NULL COMMENT 'Caminho local da foto em WebP (ex: max 1920px)',
  `thumbnail_path` varchar(500) NOT NULL COMMENT 'Caminho local da miniatura em WebP (ex: 400px)',
  `caption` varchar(255) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL COMMENT 'Palavras-chave separadas por vírgula (ex: Igreja Matriz, Batizado)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `photos`
--

INSERT INTO `photos` (`id`, `gallery_id`, `image_path`, `thumbnail_path`, `caption`, `tags`, `created_at`, `deleted_at`) VALUES
(12, 4, '/uploads/galleries/batch_1/9dc3dfc3ea12_1791311789.jpg', '/uploads/galleries/batch_1/9dc3dfc3ea12_1791311789.jpg', NULL, NULL, '2026-10-06 18:36:29', '2026-10-06 18:40:34'),
(13, 4, '/uploads/galleries/batch_1/f1595be32ad0_1791311789.jpg', '/uploads/galleries/batch_1/f1595be32ad0_1791311789.jpg', NULL, NULL, '2026-10-06 18:36:29', '2026-10-06 18:40:49'),
(14, 4, '/uploads/galleries/batch_1/f326ad77e035_1791311789.jpg', '/uploads/galleries/batch_1/f326ad77e035_1791311789.jpg', NULL, NULL, '2026-10-06 18:36:29', '2026-10-06 18:40:53'),
(15, 4, '/uploads/galleries/batch_1/b1bfb983d770_1791311790.jpg', '/uploads/galleries/batch_1/b1bfb983d770_1791311790.jpg', NULL, NULL, '2026-10-06 18:36:30', '2026-10-06 18:40:56'),
(16, 4, '/uploads/galleries/batch_1/04fde6f56dc2_1791312892.jpeg', '/uploads/galleries/batch_1/04fde6f56dc2_1791312892.jpeg', NULL, 'mercado livre mandou essa bomba errado kkkkk', '2026-10-06 18:54:52', NULL),
(17, 4, '/uploads/galleries/batch_1/62247a6467aa_1791312926.png', '/uploads/galleries/batch_1/62247a6467aa_1791312926.png', NULL, NULL, '2026-10-06 18:55:26', NULL),
(18, 5, '/uploads/galleries/batch_1/f50985e2e401_1791312963.jpg', '/uploads/galleries/batch_1/f50985e2e401_1791312963.jpg', NULL, NULL, '2026-10-06 18:56:03', NULL),
(19, 5, '/uploads/galleries/batch_1/923a0497c4f6_1791312963.jpg', '/uploads/galleries/batch_1/923a0497c4f6_1791312963.jpg', NULL, NULL, '2026-10-06 18:56:03', NULL),
(20, 5, '/uploads/galleries/batch_1/45b6282c9094_1791312981.jpg', '/uploads/galleries/batch_1/45b6282c9094_1791312981.jpg', NULL, NULL, '2026-10-06 18:56:21', NULL),
(21, 5, '/uploads/galleries/batch_1/c4502dfdabad_1791312981.jpg', '/uploads/galleries/batch_1/c4502dfdabad_1791312981.jpg', NULL, NULL, '2026-10-06 18:56:21', NULL),
(22, 5, '/uploads/galleries/batch_1/45da82c3383f_1791312981.png', '/uploads/galleries/batch_1/45da82c3383f_1791312981.png', NULL, NULL, '2026-10-06 18:56:21', NULL),
(23, 5, '/uploads/galleries/batch_1/0f87e74ace2f_1791312981.png', '/uploads/galleries/batch_1/0f87e74ace2f_1791312981.png', NULL, NULL, '2026-10-06 18:56:21', NULL),
(24, 5, '/uploads/galleries/batch_1/1fe83c642b63_1791312981.jpg', '/uploads/galleries/batch_1/1fe83c642b63_1791312981.jpg', NULL, NULL, '2026-10-06 18:56:21', NULL),
(25, 5, '/uploads/galleries/batch_1/bea73890fcce_1791312981.jpg', '/uploads/galleries/batch_1/bea73890fcce_1791312981.jpg', NULL, NULL, '2026-10-06 18:56:21', NULL),
(26, 5, '/uploads/galleries/batch_1/f10c273e8355_1791312981.jpg', '/uploads/galleries/batch_1/f10c273e8355_1791312981.jpg', NULL, NULL, '2026-10-06 18:56:21', NULL),
(27, 5, '/uploads/galleries/batch_1/856091d9b388_1791312981.jpg', '/uploads/galleries/batch_1/856091d9b388_1791312981.jpg', NULL, NULL, '2026-10-06 18:56:21', NULL),
(28, 5, '/uploads/galleries/batch_1/ac5beb622168_1791312981.jpg', '/uploads/galleries/batch_1/ac5beb622168_1791312981.jpg', NULL, NULL, '2026-10-06 18:56:21', NULL),
(29, 5, '/uploads/galleries/batch_1/cde84ad475f9_1791312981.jpg', '/uploads/galleries/batch_1/cde84ad475f9_1791312981.jpg', NULL, NULL, '2026-10-06 18:56:21', NULL),
(30, 5, '/uploads/galleries/batch_1/f12bd0a6f05c_1791312981.jpg', '/uploads/galleries/batch_1/f12bd0a6f05c_1791312981.jpg', NULL, NULL, '2026-10-06 18:56:21', NULL),
(31, 5, '/uploads/galleries/batch_1/998eb2fb8f1f_1791312981.png', '/uploads/galleries/batch_1/998eb2fb8f1f_1791312981.png', NULL, NULL, '2026-10-06 18:56:21', NULL),
(32, 5, '/uploads/galleries/batch_1/02acf718eddf_1791312981.jpg', '/uploads/galleries/batch_1/02acf718eddf_1791312981.jpg', NULL, NULL, '2026-10-06 18:56:21', NULL),
(33, 5, '/uploads/galleries/batch_1/b32db1b069b6_1791312981.jpg', '/uploads/galleries/batch_1/b32db1b069b6_1791312981.jpg', NULL, NULL, '2026-10-06 18:56:21', NULL),
(34, 5, '/uploads/galleries/batch_1/a5d39390cf8b_1791312981.jpg', '/uploads/galleries/batch_1/a5d39390cf8b_1791312981.jpg', NULL, NULL, '2026-10-06 18:56:21', NULL),
(35, 5, '/uploads/galleries/batch_1/0cbfefcaf438_1791312981.jpg', '/uploads/galleries/batch_1/0cbfefcaf438_1791312981.jpg', NULL, NULL, '2026-10-06 18:56:21', NULL),
(36, 5, '/uploads/galleries/batch_1/df9f2036e57a_1791312981.png', '/uploads/galleries/batch_1/df9f2036e57a_1791312981.png', NULL, NULL, '2026-10-06 18:56:21', NULL),
(37, 5, '/uploads/galleries/batch_1/d68bc4126149_1791312981.png', '/uploads/galleries/batch_1/d68bc4126149_1791312981.png', NULL, NULL, '2026-10-06 18:56:21', NULL),
(38, 5, '/uploads/galleries/batch_1/21664dbc1d69_1791312981.png', '/uploads/galleries/batch_1/21664dbc1d69_1791312981.png', NULL, NULL, '2026-10-06 18:56:21', NULL),
(39, 5, '/uploads/galleries/batch_1/07054e0b612b_1791312981.jpg', '/uploads/galleries/batch_1/07054e0b612b_1791312981.jpg', NULL, NULL, '2026-10-06 18:56:21', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `photo_galleries`
--

CREATE TABLE `photo_galleries` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL COMMENT 'Breve contexto sobre este lote de fotos',
  `cover_photo_id` int(11) DEFAULT NULL COMMENT 'Foto de capa escolhida (padrão: a primeira)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `photo_galleries`
--

INSERT INTO `photo_galleries` (`id`, `title`, `slug`, `description`, `cover_photo_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(4, 'Teste', 'teste', NULL, 16, '2026-10-06 02:37:59', '2026-10-06 18:55:07', NULL),
(5, 'Teste 2', 'teste-2', NULL, NULL, '2026-10-06 18:55:57', '2026-10-06 18:55:57', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `type` enum('news','event') NOT NULL DEFAULT 'news',
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` mediumtext NOT NULL,
  `cover_image_url` varchar(500) DEFAULT NULL,
  `event_date` datetime DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `posts`
--

INSERT INTO `posts` (`id`, `type`, `title`, `slug`, `content`, `cover_image_url`, `event_date`, `is_published`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'news', 'Teste de Notícia', 'teste-de-noticia', '<h2>Ocorreu uma notícia hoje!</h2><p><br></p><p>Hoje <strong>aconteceu </strong>algo <em>inesquecível</em>: <u>biblia carregador fone.</u></p><p><br></p>', '/uploads/posts/batch_1/1471cd316fcb_1791471138.JPG', NULL, 1, '2026-10-06 02:42:23', '2026-10-08 14:52:18', NULL),
(2, 'event', 'Quadrilha de Teste', 'quadrilha-de-teste', '<p>Sexta-feira dia 16/10 acontecerá a quadrilha 2026</p>', '/uploads/posts/batch_1/f0e07c96af18_1791471472.jpg', '2026-10-16 21:00:00', 1, '2026-10-08 14:57:52', '2026-10-08 14:57:52', NULL),
(3, 'event', 'Quadrilha teste passado', 'quadrilha-teste-passado', '<p>quinta feira dia 01/10 acontecerá a quadrilha 2026</p>', '/uploads/posts/batch_1/660210cec378_1791471525.jpg', '2026-10-01 21:00:00', 1, '2026-10-08 14:58:45', '2026-10-08 14:58:45', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','editor') NOT NULL DEFAULT 'editor',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Administrador', 'admin@ccmpt.com.br', '$2y$10$Ble0PWPFMmqYmZnW4huq5OeMrG9cZyZT4klfasoX8rj5q2iB9bLCG', 'admin', '2026-10-06 01:41:23', '2026-10-06 01:41:23', NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Índices de tabela `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `memorial_images`
--
ALTER TABLE `memorial_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`);

--
-- Índices de tabela `memorial_items`
--
ALTER TABLE `memorial_items`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `memorial_item_category`
--
ALTER TABLE `memorial_item_category`
  ADD PRIMARY KEY (`item_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Índices de tabela `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Índices de tabela `photos`
--
ALTER TABLE `photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gallery_id` (`gallery_id`);

--
-- Índices de tabela `photo_galleries`
--
ALTER TABLE `photo_galleries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Índices de tabela `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de tabela `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `memorial_images`
--
ALTER TABLE `memorial_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `memorial_items`
--
ALTER TABLE `memorial_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `photos`
--
ALTER TABLE `photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT de tabela `photo_galleries`
--
ALTER TABLE `photo_galleries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `memorial_images`
--
ALTER TABLE `memorial_images`
  ADD CONSTRAINT `memorial_images_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `memorial_items` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `memorial_item_category`
--
ALTER TABLE `memorial_item_category`
  ADD CONSTRAINT `memorial_item_category_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `memorial_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `memorial_item_category_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `photos`
--
ALTER TABLE `photos`
  ADD CONSTRAINT `photos_ibfk_1` FOREIGN KEY (`gallery_id`) REFERENCES `photo_galleries` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
