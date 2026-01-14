-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Янв 14 2026 г., 13:11
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `filmhub`
--

-- --------------------------------------------------------

--
-- Структура таблицы `comments`
--

CREATE TABLE `comments` (
  `id` int(10) UNSIGNED NOT NULL,
  `movie_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `text` text NOT NULL,
  `status` enum('visible','hidden') NOT NULL DEFAULT 'visible',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `comments`
--

INSERT INTO `comments` (`id`, `movie_id`, `user_id`, `text`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'Визуал до сих пор выглядит мощно. Мир Пандоры — топ.', 'visible', '2026-01-14 14:03:53', '2026-01-14 14:03:53'),
(2, 2, 1, 'Сильная история выживания. Постановка и атмосфера — огонь.', 'visible', '2026-01-14 14:03:53', '2026-01-14 14:03:53'),
(3, 3, 2, 'Очень люблю вайб 80-х и музыку. Сезон 1 — классика.', 'visible', '2026-01-14 14:03:53', '2026-01-14 14:03:53'),
(4, 4, 1, 'Гениально закручено, но смотреть лучше внимательно :)', 'visible', '2026-01-14 14:03:53', '2026-01-14 14:03:53'),
(5, 5, 2, 'Один из лучших сериалов: персонажи и развитие — 10/10.', 'visible', '2026-01-14 14:03:53', '2026-01-14 14:03:53'),
(6, 6, 2, 'Динамично и интересно, особенно первая половина фильма.', 'visible', '2026-01-14 14:03:53', '2026-01-14 14:03:53'),
(7, 7, 1, 'Лёгкое приключение, как попкорн-кино — заходит.', 'visible', '2026-01-14 14:03:53', '2026-01-14 14:03:53'),
(8, 8, 2, 'Одиночество в пустом городе — очень сильное ощущение.', 'visible', '2026-01-14 14:03:53', '2026-01-14 14:03:53'),
(9, 9, 1, 'Супернапряжение до конца. Один из лучших зомби-фильмов.', 'visible', '2026-01-14 14:03:53', '2026-01-14 14:03:53'),
(10, 10, 2, 'Классика, которую можно пересматривать. Финал всегда бьёт.', 'visible', '2026-01-14 14:03:53', '2026-01-14 14:03:53');

-- --------------------------------------------------------

--
-- Структура таблицы `favorites`
--

CREATE TABLE `favorites` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `movie_id` int(10) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `favorites`
--

INSERT INTO `favorites` (`id`, `user_id`, `movie_id`, `created_at`) VALUES
(1, 2, 5, '2026-01-14 14:03:53'),
(2, 2, 4, '2026-01-14 14:03:53'),
(3, 2, 1, '2026-01-14 14:03:53'),
(4, 1, 2, '2026-01-14 14:03:53'),
(5, 1, 9, '2026-01-14 14:03:53'),
(6, 1, 10, '2026-01-14 14:03:53');

-- --------------------------------------------------------

--
-- Структура таблицы `genres`
--

CREATE TABLE `genres` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(80) NOT NULL,
  `slug` varchar(90) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `genres`
--

INSERT INTO `genres` (`id`, `name`, `slug`, `created_at`) VALUES
(1, 'Фантастика', 'sci-fi', '2026-01-14 14:03:53'),
(2, 'Приключения', 'adventure', '2026-01-14 14:03:53'),
(3, 'Драма', 'drama', '2026-01-14 14:03:53'),
(4, 'Триллер', 'thriller', '2026-01-14 14:03:53'),
(5, 'Ужасы', 'horror', '2026-01-14 14:03:53'),
(6, 'Криминал', 'crime', '2026-01-14 14:03:53'),
(7, 'Романтика', 'romance', '2026-01-14 14:03:53'),
(8, 'Пост-апокалипсис', 'post-apoc', '2026-01-14 14:03:53'),
(9, 'Зомби', 'zombie', '2026-01-14 14:03:53'),
(10, 'Мистика', 'mystery', '2026-01-14 14:03:53');

-- --------------------------------------------------------

--
-- Структура таблицы `movies`
--

CREATE TABLE `movies` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `original_title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `year` smallint(5) UNSIGNED DEFAULT NULL,
  `duration_minutes` smallint(5) UNSIGNED DEFAULT NULL,
  `country` varchar(120) DEFAULT NULL,
  `director` varchar(120) DEFAULT NULL,
  `poster` varchar(255) DEFAULT NULL,
  `youtube_trailer_id` varchar(32) DEFAULT NULL,
  `genre_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `movies`
--

INSERT INTO `movies` (`id`, `title`, `original_title`, `description`, `year`, `duration_minutes`, `country`, `director`, `poster`, `youtube_trailer_id`, `genre_id`, `created_at`, `updated_at`) VALUES
(1, 'Avatar', 'Avatar', 'Эпическое научно-фантастическое приключение на планете Пандора, где столкновение цивилизаций меняет судьбы героев.', 2009, 162, 'USA', 'James Cameron', 'img/movies/avatar_2009.jpg', '5PSNL1qE6VY', 1, '2026-01-14 14:03:53', '2026-01-14 14:03:53'),
(2, 'The Last of Us', 'The Last of Us', 'История выживания после пандемии мутировавшего грибка: контрабандист и девочка пересекают разрушенную Америку.', 2023, 55, 'USA', 'Craig Mazin, Neil Druckmann', 'img/movies/the_last_of_us_2023.jpg', 'uLtkt8BonwM', 8, '2026-01-14 14:03:53', '2026-01-14 14:03:53'),
(3, 'Stranger Things', 'Stranger Things', 'Подростки сталкиваются с паранормальными явлениями и тайнами маленького города.', 2016, 55, 'USA', 'The Duffer Brothers', 'img/movies/stranger_things_2016.jpg', 'b9EkMc79ZSU', 10, '2026-01-14 14:03:53', '2026-01-14 14:03:53'),
(4, 'Dark', 'Dark', 'Исчезновение детей в маленьком городке приводит к раскрытию семейных и временных тайн.', 2017, 55, 'Germany', 'Baran bo Odar', 'img/movies/dark_2017.jpg', 'ESEUoa-mz2c', 4, '2026-01-14 14:03:53', '2026-01-14 14:03:53'),
(5, 'Breaking Bad', 'Breaking Bad', 'Учитель химии становится производителем метамфетамина и постепенно теряет прежнюю жизнь.', 2008, 50, 'USA', 'Vince Gilligan', 'img/movies/breaking_bad_2008.jpg', 'VaOt6tXyf2Y', 6, '2026-01-14 14:03:53', '2026-01-14 14:03:53'),
(6, 'The Maze Runner', 'The Maze Runner', 'Подростки оказываются в лабиринте и ищут путь к свободе, раскрывая правила жестокой системы.', 2014, 113, 'USA', 'Wes Ball', 'img/movies/maze_runner_2014.jpg', 'T4OfVYoWkoA', 1, '2026-01-14 14:03:53', '2026-01-14 14:03:53'),
(7, 'Uncharted', 'Uncharted', 'Приключения охотника за сокровищами: поиски легендарного клада и опасные предательства.', 2022, 116, 'USA', 'Ruben Fleischer', 'img/movies/uncharted_2022.jpg', 'eHp3MbsCbMg', 2, '2026-01-14 14:03:53', '2026-01-14 14:03:53'),
(8, 'I Am Legend', 'I Am Legend', 'Единственный выживший после пандемии пытается найти лекарство и удержать надежду.', 2007, 101, 'USA', 'Francis Lawrence', 'img/movies/i_am_legend_2007.jpg', 'dtKMEAXyPkg', 8, '2026-01-14 14:03:53', '2026-01-14 14:03:53'),
(9, 'Train to Busan', 'Train to Busan', 'Пассажиры поезда борются с зомби-эпидемией, когда безопасных мест почти не остаётся.', 2016, 118, 'South Korea', 'Yeon Sang-ho', 'img/movies/train_to_busan_2016.jpg', '1ovgxN2VWNc', 9, '2026-01-14 14:03:53', '2026-01-14 14:03:53'),
(10, 'Titanic', 'Titanic', 'Трагедия затонувшего лайнера и история любви на фоне катастрофы.', 1997, 195, 'USA', 'James Cameron', 'img/movies/titanic_1997.jpg', 'LuPB43YSgCs', 7, '2026-01-14 14:03:53', '2026-01-14 14:03:53');

-- --------------------------------------------------------

--
-- Структура таблицы `movie_rating_stats`
--

CREATE TABLE `movie_rating_stats` (
  `movie_id` int(10) UNSIGNED NOT NULL,
  `rating_avg` decimal(6,2) NOT NULL DEFAULT 0.00,
  `rating_count` bigint(20) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `movie_rating_stats`
--

INSERT INTO `movie_rating_stats` (`movie_id`, `rating_avg`, `rating_count`) VALUES
(2, 9.00, 1),
(4, 9.00, 1),
(5, 10.00, 1),
(9, 8.00, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `ratings`
--

CREATE TABLE `ratings` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `movie_id` int(10) UNSIGNED NOT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Дамп данных таблицы `ratings`
--

INSERT INTO `ratings` (`id`, `user_id`, `movie_id`, `rating`, `created_at`, `updated_at`) VALUES
(1, 2, 5, 10, '2026-01-14 14:03:53', '2026-01-14 14:03:53'),
(2, 2, 4, 9, '2026-01-14 14:03:53', '2026-01-14 14:03:53'),
(3, 1, 2, 9, '2026-01-14 14:03:53', '2026-01-14 14:03:53'),
(4, 1, 9, 8, '2026-01-14 14:03:53', '2026-01-14 14:03:53');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `login` varchar(50) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `avatar` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `login`, `email`, `password`, `role`, `avatar`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@filmhub.local', '$2b$10$ZX667gYbd6ww/Uxs6HltL.4rMBjykC1IGwc2tx.RQ1XpVR1VaD/oC', 'admin', NULL, '2026-01-14 14:03:53', '2026-01-14 14:03:53'),
(2, 'vlad', 'vlad@filmhub.local', '$2b$10$oZOXGgryMakZF5O6t5gIwux38.1vFHOKeS2Hs1tVGdrE42fNhTU.y', 'user', NULL, '2026-01-14 14:03:53', '2026-01-14 14:03:53');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_comments_movie_id` (`movie_id`),
  ADD KEY `idx_comments_user_id` (`user_id`),
  ADD KEY `idx_comments_status` (`status`);

--
-- Индексы таблицы `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_favorites_user_movie` (`user_id`,`movie_id`),
  ADD KEY `idx_favorites_user_id` (`user_id`),
  ADD KEY `idx_favorites_movie_id` (`movie_id`);

--
-- Индексы таблицы `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_genres_slug` (`slug`),
  ADD UNIQUE KEY `uq_genres_name` (`name`);

--
-- Индексы таблицы `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_movies_title_year` (`title`,`year`),
  ADD KEY `idx_movies_genre_id` (`genre_id`),
  ADD KEY `idx_movies_year` (`year`),
  ADD KEY `idx_movies_title` (`title`);

--
-- Индексы таблицы `movie_rating_stats`
--
ALTER TABLE `movie_rating_stats`
  ADD PRIMARY KEY (`movie_id`);

--
-- Индексы таблицы `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_ratings_user_movie` (`user_id`,`movie_id`),
  ADD KEY `idx_ratings_movie_id` (`movie_id`),
  ADD KEY `idx_ratings_user_id` (`user_id`),
  ADD KEY `idx_ratings_rating` (`rating`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_login` (`login`),
  ADD UNIQUE KEY `uq_users_email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `genres`
--
ALTER TABLE `genres`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `movies`
--
ALTER TABLE `movies`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `fk_comments_movie` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `fk_favorites_movie` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_favorites_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `movies`
--
ALTER TABLE `movies`
  ADD CONSTRAINT `fk_movies_genre` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `movie_rating_stats`
--
ALTER TABLE `movie_rating_stats`
  ADD CONSTRAINT `fk_stats_movie` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `fk_ratings_movie` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ratings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
