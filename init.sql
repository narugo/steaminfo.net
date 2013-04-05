-- USERS

CREATE TABLE steam_user (
  community_id           BIGINT    NOT NULL PRIMARY KEY,
  nickname               VARCHAR(64),
  creation_time          INT,
  avatar_url             VARCHAR(255),
  current_game_id        INT,
  current_game_name      VARCHAR(64),
  current_game_server_ip INET,
  is_vac_banned          BOOLEAN,
  last_login_time        INT,
  location_city_id       CHAR(16),
  location_country_code  CHAR(2),
  location_state_code    CHAR(4),
  primary_group_id       BIGINT,
  real_name              VARCHAR(64),
  status                 INT,
  tag                    VARCHAR(32),
  is_community_banned    BOOLEAN,
  economy_ban_state      VARCHAR(16),
  last_updated           TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE friends (
  user_community_id1 BIGINT NOT NULL REFERENCES steam_user (community_id)
  MATCH FULL
  ON DELETE CASCADE
  ON UPDATE CASCADE,
  user_community_id2 BIGINT NOT NULL REFERENCES steam_user (community_id)
  MATCH FULL
  ON DELETE CASCADE
  ON UPDATE CASCADE,
  since              INT
);


-- APPS

CREATE TABLE app (
  id                   BIGINT NOT NULL PRIMARY KEY,
  header_image_url     VARCHAR(256),
  name                 VARCHAR(256),
  type                 VARCHAR(100),
  website              VARCHAR(100),
  release_date         VARCHAR(20),
  legal_notice         TEXT,
  is_win               BOOLEAN,
  is_mac               BOOLEAN,
  is_linux             BOOLEAN,
  recommendations      INT,
  detailed_description TEXT
);

CREATE TABLE app_owners (
  app_id            BIGINT NOT NULL REFERENCES app (id)
  MATCH FULL
  ON DELETE CASCADE
  ON UPDATE CASCADE,
  user_community_id BIGINT NOT NULL REFERENCES steam_user (community_id)
  MATCH FULL
  ON DELETE CASCADE
  ON UPDATE CASCADE,
  used_last_2_weeks INT    NOT NULL DEFAULT 0,
  used_total        INT    NOT NULL DEFAULT 0
);


-- DOTA

CREATE TABLE dota_team (
  id                               INT NOT NULL PRIMARY KEY,
  name                             VARCHAR(100),
  tag                              VARCHAR(100),
  creation_time                    VARCHAR(100),
  rating                           VARCHAR(100),
  logo                             VARCHAR(100),
  logo_sponsor                     VARCHAR(100),
  country_code                     VARCHAR(100),
  url                              VARCHAR(256),
  games_played_with_current_roster INT,
  player_0                         BIGINT REFERENCES steam_user (community_id)
  MATCH FULL
  ON DELETE CASCADE
  ON UPDATE CASCADE,
  player_1                         BIGINT REFERENCES steam_user (community_id)
  MATCH FULL
  ON DELETE CASCADE
  ON UPDATE CASCADE,
  player_2                         BIGINT REFERENCES steam_user (community_id)
  MATCH FULL
  ON DELETE CASCADE
  ON UPDATE CASCADE,
  player_3                         BIGINT REFERENCES steam_user (community_id)
  MATCH FULL
  ON DELETE CASCADE
  ON UPDATE CASCADE,
  player_4                         BIGINT REFERENCES steam_user (community_id)
  MATCH FULL
  ON DELETE CASCADE
  ON UPDATE CASCADE,
  admin_account                    BIGINT REFERENCES steam_user (community_id)
  MATCH FULL
  ON DELETE CASCADE
  ON UPDATE CASCADE);

CREATE TABLE dota_match (
  id                      INT NOT NULL PRIMARY KEY,
  start_time              INT,
  season                  INT,
  radiant_win             BOOLEAN,
  duration                INT,
  cluster                 INT,
  first_blood_time        INT,
  lobby_type              INT,
  human_players           INT,
  league_id               INT,
  positive_votes          INT,
  negative_votes          INT,
  game_mode               INT,
  radiant_team_id         INT REFERENCES dota_team (id)
  MATCH FULL
  ON DELETE CASCADE
  ON UPDATE CASCADE,
  radiant_name            VARCHAR(100),
  radiant_logo            VARCHAR(100),
  radiant_team_complete   BOOLEAN,
  tower_status_radiant    INT,
  barracks_status_radiant INT,
  dire_team_id            INT REFERENCES dota_team (id)
  MATCH FULL
  ON DELETE CASCADE
  ON UPDATE CASCADE,
  dire_name               VARCHAR(100),
  dire_logo               VARCHAR(100),
  dire_team_complete      BOOLEAN,
  tower_status_dire       INT,
  barracks_status_dire    INT
);

CREATE TABLE dota_hero (
  id           INT          NOT NULL  PRIMARY KEY,
  name         VARCHAR(100) NOT NULL,
  display_name VARCHAR(100)
);

CREATE TABLE dota_match_player (
  account_id    BIGINT NOT NULL REFERENCES steam_user (community_id)
  MATCH FULL
  ON DELETE CASCADE
  ON UPDATE CASCADE,
  match_id      BIGINT NOT NULL REFERENCES dota_match (id)
  MATCH FULL
  ON DELETE CASCADE
  ON UPDATE CASCADE,
  hero_id       INT    NOT NULL REFERENCES dota_hero (id)
  MATCH FULL
  ON DELETE CASCADE
  ON UPDATE CASCADE,
  player_slot   INT,
  item_0        INT,
  item_1        INT,
  item_2        INT,
  item_3        INT,
  item_4        INT,
  item_5        INT,
  kills         INT,
  deaths        INT,
  assists       INT,
  leaver_status INT,
  gold          INT,
  last_hits     INT,
  denies        INT,
  gold_per_min  INT,
  xp_per_min    INT,
  gold_spent    INT,
  hero_damage   INT,
  tower_damage  INT,
  hero_healing  INT,
  level         INT
);

CREATE TABLE dota_league (
  id             INT NOT NULL PRIMARY KEY,
  name           VARCHAR(100),
  description    VARCHAR(200),
  tournament_url VARCHAR(200)
);


-- LOGGING

CREATE TABLE error_log (
  remote_address VARCHAR(255) NOT NULL DEFAULT '',
  request_uri    VARCHAR(255) NOT NULL DEFAULT '',
  message        TEXT         NOT NULL,
  time           TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE group_view_log (
  group_id       BIGINT       NOT NULL  PRIMARY KEY,
  remote_address VARCHAR(255) NOT NULL DEFAULT '',
  time           TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE user_profile_view_log (
  user_id        BIGINT       NOT NULL,
  remote_address VARCHAR(255) NOT NULL DEFAULT '',
  time           TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE dota_match_view_log (
  remote_address VARCHAR(100) NOT NULL,
  match_id       INT          NOT NULL,
  time           TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
);


-- HISTORY

CREATE TABLE active_app_users_history (
  record_time  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  active_users INT       NOT NULL,
  app_id       BIGINT    NOT NULL
);

CREATE TABLE indexed_users_history (
  record_date   DATE,
  indexed_users INT NOT NULL
);

CREATE TABLE indexed_apps_history (
  record_date  DATE NOT NULL,
  indexed_apps INT  NOT NULL
);

CREATE TABLE indexed_dota_matches_history (
  record_date     DATE NOT NULL,
  indexed_matches INT  NOT NULL
);
