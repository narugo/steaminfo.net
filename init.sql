CREATE SCHEMA steaminfo;

CREATE TABLE app ( 
	id                   BIGINT UNSIGNED NOT NULL  ,
	logo_url             VARCHAR( 255 )  NOT NULL  ,
	name                 VARCHAR( 64 )  NOT NULL  ,
	CONSTRAINT pk_app PRIMARY KEY ( id )
 );

CREATE TABLE dota_hero ( 
	id                   INT  NOT NULL  ,
	name                 VARCHAR( 100 )  NOT NULL  ,
	display_name         VARCHAR( 100 )    ,
	CONSTRAINT pk_dota_heroes PRIMARY KEY ( id )
 );

CREATE TABLE error_log ( 
	remote_address       VARCHAR( 255 )  NOT NULL DEFAULT '' ,
	request_uri          VARCHAR( 255 )  NOT NULL DEFAULT '' ,
	message              TEXT  NOT NULL  ,
	time                 TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP 
 );

CREATE TABLE group ( 
	id                   BIGINT UNSIGNED NOT NULL  ,
	name                 VARCHAR( 64 )    ,
	headline             VARCHAR( 45 )    ,
	summary              VARCHAR( 1000 )    ,
	url                  VARCHAR( 255 )    ,
	avatar_icon_url      VARCHAR( 255 )    ,
	avatar_medium_url    VARCHAR( 255 )    ,
	avatar_full_url      VARCHAR( 255 )    ,
	last_updated         TIMESTAMP   DEFAULT CURRENT_TIMESTAMP ,
	CONSTRAINT pk_group PRIMARY KEY ( id )
 );

CREATE TABLE group_view_log ( 
	group_id             BIGINT UNSIGNED NOT NULL  ,
	remote_address       VARCHAR( 255 )  NOT NULL DEFAULT '' ,
	time                 TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP 
 );

CREATE INDEX idx_group_view_log ON group_view_log ( group_id );

CREATE TABLE user ( 
	community_id         BIGINT UNSIGNED NOT NULL  ,
	nickname             VARCHAR( 64 )  NOT NULL  ,
	creation_time        INT    ,
	avatar_url           VARCHAR( 255 )  NOT NULL  ,
	current_game_id      INT    ,
	current_game_name    VARCHAR( 64 )    ,
	current_game_server_ip VARBINARY( 16 )    ,
	is_vac_banned        BIT    ,
	last_login_time      INT    ,
	last_updated         TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	location_city_id     CHAR( 16 )    ,
	location_country_code CHAR( 2 )    ,
	location_state_code  CHAR( 4 )    ,
	primary_group_id     BIGINT    ,
	real_name            VARCHAR( 64 )    ,
	status               INT    ,
	tag                  VARCHAR( 32 )    ,
	is_community_banned  BIT    ,
	economy_ban_state    VARCHAR( 16 )    ,
	CONSTRAINT pk_user PRIMARY KEY ( community_id )
 );

CREATE TABLE user_profile_view_log ( 
	user_id              BIGINT UNSIGNED NOT NULL  ,
	remote_address       VARCHAR( 255 )  NOT NULL DEFAULT '' ,
	time                 TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP 
 );

CREATE INDEX idx_user_profile_view_log ON user_profile_view_log ( user_id );

CREATE TABLE active_app_users_history ( 
	record_time          TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	active_users         INT  NOT NULL  ,
	app_id               BIGINT UNSIGNED NOT NULL  
 );

CREATE INDEX idx_active_app_users_history ON active_app_users_history ( app_id );

CREATE TABLE indexed_users_history ( 
	record_date          DATE    ,
	indexed_users        INT  NOT NULL  
 );

CREATE TABLE indexed_apps_history ( 
	record_date          DATE  NOT NULL  ,
	indexed_apps         INT  NOT NULL  
 );

CREATE TABLE indexed_dota_matches_history ( 
	record_date          DATE  NOT NULL  ,
	indexed_matches      INT  NOT NULL  
 );

CREATE TABLE dota_league ( 
	id                   INT  NOT NULL  ,
	name                 VARCHAR( 100 )    ,
	description          VARCHAR( 200 )    ,
	tournament_url       VARCHAR( 200 )    ,
	CONSTRAINT pk_dota_league PRIMARY KEY ( id )
 );

CREATE TABLE app_owners ( 
	app_id               BIGINT UNSIGNED NOT NULL  ,
	user_community_id    BIGINT UNSIGNED NOT NULL  ,
	used_last_2_weeks    INT  NOT NULL DEFAULT 0 ,
	used_total           INT  NOT NULL DEFAULT 0 
 );

CREATE INDEX idx_app_owners ON app_owners ( app_id );

CREATE INDEX idx_app_owners_0 ON app_owners ( user_community_id );

