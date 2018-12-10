--
--	USER : skeleton_dev_portal
--
--	PURPOSE : Credential Assessment for Login System
--
--	PRIVILEGES :
--		SELECT ONLY on skeleton_dev_member.member for username, email, access, and banned
--
CREATE USER 'skeleton_dev_portal'@'localhost' IDENTIFIED BY 'EJVFxvzYuVVyrSbm';

GRANT USAGE ON *.* TO 'skeleton_dev_portal'@'localhost' REQUIRE NONE WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;

GRANT SELECT (`id`, `lockout`, `username`, `email`, `access`) ON `skeleton_dev_member`.`member` TO 'skeleton_dev_portal'@'localhost';
GRANT SELECT ON `skeleton_dev_tmp`.`account_banned` TO 'skeleton_dev_portal'@'localhost';
GRANT SELECT ON `skeleton_dev_tmp`.`ip_banned` TO 'skeleton_dev_portal'@'localhost';

--
--	USER : skeleton_dev_system
--
--	PURPOSE : Application usage. Default database user access point for site
--
--	PRIVILEGES :
--		SELECT, INSERT, UPDATE, DELETE on skeleton_dev_member and skeleton_dev_main
--
CREATE USER 'skeleton_dev_system'@'localhost' IDENTIFIED BY 'NojeGpLokdSiWnQLI';

GRANT USAGE ON *.* TO 'skeleton_dev_system'@'localhost' REQUIRE NONE WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;

GRANT SELECT, INSERT, UPDATE, DELETE ON  `skeleton_dev_main`.* TO 'skeleton_dev_system'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON  `skeleton_dev_member`.* TO 'skeleton_dev_system'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON  `skeleton_dev_tmp`.* TO 'skeleton_dev_system'@'localhost';

--
--	USER : skeleton_dev_staff
--
--	PURPOSE : Development and Policing
--
--	PRIVILEGES :
--		ALL PRIVILEGES on all databases
--
CREATE USER 'skeleton_dev_staff'@'localhost' IDENTIFIED BY 'iWZkMZZS8XiOKuua';

GRANT USAGE ON *.* TO 'skeleton_dev_staff'@'localhost' REQUIRE NONE WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;

GRANT ALL PRIVILEGES ON  `skeleton_dev_backup`.* TO 'skeleton_dev_staff'@'localhost' WITH GRANT OPTION;
GRANT ALL PRIVILEGES ON  `skeleton_dev_main`.* TO 'skeleton_dev_staff'@'localhost' WITH GRANT OPTION;
GRANT ALL PRIVILEGES ON  `skeleton_dev_member`.* TO 'skeleton_dev_staff'@'localhost' WITH GRANT OPTION;
GRANT ALL PRIVILEGES ON  `skeleton_dev_tmp`.* TO 'skeleton_dev_staff'@'localhost' WITH GRANT OPTION;

--
--	USER : skeleton_dev_tick
--
--	PURPOSE : cronjob database access
--
--	PRIVILEGES :
--		SELECT, INSERT, UPDATE, DELETE, CREATE, DROP,
--		INDEX, ALTER, CREATE TEMPORARY TABLES,
--		LOCK TABLES, SHOW VIEW, EXECUTE
--		on all databases
--
CREATE USER 'skeleton_dev_tick'@'localhost' IDENTIFIED BY 'QmjeBfLTJiGfEaLD';

GRANT USAGE ON *.* TO 'skeleton_dev_tick'@'localhost' REQUIRE NONE WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;

GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, CREATE TEMPORARY TABLES, LOCK TABLES, SHOW VIEW, EXECUTE ON  `skeleton_dev_backup`.* TO 'skeleton_dev_tick'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, CREATE TEMPORARY TABLES, LOCK TABLES, SHOW VIEW, EXECUTE ON  `skeleton_dev_main`.* TO 'skeleton_dev_tick'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, CREATE TEMPORARY TABLES, LOCK TABLES, SHOW VIEW, EXECUTE ON  `skeleton_dev_member`.* TO 'skeleton_dev_tick'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, CREATE TEMPORARY TABLES, LOCK TABLES, SHOW VIEW, EXECUTE ON  `skeleton_dev_tmp`.* TO 'skeleton_dev_tick'@'localhost';
  
FLUSH PRIVILEGES;  