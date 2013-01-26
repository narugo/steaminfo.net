CREATE SCHEMA steaminfo;

CREATE TABLE app ( 
	id                   BIGINT UNSIGNED NOT NULL,
	logo_url             VARCHAR( 255 ) NOT NULL,
	name                 VARCHAR( 64 ) NOT NULL,
	CONSTRAINT pk_app PRIMARY KEY ( id )
 );

CREATE TABLE dota_hero ( 
	id                   INT NOT NULL,
	name                 VARCHAR( 100 ),
	CONSTRAINT pk_dota_heroes PRIMARY KEY ( id )
 );

CREATE TABLE error_log ( 
	id                   INT NOT NULL AUTO_INCREMENT,
	remote_address       VARCHAR( 255 ) NOT NULL DEFAULT '',
	request_uri          VARCHAR( 255 ) NOT NULL DEFAULT '',
	message              TEXT NOT NULL,
	time                 TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	CONSTRAINT pk_error_log PRIMARY KEY ( id )
 );

CREATE TABLE `group` ( 
	id                   BIGINT UNSIGNED NOT NULL,
	name                 VARCHAR( 64 ) NOT NULL,
	avatar_url           VARCHAR( 255 ),
	headline             VARCHAR( 45 ),
	summary              VARCHAR( 45 ),
	url                  VARCHAR( 255 ),
	CONSTRAINT pk_group PRIMARY KEY ( id )
 );

CREATE TABLE user ( 
	community_id         BIGINT UNSIGNED NOT NULL,
	nickname             VARCHAR( 64 ) NOT NULL,
	creation_time        INT,
	avatar_url           VARCHAR( 255 ) NOT NULL,
	current_game_id      INT,
	current_game_name    VARCHAR( 64 ),
	current_game_server_ip VARBINARY( 16 ),
	is_vac_banned        BIT,
	last_login_time      INT,
	last_updated         TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	location_city_id     CHAR( 16 ),
	location_country_code CHAR( 2 ),
	location_state_code  CHAR( 4 ),
	primary_group_id     BIGINT,
	real_name            VARCHAR( 64 ),
	status               INT,
	tag                  VARCHAR( 32 ),
	is_community_banned  BIT,
	economy_ban_state    VARCHAR( 16 ),
	CONSTRAINT pk_user PRIMARY KEY ( community_id )
 );

CREATE TABLE user_profile_view_log ( 
	id                   INT NOT NULL AUTO_INCREMENT,
	user_id              BIGINT UNSIGNED NOT NULL,
	remote_address       VARCHAR( 255 ) NOT NULL DEFAULT '',
	time                 TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	CONSTRAINT pk_user_profile_view_log PRIMARY KEY ( id )
 );

CREATE TABLE user_view_log ( 
	id                   INT NOT NULL AUTO_INCREMENT,
	user_id              BIGINT UNSIGNED NOT NULL,
	remote_address       VARCHAR( 255 ) NOT NULL DEFAULT '',
	time                 TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	CONSTRAINT pk_user_view_log PRIMARY KEY ( id )
 );

CREATE TABLE dota_match ( 
	id                   INT NOT NULL,
	CONSTRAINT pk_dota_match PRIMARY KEY ( id )
 );

CREATE TABLE app_owners ( 
	app_id               BIGINT UNSIGNED NOT NULL,
	user_community_id    BIGINT UNSIGNED NOT NULL,
	used_last_2_weeks    INT NOT NULL DEFAULT 0,
	used_total           INT NOT NULL DEFAULT 0
 );

CREATE INDEX idx_app_owners ON app_owners ( app_id );

CREATE INDEX idx_app_owners_0 ON app_owners ( user_community_id );

CREATE TABLE friends ( 
	since                INT,
	user_community_id1   BIGINT UNSIGNED NOT NULL,
	user_community_id2   BIGINT UNSIGNED NOT NULL
 );

CREATE INDEX idx_friends ON friends ( user_community_id1 );

CREATE INDEX idx_friends_0 ON friends ( user_community_id2 );

CREATE TABLE group_members ( 
	group_id             BIGINT UNSIGNED NOT NULL,
	user_community_id    BIGINT UNSIGNED NOT NULL
 );

CREATE INDEX idx_group_members ON group_members ( group_id );

CREATE INDEX idx_group_members_0 ON group_members ( user_community_id );

ALTER TABLE app_owners ADD CONSTRAINT fk_app_owners_app FOREIGN KEY ( app_id ) REFERENCES app( id ) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE app_owners ADD CONSTRAINT fk_app_owners_user FOREIGN KEY ( user_community_id ) REFERENCES user( community_id ) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE friends ADD CONSTRAINT fk_friends1 FOREIGN KEY ( user_community_id1 ) REFERENCES user( community_id ) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE friends ADD CONSTRAINT fk_friends2 FOREIGN KEY ( user_community_id2 ) REFERENCES user( community_id ) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE group_members ADD CONSTRAINT fk_group_members_group FOREIGN KEY ( group_id ) REFERENCES `group`( id ) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE group_members ADD CONSTRAINT fk_group_members_user FOREIGN KEY ( user_community_id ) REFERENCES user( community_id ) ON DELETE CASCADE ON UPDATE CASCADE;

