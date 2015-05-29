CREATE TABLE `{{db_sessions}}` (
    `session_id` varchar(40) NOT NULL,
    `session_user` int(8) NOT NULL DEFAULT '0',
    `session_ip` varchar(15) NOT NULL DEFAULT '',
    `session_browser` varchar(128) NOT NULL DEFAULT '',
    `session_time` int(11) NOT NULL DEFAULT '0'
    );

CREATE TABLE `{{db_users}}` (
    `user_id` INTEGER PRIMARY KEY,
    `user_login` varchar(32) NOT NULL UNIQUE,
    `user_name` varchar(40) NOT NULL,
    `user_password` varchar(40) NOT NULL,
    `user_lastvisit` int(8) NOT NULL
    );

CREATE TABLE `{{db_logs}}` (
    `log_id` INTEGER PRIMARY KEY,
    `log_time` int(11) NOT NULL,
    `log_message` text NOT NULL
    );

CREATE TABLE `{{db_news}}` (
    `news_id` INTEGER PRIMARY KEY,
    `news_removed` boolean NOT NULL,
    `news_user` varchar(32) NOT NULL,
    `news_ctime` int(11) NOT NULL,
    `news_mtime` int(11) NOT NULL,
    `news_meettime` int(11),
    `news_title` varchar(128) NOT NULL,
    `news_body` text NOT NULL
    );

CREATE TABLE `{{db_articles}}` (
    `article_id` INTEGER PRIMARY KEY,
    `article_public` boolean NOT NULL,
    `article_removed` boolean NOT NULL,
    `article_user` varchar(32) NOT NULL,
    `article_ctime` datetime NOT NULL,
    `article_mtime` datetime NOT NULL,
    `article_title` varchar(128) NOT NULL,
    `article_descr` text,
    `article_body` text NOT NULL,
    `article_bg` int(10)
    );

CREATE TABLE `{{db_albums}}` (
    `picture_categories_id` INTEGER PRIMARY KEY,
    `picture_categories_public` boolean NOT NULL,
    `picture_categories_user` varchar(32) NOT NULL,
    `picture_categories_ctime` datetime NOT NULL,
    `picture_categories_mtime` datetime NOT NULL,
    `picture_categories_title` varchar(128) NOT NULL,
    `picture_categories_descr` text,
    `picture_categories_picture_id` int(10)
    );

CREATE TABLE `{{db_pictures}}` (
    `picture_id` INTEGER PRIMARY KEY,
    `picture_category` int NOT NULL,
    `picture_user` varchar(32) NOT NULL,
    `picture_ctime` datetime NOT NULL,
    `picture_mtime` datetime NOT NULL,
    `picture_caption` text,
    `picture_filename` VARCHAR(255) NOT NULL,
    FOREIGN KEY(`picture_category`) REFERENCES `{{db_albums}}`(`picture_categories_id`)
                ON DELETE CASCADE ON UPDATE CASCADE
    );

INSERT INTO `{{db_users}}` VALUES (1, 'admin', 'Administrator', '{{default_pass}}', 1138562170);
INSERT INTO `{{db_users}}` VALUES (2, 'michal', 'Michał', '{{default_pass}}', 1138562170);

INSERT INTO `{{db_news}}` VALUES (NULL, 0, 'Michał', strftime('%s', 'now'), strftime('%s', 'now'), NULL, 'Tytuł 1', 'Przykładowy tekst 1');
INSERT INTO `{{db_news}}` VALUES (NULL, 0, 'Michał', strftime('%s', 'now'), strftime('%s', 'now'), NULL, 'Tytuł 2', 'Przykładowy tekst 2');
INSERT INTO `{{db_news}}` VALUES (NULL, 1, 'Michał', strftime('%s', 'now'), strftime('%s', 'now'), NULL, 'Tytuł 3', 'Przykładowy tekst 3');

INSERT INTO `{{db_articles}}` VALUES (1, 1, 0, 'Michał', date('now'), date('now'), 'O nas', NULL, 'Tu będzie część o nas', NULL);
INSERT INTO `{{db_articles}}` VALUES (2, 1, 0, 'Michał', date('now'), date('now'), 'Kontakt', NULL, 'Dane kontaktowe', NULL);
INSERT INTO `{{db_articles}}` VALUES (NULL, 1, 0, 'Michał', date('now'), date('now'), 'Przykładowy artykuł', 'Opis art.', 'Treść artykułu', NULL);
INSERT INTO `{{db_articles}}` VALUES (NULL, 0, 0, 'Michał', date('now'), date('now'), 'Przykładowy usunięty artykuł', 'Opis art.', 'Treść artykułu', NULL);

INSERT INTO `{{db_albums}}` VALUES (1, 0, 'Michał', date('now'), date('now'), 'Kosz', 'Zawiera usunięte zdjęcia', NULL);
