
# Dump of table media_folders
# ------------------------------------------------------------

DROP TABLE IF EXISTS `media_folders`;

CREATE TABLE `media_folders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `count` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;



# Dump of table media_library
# ------------------------------------------------------------

DROP TABLE IF EXISTS `media_library`;

CREATE TABLE `media_library` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `folder_id` int(11) DEFAULT NULL,
  `preview_image` varchar(200) DEFAULT NULL,
  `filename` varchar(200) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `data` text,
  PRIMARY KEY (`id`),
  KEY `folder_id` (`folder_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;



# Dump of table media_library_content
# ------------------------------------------------------------

DROP TABLE IF EXISTS `media_library_content`;

CREATE TABLE `media_library_content` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `media_id` int(11) DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `text` text CHARACTER SET utf8mb4,
  `lang` varchar(5) CHARACTER SET utf8mb4 DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `media_id` (`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;



# Dump of table media_linked_modules_albums
# ------------------------------------------------------------

DROP TABLE IF EXISTS `media_linked_modules_albums`;

CREATE TABLE `media_linked_modules_albums` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `album_id` int(11) DEFAULT NULL,
  `other_id` int(11) DEFAULT NULL,
  `module` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `type` varchar(20) CHARACTER SET utf8mb4 DEFAULT NULL,
  `sequence` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `other_id` (`other_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;



# Dump of table media_linked_album_media
# ------------------------------------------------------------

DROP TABLE IF EXISTS `media_linked_album_media`;

CREATE TABLE `media_linked_album_media` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `media_id` int(11) DEFAULT NULL,
  `album_id` int(11) DEFAULT NULL,
  `sequence` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `media_id` (`media_id`),
  KEY `album_id` (`album_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;



# Dump of table media_albums
# ------------------------------------------------------------

DROP TABLE IF EXISTS `media_albums`;

CREATE TABLE `media_albums` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(30) CHARACTER SET utf8mb4 DEFAULT NULL,
  `view` varchar(30) CHARACTER SET utf8mb4 DEFAULT NULL,
  `count` int(11) DEFAULT NULL,
  `data` text CHARACTER SET utf8mb4,
  `extra_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `extra_id` (`extra_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

