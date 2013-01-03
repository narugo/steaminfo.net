CREATE TABLE steaminfo.app (
	id bigint unsigned NOT NULL,
	logo_url varchar( 255 ) NOT NULL,
	name varchar( 64 ) NOT NULL,
	CONSTRAINT pk_app PRIMARY KEY ( id )
);

CREATE TABLE steaminfo.`group` (
	id bigint unsigned NOT NULL,
	name                 varchar( 64 ) NOT NULL,
	avatar_url           varchar( 255 ),
	headline             varchar( 45 ),
	summary              varchar( 45 ),
	url                  varchar( 255 ),
	CONSTRAINT pk_group PRIMARY KEY ( id )
);

CREATE TABLE steaminfo.user ( 
	community_id         bigint unsigned NOT NULL,
	nickname             varchar( 64 ) NOT NULL,
	creation_time        int,
	avatar_url           varchar( 255 ) NOT NULL,
	current_game_id      int,
	current_game_name    varchar( 64 ),
	current_game_server_ip varbinary( 16 ),
	is_limited_account   binary( 1 ),
	is_vac_banned        binary( 1 ),
	last_login_time      int,
	last_updated         timestamp NOT NULL default CURRENT_TIMESTAMP,
	location_city_id     CHAR( 16 ),
	location_country_code CHAR( 2 ),
	location_state_code  CHAR( 4 ),
	primary_group_id     bigint,
	real_name            varchar( 64 ),
	status               int,
	tag                  varchar( 32 ),
	trade_ban_state      varchar( 32 ),
	CONSTRAINT pk_user PRIMARY KEY ( community_id )
);

CREATE TABLE steaminfo.app_owners ( 
	app_id               bigint unsigned NOT NULL,
	user_community_id    bigint unsigned NOT NULL,
	used_last_2_weeks    int NOT NULL default 0,
	used_total           int NOT NULL default 0
);
CREATE INDEX idx_app_owners_app ON steaminfo.app_owners ( app_id );
CREATE INDEX idx_app_owners_user ON steaminfo.app_owners ( user_community_id );

CREATE TABLE steaminfo.friends ( 
	since                int,
	user_community_id1   bigint unsigned NOT NULL,
	user_community_id2   bigint unsigned NOT NULL
);
CREATE INDEX idx_friends_user_1 ON steaminfo.friends ( user_community_id1 );
CREATE INDEX idx_friends_user_2 ON steaminfo.friends ( user_community_id2 );

CREATE TABLE steaminfo.group_members ( 
	group_id             bigint unsigned NOT NULL,
	user_community_id    bigint unsigned NOT NULL
);
CREATE INDEX idx_group_members_group ON steaminfo.group_members ( group_id );
CREATE INDEX idx_group_members_user ON steaminfo.group_members ( user_community_id );


ALTER TABLE steaminfo.app_owners ADD CONSTRAINT fk_app_owners_app FOREIGN KEY ( app_id ) REFERENCES steaminfo.app( id ) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE steaminfo.app_owners ADD CONSTRAINT fk_app_owners_user FOREIGN KEY ( user_community_id ) REFERENCES steaminfo.user( community_id ) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE steaminfo.friends ADD CONSTRAINT fk_friends1 FOREIGN KEY ( user_community_id1 ) REFERENCES steaminfo.user( community_id ) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE steaminfo.friends ADD CONSTRAINT fk_friends2 FOREIGN KEY ( user_community_id2 ) REFERENCES steaminfo.user( community_id ) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE steaminfo.group_members ADD CONSTRAINT fk_group_members_group FOREIGN KEY ( group_id ) REFERENCES steaminfo.`group`( id ) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE steaminfo.group_members ADD CONSTRAINT fk_group_members_user FOREIGN KEY ( user_community_id ) REFERENCES steaminfo.user( community_id ) ON DELETE CASCADE ON UPDATE CASCADE;