CREATE TABLE app (
  id       BIGINT UNSIGNED NOT NULL,
  logo_url VARCHAR(255)    NOT NULL,
  name     VARCHAR(64)     NOT NULL,
  CONSTRAINT pk_app PRIMARY KEY (id)
)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8
  COLLATE = utf8_general_ci;

CREATE TABLE `group` (
  id         BIGINT UNSIGNED NOT NULL,
  name       VARCHAR(64)     NOT NULL,
  avatar_url VARCHAR(255),
  headline   VARCHAR(45),
  summary    VARCHAR(45),
  url        VARCHAR(255),
  CONSTRAINT pk_group PRIMARY KEY (id)
)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8
  COLLATE = utf8_general_ci;

CREATE TABLE user (
  community_id           BIGINT UNSIGNED NOT NULL,
  nickname               VARCHAR(64)     NOT NULL,
  tag                    VARCHAR(32),
  avatar_url             VARCHAR(255)    NOT NULL,
  status                 INT,
  last_login_time        INT,
  current_game_id        INT,
  current_game_name      VARCHAR(64),
  current_game_server_ip VARBINARY(16),
  is_community_banned    BIT(1),
  is_vac_banned          BIT(1),
  economy_ban_state      VARCHAR(16),
  real_name              VARCHAR(64),
  location_city_id       CHAR(16),
  location_country_code  CHAR(2),
  location_state_code    CHAR(4),
  primary_group_id       BIGINT,
  creation_time          INT,
  last_updated           TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT pk_user PRIMARY KEY (community_id)
)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8
  COLLATE = utf8_general_ci;

CREATE TABLE app_owners (
  app_id            BIGINT UNSIGNED NOT NULL,
  user_community_id BIGINT UNSIGNED NOT NULL,
  used_last_2_weeks INT             NOT NULL DEFAULT 0,
  used_total        INT             NOT NULL DEFAULT 0
)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8
  COLLATE = utf8_general_ci;
CREATE INDEX idx_app_owners_app ON app_owners (app_id);
CREATE INDEX idx_app_owners_user ON app_owners (user_community_id);

CREATE TABLE friends (
  user_community_id1 BIGINT UNSIGNED NOT NULL,
  user_community_id2 BIGINT UNSIGNED NOT NULL,
  since              INT
)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8
  COLLATE = utf8_general_ci;
CREATE INDEX idx_friends_user_1 ON friends (user_community_id1);
CREATE INDEX idx_friends_user_2 ON friends (user_community_id2);

CREATE TABLE group_members (
  group_id          BIGINT UNSIGNED NOT NULL,
  user_community_id BIGINT UNSIGNED NOT NULL
)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8
  COLLATE = utf8_general_ci;
CREATE INDEX idx_group_members_group ON group_members (group_id);
CREATE INDEX idx_group_members_user ON group_members (user_community_id);


ALTER TABLE app_owners ADD CONSTRAINT fk_app_owners_app FOREIGN KEY (app_id) REFERENCES app (id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;
ALTER TABLE app_owners ADD CONSTRAINT fk_app_owners_user FOREIGN KEY (user_community_id) REFERENCES user (community_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE friends ADD CONSTRAINT fk_friends1 FOREIGN KEY (user_community_id1) REFERENCES user (community_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;
ALTER TABLE friends ADD CONSTRAINT fk_friends2 FOREIGN KEY (user_community_id2) REFERENCES user (community_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE group_members ADD CONSTRAINT fk_group_members_group FOREIGN KEY (group_id) REFERENCES `group` (id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;
ALTER TABLE group_members ADD CONSTRAINT fk_group_members_user FOREIGN KEY (user_community_id) REFERENCES user (community_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

--
-- LOGS
--

CREATE TABLE error_log (
  id             INT(11)      NOT NULL AUTO_INCREMENT,
  remote_address VARCHAR(255) NOT NULL DEFAULT '',
  request_uri    VARCHAR(255) NOT NULL DEFAULT '',
  message        TEXT         NOT NULL DEFAULT '',
  time           TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT pk_error PRIMARY KEY (id)
)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8
  COLLATE = utf8_general_ci;

CREATE TABLE user_view_log (
  id             INT(11)         NOT NULL AUTO_INCREMENT,
  user_id        BIGINT UNSIGNED NOT NULL,
  remote_address VARCHAR(255)    NOT NULL DEFAULT '',
  time           TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT pk_user_log PRIMARY KEY (id)
)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8
  COLLATE = utf8_general_ci;

CREATE TABLE group_view_log (
  id             INT(11)         NOT NULL AUTO_INCREMENT,
  group_id       BIGINT UNSIGNED NOT NULL,
  remote_address VARCHAR(255)    NOT NULL DEFAULT '',
  time           TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT pk_user_log PRIMARY KEY (id)
)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8
  COLLATE = utf8_general_ci;

--
-- STATISTICS
--

CREATE TABLE indexed_users (
  time   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  amount INT(11)   NOT NULL AUTO_INCREMENT,
  CONSTRAINT pk_user_log PRIMARY KEY (TIME)
)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8
  COLLATE = utf8_general_ci;

CREATE TABLE indexed_groups (
  time   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  amount INT(11)   NOT NULL AUTO_INCREMENT,
  CONSTRAINT pk_user_log PRIMARY KEY (TIME)
)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8
  COLLATE = utf8_general_ci;