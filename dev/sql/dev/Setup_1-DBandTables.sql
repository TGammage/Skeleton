--
--		Core Database Information Creation
--
--

CREATE DATABASE `skeleton_dev_main` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
CREATE DATABASE `skeleton_dev_member` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
CREATE DATABASE `skeleton_dev_backup` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
CREATE DATABASE `skeleton_dev_tmp` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;


--
--
--	Database : skeleton_dev_member
--
--

USE `skeleton_dev_member`;


--
--	Table : skeleton_dev_member.member
--
--	PRIMARY		: id
--	UNIQUE		: username
--	UNIQUE		: email
--
CREATE TABLE `member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lockout` TINYINT(1) NOT NULL DEFAULT 1,
  `username` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(254) COLLATE utf8mb4_general_ci NOT NULL,
  `email_verified` TINYINT(1) NOT NULL DEFAULT 0,
  `first_name` varchar(20) NULL DEFAULT NULL,
  `last_name` varchar(30) NULL DEFAULT NULL,
  `access` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `sec_level` TINYINT(1) NOT NULL DEFAULT 1,
  `created_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `member`
  ADD UNIQUE KEY `unique_username` (`username`),
  ADD UNIQUE KEY `unique_email` (`email`);

CREATE VIEW `profile_info` AS SELECT `id`, `username`, `email`, `email_verified`, `first_name`, `last_name`, `sec_level`,`last_updated` FROM `member`;

--
--	Table : skeleton_dev_member.session
--
--	PRIMARY		: session_id
--	FOREIGN		: user_id( `member`.`id` )
CREATE TABLE `session` (
  `session_id` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_agent` varchar(240) COLLATE utf8mb4_general_ci NOT NULL,
  `user_ip` VARCHAR(15) NULL,
  `session_created` DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
  `last_active` DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `session`
  ADD PRIMARY KEY (`session_id`),
  ADD CONSTRAINT `foreign_user_id` FOREIGN KEY (`user_id`) REFERENCES `member`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

INSERT INTO `member` (`id`, `username`, `email`, `email_verified`, `access`, `sec_level`, `created_date`, `last_updated`) VALUES
(1, 'Architect', 'mr.t.gammage@gmail.com', 1, '$2y$10$ITmP0pW7z.bJnFX4PUnMaOkXcjMPopQNb1RWToo9ylNi9KWkILisC', 10, '2018-12-07 01:18:39', '2018-12-07 01:18:39');

--
--
--	Database : skeleton_dev_main
--
--

USE `skeleton_dev_main`;


--
--  Table : skeleton_dev_main.member
--
--  PRIMARY		: id
--
CREATE TABLE `member` (
  `id` INT(11) NOT NULL ,
  `location` VARCHAR(75) NOT NULL DEFAULT 'Account Creation'
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

ALTER TABLE `member`
  ADD PRIMARY KEY (`id`);


INSERT INTO `member` (`id`, `location`) VALUES
(1, 'Account Creation');




--
--
--	Database : skeleton_dev_tmp
--
--

USE `skeleton_dev_tmp`;


--
--  Table : skeleton_dev_tmp.account_recovery
--
--  PRIMARY		: id
--
CREATE TABLE `account_recovery` (
  `id` varchar(32) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_code` varchar(8) NOT NULL,
  `expiration` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `account_recovery`
  ADD UNIQUE KEY `unique_email` (`email`);

--
--  Table : skeleton_dev_tmp.email_verify
--
--  PRIMARY		: id
--
CREATE TABLE `email_verify` (
  `id` varchar(32) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_code` varchar(32) NOT NULL,
  `expiration` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `email_verify`
  ADD UNIQUE KEY `unique_email` (`email`);

--
--  Table : skeleton_dev_tmp.ip_banned
--
--  PRIMARY		: id
--
CREATE TABLE `ip_banned` (
  `ip` varchar(15) NOT NULL,
  `start_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `finish_time` datetime NOT NULL,
  `reason` varchar(255) NOT NULL,
  `staff_issued_by` int(11) NOT NULL,
  PRIMARY KEY (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
--  Table : skeleton_dev_tmp.account_banned
--
--  PRIMARY		: id
--
CREATE TABLE `account_banned` (
  `id` varchar(15) NOT NULL,
  `start_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `finish_time` datetime NOT NULL,
  `reason` varchar(255) NOT NULL,
  `staff_issued_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
