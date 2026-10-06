-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 06/10/2026 às 04:46
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

-- --------------------------------------------------------

--
-- Estrutura para tabela `memorial_images`
--

CREATE TABLE `memorial_images` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `image_url` varchar(500) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `memorial_items`
--

CREATE TABLE `memorial_items` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `historical_description` text NOT NULL,
  `main_image_url` varchar(500) NOT NULL COMMENT 'URL do Firebase/Storage',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `content` text NOT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `show_in_menu` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pages`
--

INSERT INTO `pages` (`id`, `title`, `slug`, `content`, `is_published`, `show_in_menu`, `updated_at`) VALUES
(1, 'A Instituição', 'instituicao', '<p>O Centro Cultural e Memorial Padre Tiago foi idealizado para preservar a memória...</p>', 1, 1, '2026-10-06 01:43:07'),
(2, 'História do Padre Tiago', 'historia', '<p>Nascido na Itália, Padre Tiago dedicou sua vida...</p>', 1, 1, '2026-10-06 01:43:07'),
(3, 'Projetos Sociais', 'projetos', '<p>Nossos projetos envolvem escolinha de futebol, aulas de música...</p>', 1, 1, '2026-10-06 01:43:07');

-- --------------------------------------------------------

--
-- Estrutura para tabela `photos`
--

CREATE TABLE `photos` (
  `id` int(11) NOT NULL,
  `gallery_id` int(11) NOT NULL,
  `image_path` varchar(500) NOT NULL COMMENT 'Caminho local da foto em WebP (ex: max 1920px)',
  `thumbnail_path` varchar(500) NOT NULL COMMENT 'Caminho local da miniatura em WebP (ex: 400px)',
  `tags` varchar(255) DEFAULT NULL COMMENT 'Palavras-chave separadas por vírgula (ex: Igreja Matriz, Batizado)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `photos`
--

INSERT INTO `photos` (`id`, `gallery_id`, `image_path`, `thumbnail_path`, `tags`, `created_at`, `deleted_at`) VALUES
(5, 4, '/uploads/galleries/batch_1/bca208eb0832_1791254280.png', '/uploads/galleries/batch_1/bca208eb0832_1791254280.png', NULL, '2026-10-06 02:38:00', NULL),
(6, 4, '/uploads/galleries/batch_1/e942d66f3e3f_1791254280.jpeg', '/uploads/galleries/batch_1/e942d66f3e3f_1791254280.jpeg', NULL, '2026-10-06 02:38:00', NULL),
(7, 4, '/uploads/galleries/batch_1/2abed95e18c7_1791254280.png', '/uploads/galleries/batch_1/2abed95e18c7_1791254280.png', NULL, '2026-10-06 02:38:00', NULL),
(8, 4, '/uploads/galleries/batch_1/7d2245bf08fb_1791254280.jpg', '/uploads/galleries/batch_1/7d2245bf08fb_1791254280.jpg', NULL, '2026-10-06 02:38:00', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `photo_galleries`
--

CREATE TABLE `photo_galleries` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL COMMENT 'Breve contexto sobre este lote de fotos',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `photo_galleries`
--

INSERT INTO `photo_galleries` (`id`, `title`, `slug`, `description`, `created_at`, `updated_at`, `deleted_at`) VALUES
(4, 'Teste', 'teste', NULL, '2026-10-06 02:37:59', '2026-10-06 02:37:59', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `type` enum('news','event') NOT NULL DEFAULT 'news',
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` text NOT NULL,
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
(1, 'news', 'Teste de Notícia', 'teste-de-noticia', '<h2>Ocorreu uma notícia hoje!</h2><p><br></p><p>Hoje <strong>aconteceu </strong>algo <em>inesquecível</em>: <u>biblia carregador fone.</u></p><p><br></p>', NULL, NULL, 1, '2026-10-06 02:42:23', '2026-10-06 02:42:23', NULL);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `memorial_images`
--
ALTER TABLE `memorial_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `memorial_items`
--
ALTER TABLE `memorial_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `photos`
--
ALTER TABLE `photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `photo_galleries`
--
ALTER TABLE `photo_galleries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
