CREATE TABLE app (
	id bigint unsigned NOT NULL,
	logo_url varchar(255) NOT NULL,
	name varchar(64) NOT NULL,
	CONSTRAINT pk_app PRIMARY KEY (id)
);

CREATE TABLE `group` (
	id bigint unsigned NOT NULL,
	name varchar(64) NOT NULL,
	avatar_url varchar(255),
	headline varchar(45),
	summary varchar(45),
	url varchar(255),
	CONSTRAINT pk_group PRIMARY KEY (id)
);

CREATE TABLE user (
	community_id bigint unsigned NOT NULL,
	nickname varchar(64) NOT NULL,
	creation_time int,
	avatar_url varchar(255) NOT NULL,
	current_game_id int,
	current_game_name varchar(64),
	current_game_server_ip varbinary(16),
	is_limited_account binary(1),
	is_vac_banned binary(1),
	last_login_time int,
	last_updated timestamp NOT NULL default CURRENT_TIMESTAMP,
	location_city_id char(16),
	location_country_code char(2),
	location_state_code char(4),
	primary_group_id bigint,
	real_name varchar(64),
	status int,
	tag varchar(32),
	trade_ban_state varchar(32),
	CONSTRAINT pk_user PRIMARY KEY (community_id)
);

CREATE TABLE app_owners (
	app_id bigint unsigned NOT NULL,
	user_community_id bigint unsigned NOT NULL,
	used_last_2_weeks int NOT NULL default 0,
	used_total int NOT NULL default 0
);
CREATE INDEX idx_app_owners_app ON app_owners (app_id);
CREATE INDEX idx_app_owners_user ON app_owners (user_community_id);

CREATE TABLE friends (
	since int,
	user_community_id1 bigint unsigned NOT NULL,
	user_community_id2 bigint unsigned NOT NULL
);
CREATE INDEX idx_friends_user_1 ON friends (user_community_id1);
CREATE INDEX idx_friends_user_2 ON friends (user_community_id2);

CREATE TABLE group_members (
	group_id bigint unsigned NOT NULL,
	user_community_id bigint unsigned NOT NULL
);
CREATE INDEX idx_group_members_group ON group_members (group_id);
CREATE INDEX idx_group_members_user ON group_members (user_community_id);


ALTER TABLE app_owners ADD CONSTRAINT fk_app_owners_app FOREIGN KEY (app_id) REFERENCES app(id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE app_owners ADD CONSTRAINT fk_app_owners_user FOREIGN KEY (user_community_id) REFERENCES user(community_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE friends ADD CONSTRAINT fk_friends1 FOREIGN KEY (user_community_id1) REFERENCES user(community_id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE friends ADD CONSTRAINT fk_friends2 FOREIGN KEY (user_community_id2) REFERENCES user(community_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE group_members ADD CONSTRAINT fk_group_members_group FOREIGN KEY (group_id) REFERENCES `group`(id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE group_members ADD CONSTRAINT fk_group_members_user FOREIGN KEY (user_community_id) REFERENCES user(community_id) ON DELETE CASCADE ON UPDATE CASCADE;


CREATE TABLE error_logs (
  id int(11) NOT NULL AUTO_INCREMENT,
  remote_address varchar(255) NOT NULL default '',
  request_uri varchar(255) NOT NULL default '',
  message text NOT NULL default '',
  log_date timestamp NOT NULL default CURRENT_TIMESTAMP,
  CONSTRAINT pk_error PRIMARY KEY (id)
);