CREATE TABLE dota_match ( 
	id                   INT  NOT NULL  ,
	start_time           INT    ,
	season               INT    ,
	radiant_win          BIT    ,
	duration             INT    ,
	cluster              INT    ,
	first_blood_time     INT    ,
	lobby_type           INT    ,
	human_players        INT    ,
	league_id            INT    ,
	positive_votes       INT    ,
	negative_votes       INT    ,
	game_mode            INT    ,
	radiant_name         VARCHAR( 100 )    ,
	radiant_logo         VARCHAR( 100 )    ,
	radiant_team_complete BIT    ,
	tower_status_radiant INT    ,
	barracks_status_radiant INT    ,
	dire_name            VARCHAR( 100 )    ,
	dire_logo            VARCHAR( 100 )    ,
	dire_team_complete   BIT    ,
	tower_status_dire    INT    ,
	barracks_status_dire INT    ,
	CONSTRAINT pk_dota_match PRIMARY KEY ( id )
 );

CREATE INDEX idx_dota_match ON dota_match ( league_id );

CREATE TABLE dota_match_player ( 
	account_id           BIGINT UNSIGNED   ,
	match_id             INT  NOT NULL  ,
	hero_id              INT  NOT NULL  ,
	player_slot          INT    ,
	item_0               INT    ,
	item_1               INT    ,
	item_2               INT    ,
	item_3               INT    ,
	item_4               INT    ,
	item_5               INT    ,
	kills                INT    ,
	deaths               INT    ,
	assists              INT    ,
	leaver_status        INT    ,
	gold                 INT    ,
	last_hits            INT    ,
	denies               INT    ,
	gold_per_min         INT    ,
	xp_per_min           INT    ,
	gold_spent           INT    ,
	hero_damage          INT    ,
	tower_damage         INT    ,
	hero_healing         INT    ,
	level                INT    
 );

CREATE INDEX idx_dota_match_player ON dota_match_player ( match_id );

CREATE INDEX idx_dota_match_player_0 ON dota_match_player ( hero_id );

CREATE INDEX idx_dota_match_player_1 ON dota_match_player ( account_id );

CREATE TABLE dota_match_view_log ( 
	remote_address       VARCHAR( 100 )  NOT NULL  ,
	match_id             INT  NOT NULL  ,
	time                 TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP 
 );

CREATE INDEX idx_dota_match_view_log ON dota_match_view_log ( match_id );

CREATE TABLE friends ( 
	since                INT    ,
	user_community_id1   BIGINT UNSIGNED NOT NULL  ,
	user_community_id2   BIGINT UNSIGNED NOT NULL  
 );

CREATE INDEX idx_friends ON friends ( user_community_id1 );

CREATE INDEX idx_friends_0 ON friends ( user_community_id2 );

CREATE TABLE group_members ( 
	group_id             BIGINT UNSIGNED NOT NULL  ,
	user_community_id    BIGINT UNSIGNED NOT NULL  
 );

CREATE INDEX idx_group_members ON group_members ( group_id );

CREATE INDEX idx_group_members_0 ON group_members ( user_community_id );

ALTER TABLE app_owners ADD CONSTRAINT fk_app_owners_app FOREIGN KEY ( app_id ) REFERENCES app( id ) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE app_owners ADD CONSTRAINT fk_app_owners_user FOREIGN KEY ( user_community_id ) REFERENCES user( community_id ) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE dota_match ADD CONSTRAINT fk_dota_match FOREIGN KEY ( league_id ) REFERENCES dota_league( id ) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE dota_match_player ADD CONSTRAINT fk_dota_match_player FOREIGN KEY ( match_id ) REFERENCES dota_match( id ) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE dota_match_player ADD CONSTRAINT fk_dota_match_player_0 FOREIGN KEY ( hero_id ) REFERENCES dota_hero( id ) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE dota_match_player ADD CONSTRAINT fk_dota_match_player_1 FOREIGN KEY ( account_id ) REFERENCES user( community_id ) ON DELETE NO ACTION ON UPDATE CASCADE;

ALTER TABLE dota_match_view_log ADD CONSTRAINT fk_dota_match_view_log FOREIGN KEY ( match_id ) REFERENCES dota_match( id ) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE friends ADD CONSTRAINT fk_friends1 FOREIGN KEY ( user_community_id1 ) REFERENCES user( community_id ) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE friends ADD CONSTRAINT fk_friends2 FOREIGN KEY ( user_community_id2 ) REFERENCES user( community_id ) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE group_members ADD CONSTRAINT fk_group_members_group FOREIGN KEY ( group_id ) REFERENCES group( id ) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE group_members ADD CONSTRAINT fk_group_members_user FOREIGN KEY ( user_community_id ) REFERENCES user( community_id ) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE group_view_log ADD CONSTRAINT fk_group_view_log FOREIGN KEY ( group_id ) REFERENCES group( id ) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE user_profile_view_log ADD CONSTRAINT fk_user_profile_view_log FOREIGN KEY ( user_id ) REFERENCES user( community_id ) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE active_app_users_history ADD CONSTRAINT fk_active_app_users_history FOREIGN KEY ( app_id ) REFERENCES app( id ) ON DELETE CASCADE ON UPDATE CASCADE;

