-- Table structure for table `articles`
CREATE TABLE IF NOT EXISTS `articles` (
	`id` int UNSIGNED NOT NULL AUTO_INCREMENT,
	`title` varchar(48) NOT NULL,
	`header` text NOT NULL,
	`content` text NOT NULL,
	`category_id` smallint UNSIGNED NOT NULL,
	`user_id` smallint UNSIGNED NOT NULL,
	`created` datetime NOT NULL,
	`modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
) ENGINE=INNODB;

INSERT INTO `articles` (`id`, `title`, `header`, `content`, `category_id`, `user_id`, `created`, `modified`) VALUES
(1, 'Un article de test', 'Le chapô du 1er article.', 'Le contenu du premier article, avec un peu de texte en plus pour meubler.', 2, 1, '2017-09-19 22:11:56', '2017-09-21 21:12:20'),
(2, 'A second test article', 'The header of article number 2.', 'The content of the second article. I also add some dummy text here.', 1, 1, '2017-09-19 22:42:31', '2017-09-19 22:48:07');

-- Table structure for table `categories`
CREATE TABLE IF NOT EXISTS `categories` (
	`id` smallint UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` varchar(256) NOT NULL,
	`created` datetime NOT NULL,
	`modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
) ENGINE=INNODB;

INSERT INTO `categories` (`id`, `name`, `created`, `modified`) VALUES
(1, 'News', '2017-09-19 21:55:07', '2017-09-19 21:55:07'),
(2, 'Tutorials', '2017-09-19 21:56:11', '2017-09-22 01:13:15');

-- Table structure for table `users`
CREATE TABLE IF NOT EXISTS `users` (
	`id` int UNSIGNED NOT NULL AUTO_INCREMENT,
	`username` varchar(40) NOT NULL,
	`email` varchar(128) NOT NULL,
	`password` varchar(128) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=INNODB;

INSERT INTO `users` (`id`, `username`, `email`, `password`) VALUES
(1, 'Julien', 'julien.helfer@gmail.com', '123456');