-- File: sql/create_table_session.sql
--
-- F.2013-06-25 : Moving away from the ondisk-locked-file based default PHP
-- implementation of session storage, because of the godamn lock, actually : /
--
-- For Symfony2's database-baked PHP sessions.
-- @link http://symfony.com/doc/current/cookbook/configuration/pdo_session_storage.html#mysql
--
-- @link app/config/config.yml & app/config/parameters.yml
--
CREATE TABLE session
(
   session_id character varying(255) NOT NULL,
   session_value text NOT NULL,
   session_time integer NOT NULL,
   CONSTRAINT session_pkey PRIMARY KEY (session_id)
);
