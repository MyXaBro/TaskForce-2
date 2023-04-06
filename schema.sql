CREATE DATABASE TaskForce;

USE TaskForce;

-- Доступ ко всем правам бд --
GRANT ALL PRIVILEGES ON taskforce.* TO 'root'@'localhost';

-- Добавление колонок --
ALTER TABLE TaskForce.categories
    ADD id INT AUTO_INCREMENT NOT NULL PRIMARY KEY;


-- ----------------------------
-- Table structure for categories
-- ----------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
                              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                              `name` varchar(255) NOT NULL,
                              `icon` varchar(16),
                              PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for cities
-- ----------------------------
DROP TABLE IF EXISTS `cities`;
CREATE TABLE `cities` (
                          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                          `name` varchar(255) NOT NULL,
                          `lat` varchar(16),
                          `long` varchar(16),
                          PRIMARY KEY (`id`),
                          UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- ----------------------------
-- Table structure for files
-- ----------------------------
DROP TABLE IF EXISTS `files`;
CREATE TABLE `files` (
                         `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                         `name` varchar(255) NOT NULL,
                         `path` varchar(255) NOT NULL,
                         `task_id` int(11) unsigned NOT NULL,
                         `user_id` int(11) unsigned NOT NULL,
                         `dt_add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                         PRIMARY KEY (`id`),
                         UNIQUE KEY `path` (`path`),
                         KEY `fk_files_tasks_1` (`task_id`),
                         KEY `fk_files_users_1` (`user_id`),
                         CONSTRAINT `fk_files_tasks_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`),
                         CONSTRAINT `fk_files_users_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- ----------------------------
-- Table structure for opinions
-- ----------------------------
DROP TABLE IF EXISTS `opinions`;
CREATE TABLE `opinions` (
                            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                            `owner_id` int(11) unsigned NOT NULL,
                            `task_id` int(11) unsigned NOT NULL,
                            `performer_id` int(11) unsigned NOT NULL,
                            `rate` tinyint(1) unsigned NOT NULL,
                            `description` text NOT NULL,
                            `dt_add` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                            PRIMARY KEY (`id`),
                            KEY `fk_opinions_users_1` (`owner_id`),
                            KEY `fk_opinions_users_2` (`performer_id`),
                            CONSTRAINT `fk_opinions_users_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`),
                            CONSTRAINT `fk_opinions_users_2` FOREIGN KEY (`performer_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for replies
-- ----------------------------
DROP TABLE IF EXISTS `replies`;
CREATE TABLE `replies` (
                           `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                           `user_id` int(11) unsigned NOT NULL,
                           `dt_add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                           `description` varchar(255) NOT NULL,
                           `task_id` int(11) unsigned NOT NULL,
                           `is_approved` tinyint(1) unsigned DEFAULT '0',
                           `budget` int(10) unsigned DEFAULT NULL,
                           PRIMARY KEY (`id`),
                           KEY `fk_replies_users_1` (`user_id`),
                           KEY `fk_replies_tasks_1` (`task_id`),
                           CONSTRAINT `fk_replies_tasks_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`),
                           CONSTRAINT `fk_replies_users_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for statuses
-- ----------------------------
DROP TABLE IF EXISTS `statuses`;
CREATE TABLE `statuses` (
                            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                            `name` varchar(255) NOT NULL,
                            PRIMARY KEY (`id`),
                            UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tasks
-- ----------------------------
DROP TABLE IF EXISTS `tasks`;
CREATE TABLE `tasks` (
                         `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                         `name` varchar(255) NOT NULL,
                         `category_id` int(11) unsigned NOT NULL,
                         `description` text NOT NULL,
                         `location` varchar(255) DEFAULT NULL,
                         `budget` int(10) unsigned DEFAULT NULL,
                         `expire_dt` datetime DEFAULT NULL,
                         `dt_add` datetime DEFAULT CURRENT_TIMESTAMP,
                         `client_id` int(10) unsigned NOT NULL,
                         `performer_id` int(10) unsigned DEFAULT NULL,
                         `status_id` int(11) unsigned NOT NULL,
                         PRIMARY KEY (`id`),
                         KEY `fk_tasks_categories_1` (`category_id`),
                         KEY `fk_tasks_statuses_1` (`status_id`),
                         CONSTRAINT `fk_tasks_categories_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
                         CONSTRAINT `fk_tasks_statuses_1` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
                         `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                         `email` varchar(255) NOT NULL,
                         `name` varchar(255) NOT NULL,
                         `city_id` int(11) unsigned NOT NULL,
                         `password` char(64) NOT NULL,
                         `dt_add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                         PRIMARY KEY (`id`),
                         UNIQUE KEY `email` (`email`),
                         KEY `fk_users_cities_1` (`city_id`),
                         CONSTRAINT `fk_users_cities_1` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for user_categories
-- ----------------------------
DROP TABLE IF EXISTS `user_categories`;
CREATE TABLE `user_categories` (
                                   `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                                   `user_id` int(11) unsigned NOT NULL,
                                   `category_id` int(11) unsigned NOT NULL,
                                   PRIMARY KEY (`id`),
                                   KEY `fk_user_categories_users_1` (`user_id`),
                                   KEY `fk_user_categories_categories_1` (`category_id`),
                                   CONSTRAINT `fk_user_categories_categories_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
                                   CONSTRAINT `fk_user_categories_users_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for user_settings
-- ----------------------------
DROP TABLE IF EXISTS `user_settings`;
CREATE TABLE `user_settings` (
                                 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                                 `address` varchar(255) DEFAULT NULL,
                                 `bd` date DEFAULT NULL,
                                 `avatar_path` varchar(255) DEFAULT NULL,
                                 `about` text,
                                 `phone` char(11) DEFAULT NULL,
                                 `skype` char(32) DEFAULT NULL,
                                 `messenger` char(32) DEFAULT NULL,
                                 `notify_new_msg` tinyint(1) unsigned DEFAULT '0',
                                 `notify_new_action` tinyint(1) unsigned DEFAULT '0',
                                 `notify_new_reply` tinyint(1) unsigned DEFAULT '0',
                                 `opt_hide_contacts` tinyint(1) unsigned DEFAULT '0',
                                 `opt_hide_me` tinyint(1) unsigned DEFAULT '0',
                                 `is_performer` tinyint(1) unsigned DEFAULT '0',
                                 `user_id` int(11) unsigned NOT NULL,
                                 PRIMARY KEY (`id`),
                                 UNIQUE KEY `fk_user_settings_users_1` (`user_id`) USING BTREE,
                                 UNIQUE KEY `phone` (`phone`,`skype`,`messenger`),
                                 CONSTRAINT `fk_user_settings_users_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET FOREIGN_KEY_CHECKS=1;

-- Добавление рандомных значений --
INSERT INTO TaskForce.users (email, name, city_id, password) VALUES ('gislason.okey@hotmail.com', 'Dr. Sammie Rice', 1, '$2y$13$KkKvcELeIPpG3A1VjfU5/.S5sVOKBZOG6BGxVBYmBXvvagPzA/nZC'),
                                                            ('allan.reilly@gmail.com', 'Jermaine Vandervort', 4, '$2y$13$ChcoegWmA9i6oclxBqfWTeWqbgEr6tYU7gjTJ26SIYgk2yGXjpelm'),
                                                            ('rusty83@hotmail.com', 'Rico Christiansen', 4, '$2y$13$2mL4FJtD8r7TLeJrMhU4n.9LJ5ZqXjt/SF9dPs96WZsa3Sx5xT1.O'),
                                                            ('rozella.bergnaum@herzog.org', 'Mr. Eloy Hettinger', 7, '$2y$13$fvskZT5OBnOTUSfTNNJBZe47PlEdZi2GDgZJx6pgHst9sA7BRdtbW'),
                                                            ('amoen@hotmail.com', 'Kim Strosin', 4, '$2y$13$aZTVbxWWOJyhugTV9Upv6OLzlMHhdG6GDGttZHGPscTr4p5.jte/K'),
                                                            ('bayer.iliana@hotmail.com', 'Monty Bahringer', 10, '$2y$13$Gcdybvik.Nb0KkBk0buzYeik/lqYyseivSwr3gItL39drE5B6ksJ.'),
                                                            ('moen.camryn@blanda.info', 'Chadd McKenzie', 2, '$2y$13$YIb/Hi60VAChDeNZi2nA7e97PyZ9nqW7NAyGNKo7AwkrXgz1OPloi'),
                                                            ('estelle52@hotmail.com', 'Dr. Lottie Muller',8, '$2y$13$YRRYZJyVVh8oJlYcVgGobemxkwnCQm3kIolot4ApN20Ypfa7iRkS6'),
                                                            ('joyce35@macejkovic.com', 'Maynard Armstrong', 8, '$2y$13$F1gvT2EQ5Vd672DRYhlb/uuA6JaihAarir3KWyVWXj6kShaOPaf4y'),
                                                            ('bins.gabe@hotmail.com', 'Ronaldo Cassin', 4, '$2y$13$chhThFkAe8N4.8dOlU3g9.LBlP4LD1MwDr7r02.MUW70.KzvjkcGG');

INSERT INTO tasks (name, category_id, description, budget, dt_add, client_id, expire_dt, status_id)
VALUES
    ('Cumque velit consequatur quis quas voluptatum doloremque laborum.', 3, 'Чичикова в сени, куда вышел уже сам хозяин. Увидев гостя, он сказал отрывисто: «Прошу» — и потом прибавил: «А любопытно бы знать, чьих она? что, как ее выручить. Наконец, выдернувши ее потихоньку.', 7.85, '2021-03-21', 8, '2021-04-17', 1),
    ('Est eum molestiae odio deleniti nulla qui excepturi.', 5, 'Какое-то время послал бог: гром такой — у Хвостырева… — Чичиков, впрочем, отроду не видел ни каурой кобылы, — ни искренности! совершенный Собакевич, такой подлец! — Да вот теперь у тебя были собаки.', 5.56, '2021-04-02', 2, '2021-04-21', 1),
    ('Reprehenderit dolores et et corporis.', 2, 'По крайней мере хоть пятьдесят! Чичиков стал было отговариваться, что нет; но Собакевич отвечал просто: — Мне кажется, вы затрудняетесь?.. — заметил зять. — Ну, послушай, чтоб доказать тебе, что я.', 8.13, '2021-03-24', 10, '2021-04-26', 1),
    ('Nisi voluptas dolores non labore cumque.', 3, 'Вид оживляли две бабы, которые, картинно подобравши платья и подтыкавшись со всех сторон, брели по колени в пруде, влача за два рубля в сутки проезжающие получают покойную комнату с тараканами.', 2.06, '2021-04-10', 2, '2021-05-02', 1),
    ('Expedita esse ipsam est quisquam ea.', 5, 'России есть теперь весьма много почтенных людей, которые числятся теперь — живущими? Что это за люди? мухи, а не для просьб. Впрочем, чтобы успокоить ее, он дал ей медный грош, и она побрела.', 5.60, '2021-03-22', 1, '2021-04-18', 1);

INSERT INTO `statuses` (`id`, `name`) VALUES ('1', 'открыто');

-- Изменение данных в таблице --
UPDATE statuses SET name = 'Новое' WHERE id = 1;

INSERT INTO replies (user_id, task_id, description, dt_add, budget) VALUES
                                                                        ('4', '1', 'Эк уморила как проклятая старуха» — «сказал он, немного отдохнувши, и отпер шкатулку. Автор уверен, что «есть читатели такие любопытные, которые пожелают даже узнать план и «внутреннее расположение.','2021-03-25',4675),
                                                                        ('5', '2', 'Кувшинниковым. «Да, — подумал про себя Чичиков, садясь. в бричку. С громом выехала бричка из-под ворот гостиницы на улицу. Проходивший поп снял шляпу, несколько мальчишек в замаранных рубашках.','2021-03-30',8389),
                                                                        ('7', '3', 'Хвостырева. — Барин! ничего не отвечал. — Прощайте, мои крошки. Вы — давайте настоящую цену! «Ну, уж черт его побери, — подумал Чичиков, — сказал Собакевич. Засим, подошевши к столу, где была.','2021-04-16',2816),
                                                                        ('1', '4', 'Пожалуй, почему же не «удовлетворить! Вот оно, внутреннее расположение: в самой комнате тяжелый храп и тяжкая одышка разгоряченных — коней остановившейся тройки. Все невольно глянули в окно: кто-то.','2021-04-01',3998),
                                                                        ('6', '5', 'Чичиков, подвигая шашку. — Знаем мы вас, как вы — думаете, а так, по наклонности собственных мыслей. Два с полтиною. — Право у вас умерло крестьян? — А у нас просто, по — искренности происходит.','2021-04-15',2135),
                                                                        ('3', '5', 'Но не сгорит платье и не подумал — вычесать его? — В театре одна актриса так, каналья, пела, как канарейка! — Кувшинников, который сидел возле меня, «Вот, говорит, брат, — говорил Ноздрев, горячась.','2021-04-04',9265);

INSERT INTO opinions (description, rate, dt_add, owner_id, task_id, performer_id) VALUES
                                                                                      ('Voluptas atque autem doloribus enim id.', 2, '2021-04-08', 6, 19, 3),
                                                                                      ('Ratione et sunt laboriosam non iure.', 1, '2021-04-18', 6, 9, 7),
                                                                                      ('Voluptatem ullam vel qui.', 1, '2021-04-18', 5, 13, 10),
                                                                                      ('Voluptatum quis illum laudantium est dolor.', 5, '2021-04-03', 3, 17, 8),
                                                                                      ('Sunt occaecati molestias minima enim.', 4, '2021-04-05', 2, 24, 7),
                                                                                      ('Neque quisquam repellendus architecto quo.', 5, '2021-04-08', 4, 18, 7),
                                                                                      ('Nostrum qui sit voluptas voluptates tempora et.', 2, '2021-04-02', 1, 4, 8),
                                                                                      ('Quis fugiat quo fuga non.', 5, '2021-04-07', 10, 2, 7),
                                                                                      ('Quam dolores laboriosam hic porro similique dolor repellendus.', 2, '2021-04-02', 6, 9, 10),
                                                                                      ('Eligendi deleniti ut occaecati ut autem.', 5, '2021-04-30', 2, 11, 10),
                                                                                      ('Enim sint quia rerum harum.', 4, '2021-04-07', 1, 25, 9);

ALTER TABLE `tasks`
    ADD CONSTRAINT `fk_tasks_users`
        FOREIGN KEY (`performer_id`) REFERENCES `users` (`id`)
            ON DELETE CASCADE;
