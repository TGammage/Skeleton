--
--		Core Database Information Creation
--
--

CREATE DATABASE `skeleton_main` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
CREATE DATABASE `skeleton_member` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
CREATE DATABASE `skeleton_backup` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
CREATE DATABASE `skeleton_tmp` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;


--
--
--	Database : skeleton_member
--
--

USE `skeleton_member`;


--
--	Table : skeleton_member.member
--
--	PRIMARY		: id
--	UNIQUE		: username
--	UNIQUE		: email
--
CREATE TABLE `member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(254) COLLATE utf8mb4_general_ci NOT NULL,
  `email_verified` TINYINT(1) NOT NULL DEFAULT 0,
  `email_code` VARCHAR(32) NULL DEFAULT NULL,
  `access` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `sec_level` TINYINT(1) NOT NULL DEFAULT 1,
  `created_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `banned` tinyint(1) COLLATE utf8mb4_general_ci DEFAULT 0 NOT NULL,
  `last_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `member`
  ADD UNIQUE KEY `unique_username` (`username`),
  ADD UNIQUE KEY `unique_email` (`email`);

--
--	Table : skeleton_member.session
--
--	PRIMARY		: session_id
--	FOREIGN		: user_id( `member`.`id` )
CREATE TABLE `session` (
  `session_id` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_agent` varchar(240) COLLATE utf8mb4_general_ci NOT NULL,
  `session_created` DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
  `last_active` DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `session`
  ADD PRIMARY KEY (`session_id`),
  ADD CONSTRAINT `foreign_user_id` FOREIGN KEY (`user_id`) REFERENCES `member`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;



--
--
--	Database : skeleton_main
--
--

USE `skeleton_main`;


--
--  Table : skeleton_main.member
--
--  PRIMARY		: id
--
CREATE TABLE `member` (
  `id` INT(11) NOT NULL ,
  `location` VARCHAR(75) NOT NULL DEFAULT 'Account Creation'
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

ALTER TABLE `member`
  ADD PRIMARY KEY (`id`);

