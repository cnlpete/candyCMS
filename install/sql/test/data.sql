LOCK TABLES `blogs` WRITE;

INSERT INTO `blogs` (`id`, `author_id`, `title`, `teaser`, `tags`, `keywords`, `content`, `language`, `date`, `date_modified`, `published`)
VALUES
	(1,2,'b3cf6b2dd0',NULL,'tag1',NULL,'','en','1970-01-01 01:00:01',NULL,1),
	(2,2,'e12b3a84b2',NULL,NULL,NULL,'','en','1970-01-01 01:00:02',NULL,0),
	(3,2,'c11be3b344',NULL,'tag1',NULL,'','en','1970-01-01 01:00:03',NULL,1),
	(4,2,'1d2275e170',NULL,NULL,NULL,'','de','1970-01-01 01:00:04',NULL,1),
	(5,2,'hs24br55e2',NULL,NULL,NULL,'','en','1970-01-01 01:00:05',NULL, 1);


UNLOCK TABLES;

LOCK TABLES `calendars` WRITE;

INSERT INTO `calendars` (`id`, `author_id`, `title`, `content`, `date`, `start_date`, `end_date`)
VALUES
	(1,2,'7c015444a5','','1970-01-01 01:00:02','2000-01-01','0000-00-00'),
	(2,2,'8f9e4a9962','','1970-01-01 01:00:04','2020-01-01','2020-12-31');

UNLOCK TABLES;

LOCK TABLES `comments` WRITE;

INSERT INTO `comments` (`id`, `parent_id`, `author_id`, `author_facebook_id`, `author_name`, `author_email`, `author_ip`, `content`, `date`)
VALUES
	(1,1,2,NULL,'','','','7c883dc7d2','1970-01-01 01:00:01'),
	(2,1,0,NULL,'Test Commenter','test@example.com','','2e8e0b2d93','1970-01-01 01:00:02');

UNLOCK TABLES;

LOCK TABLES `contents` WRITE;

INSERT INTO `contents` (`id`, `author_id`, `title`, `teaser`, `keywords`, `content`, `date`, `published`)
VALUES
	(1,2,'18855f87f2',NULL,NULL,'','1970-01-01 01:00:01',1),
	(2,2,'8f7fb844b0',NULL,NULL,'','1970-01-01 01:00:02',0);

UNLOCK TABLES;

LOCK TABLES `downloads` WRITE;

INSERT INTO `downloads` (`id`, `author_id`, `title`, `content`, `category`, `file`, `extension`, `downloads`, `date`)
VALUES
	(1,2,'098dec456d',NULL,NULL,'none','ext',0,'1970-01-01 01:00:01');

UNLOCK TABLES;

LOCK TABLES `gallery_albums` WRITE;

INSERT INTO `gallery_albums` (`id`, `author_id`, `title`, `content`, `date`)
VALUES
	(1,2,'6dffc4c552','','1970-01-01 01:00:01');

UNLOCK TABLES;

LOCK TABLES `gallery_files` WRITE;

INSERT INTO `gallery_files` (`id`, `author_id`, `album_id`, `date`, `file`, `extension`, `content`)
VALUES
	(1,2,1,'1970-01-01 01:00:01','982e960e18','jpg','782c660e17');

UNLOCK TABLES;

LOCK TABLES `logs` WRITE;

INSERT INTO `logs` (`id`, `controller_name`, `action_name`, `action_id`, `time_start`, `time_end`, `user_id`)
VALUES
	(1,'blog','create','1','1970-01-01 01:00:03','1970-01-01 01:00:10',1);

UNLOCK TABLES;

LOCK TABLES `users` WRITE;

INSERT INTO `users` (`id`, `name`, `surname`, `password`, `email`, `role`, `date`, `verification_code`, `api_token`, `content`, `use_gravatar`, `receive_newsletter`)
VALUES
	(2,'Administrator','c2f9619961','098f6bcd4621d373cade4e832627b4f6','admin@example.com',4,NOW(),NULL,'c2f9619961',NULL,0,1),
	(3,'Moderator','c3f32cb996','098f6bcd4621d373cade4e832627b4f6','moderator@example.com',3,NOW(),NULL,'c3f32cb996',NULL,0,1),
	(4,'Facebook-User','4ef590ffb5','098f6bcd4621d373cade4e832627b4f6','facebook@example.com',2,NOW(),NULL,'4ef590ffb5',NULL,0,1),
	(5,'Member','6b6ff4a437','098f6bcd4621d373cade4e832627b4f6','member@example.com',1,NOW(),NULL,'6b6ff4a437',NULL,0,1),
	(6,'Unverified','6ccfcbb125','098f6bcd4621d373cade4e832627b4f6','unverified@example.com',1,NOW(),'6ccfcbb125','6ccfcbb125',NULL,0,1);

UNLOCK TABLES;