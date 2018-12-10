--
--	USER : skeleton_portal
--
--	PURPOSE : Credential Assessment for Login System
--
--	PRIVILEGES :
--		SELECT ONLY on skeleton_member.member for username, email, access, and banned
--
CREATE USER 'skeleton_portal'@'localhost' IDENTIFIED BY 'EJVFxvzYuVVyrSbm';

GRANT USAGE ON *.* TO 'skeleton_portal'@'localhost' REQUIRE NONE WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;

GRANT SELECT (`id`,`username`, `email`, `access`, `banned`) ON  `skeleton_member`.`member` TO 'skeleton_portal'@'localhost';

--
--	USER : skeleton_system
--
--	PURPOSE : Application usage. Default database user access point for site
--
--	PRIVILEGES :
--		SELECT, INSERT, UPDATE, DELETE on skeleton_member and skeleton_main
--
CREATE USER 'skeleton_system'@'localhost' IDENTIFIED BY 'NojeGpLokdSiWnQLI';

GRANT USAGE ON *.* TO 'skeleton_system'@'localhost' REQUIRE NONE WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;

GRANT SELECT, INSERT, UPDATE, DELETE ON  `skeleton_main`.* TO 'skeleton_system'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON  `skeleton_member`.* TO 'skeleton_system'@'localhost';

--
--	USER : skeleton_staff
--
--	PURPOSE : Development and Policing
--
--	PRIVILEGES :
--		ALL PRIVILEGES on all databases
--
CREATE USER 'skeleton_staff'@'localhost' IDENTIFIED BY 'iWZkMZZS8XiOKuua';

GRANT USAGE ON *.* TO 'skeleton_staff'@'localhost' REQUIRE NONE WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;

GRANT ALL PRIVILEGES ON  `skeleton_backup`.* TO 'skeleton_staff'@'localhost' WITH GRANT OPTION;
GRANT ALL PRIVILEGES ON  `skeleton_main`.* TO 'skeleton_staff'@'localhost' WITH GRANT OPTION;
GRANT ALL PRIVILEGES ON  `skeleton_member`.* TO 'skeleton_staff'@'localhost' WITH GRANT OPTION;
GRANT ALL PRIVILEGES ON  `skeleton_tmp`.* TO 'skeleton_staff'@'localhost' WITH GRANT OPTION;

--
--	USER : skeleton_tick
--
--	PURPOSE : cronjob database access
--
--	PRIVILEGES :
--		SELECT, INSERT, UPDATE, DELETE, CREATE, DROP,
--		INDEX, ALTER, CREATE TEMPORARY TABLES,
--		LOCK TABLES, SHOW VIEW, EXECUTE
--		on all databases
--
CREATE USER 'skeleton_tick'@'localhost' IDENTIFIED BY 'QmjeBfLTJiGfEaLD';

GRANT USAGE ON *.* TO 'skeleton_tick'@'localhost' REQUIRE NONE WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;

GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, CREATE TEMPORARY TABLES, LOCK TABLES, SHOW VIEW, EXECUTE ON  `skeleton_backup`.* TO 'skeleton_tick'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, CREATE TEMPORARY TABLES, LOCK TABLES, SHOW VIEW, EXECUTE ON  `skeleton_main`.* TO 'skeleton_tick'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, CREATE TEMPORARY TABLES, LOCK TABLES, SHOW VIEW, EXECUTE ON  `skeleton_member`.* TO 'skeleton_tick'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, CREATE TEMPORARY TABLES, LOCK TABLES, SHOW VIEW, EXECUTE ON  `skeleton_tmp`.* TO 'skeleton_tick'@'localhost';
  
FLUSH PRIVILEGES;  