
DROP TABLE IF EXISTS `media_albums`;

CREATE TABLE `media_albums` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(30) DEFAULT NULL,
  `view` varchar(30) DEFAULT NULL,
  `count` int(11) DEFAULT NULL,
  `data` text,
  `extra_id` int(11) DEFAULT NULL,
  `meta_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `extra_id` (`extra_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;



# Dump of table media_allowed_file_types
# ------------------------------------------------------------

DROP TABLE IF EXISTS `media_allowed_file_types`;

CREATE TABLE `media_allowed_file_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `extension` varchar(10) DEFAULT NULL,
  `mimetype` varchar(255) DEFAULT NULL,
  `type` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `media_allowed_file_types` WRITE;
/*!40000 ALTER TABLE `media_allowed_file_types` DISABLE KEYS */;

INSERT INTO `media_allowed_file_types` (`id`, `extension`, `mimetype`, `type`)
VALUES
  (1,'doc','application/msword','file'),
  (2,'dot','application/msword','file'),
  (3,'docx','application/vnd.openxmlformats-officedocument.wordprocessingml.document','file'),
  (4,'dotx','application/vnd.openxmlformats-officedocument.wordprocessingml.template','file'),
  (5,'docm','application/vnd.ms-word.document.macroEnabled.12','file'),
  (6,'dotm','application/vnd.ms-word.template.macroEnabled.12','file'),
  (7,'xls','application/vnd.ms-excel','file'),
  (8,'xlt','application/vnd.ms-excel','file'),
  (9,'xla','application/vnd.ms-excel','file'),
  (10,'xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','file'),
  (11,'xltx','application/vnd.openxmlformats-officedocument.spreadsheetml.template','file'),
  (12,'xlsm','application/vnd.ms-excel.sheet.macroEnabled.12','file'),
  (13,'ppt','application/vnd.ms-powerpoint','file'),
  (14,'pot','application/vnd.ms-powerpoint','file'),
  (15,'pps','application/vnd.ms-powerpoint','file'),
  (16,'ppa','application/vnd.ms-powerpoint','file'),
  (17,'pptx',' application/vnd.openxmlformats-officedocument.presentationml.presentation','file'),
  (18,'potx','application/vnd.openxmlformats-officedocument.presentationml.template','file'),
  (19,'pdf','application/pdf','file'),
  (20,'zip','application/zip','file'),
  (21,'jpeg','image/jpeg','image'),
  (22,'png','image/png','image'),
  (23,'jpg','image/jpeg','image'),
  (24,'gif','image/gif','image'),
  (25,'mp4','application/mp4','video');

/*!40000 ALTER TABLE `media_allowed_file_types` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table media_folders
# ------------------------------------------------------------

DROP TABLE IF EXISTS `media_folders`;

CREATE TABLE `media_folders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `count` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `module` varchar(50) DEFAULT NULL,
  `sequence` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

# Dump of table media_library
# ------------------------------------------------------------

DROP TABLE IF EXISTS `media_library`;

CREATE TABLE `media_library` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `folder_id` int(11) DEFAULT NULL,
  `preview_image` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `filename` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `original_filename` varchar(100) DEFAULT NULL,
  `type` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `data` text CHARACTER SET latin1,
  `created_on` datetime DEFAULT NULL,
  `edited_on` datetime DEFAULT NULL,
  `modified` enum('N','Y') DEFAULT NULL,
  `extension` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `folder_id` (`folder_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;


# Dump of table media_library_content
# ------------------------------------------------------------

DROP TABLE IF EXISTS `media_library_content`;

CREATE TABLE `media_library_content` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `media_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `text` text,
  `language` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `media_id` (`media_id`)
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



# Dump of table media_linked_modules_albums
# ------------------------------------------------------------

DROP TABLE IF EXISTS `media_linked_modules_albums`;

CREATE TABLE `media_linked_modules_albums` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `album_id` int(11) DEFAULT NULL,
  `other_id` int(11) DEFAULT NULL,
  `module` varchar(50) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `sequence` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `other_id` (`other_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;



# Dump of table media_resolutions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `media_resolutions`;

CREATE TABLE `media_resolutions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `crop` enum('N','Y') DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;