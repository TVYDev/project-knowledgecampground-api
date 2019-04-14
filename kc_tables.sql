-------------------------Current Database Structure---------------------
CREATE TABLE users (
  id SERIAL NOT NULL,
  name VARCHAR(20) NOT NULL,
  email VARCHAR(30) NOT NULL,
  email_verified_at TIMESTAMP(0),
  password VARCHAR(255) NOT NULL,
  remember_token VARCHAR(100),
  created_at TIMESTAMP(0),
  updated_at TIMESTAMP(0),
  password1 VARCHAR(255),
  password2 VARCHAR(255),
  password3 VARCHAR(255),
  isActive BOOLEAN,
  isBlocked BOOLEAN,
  isdeleted BOOLEAN,
  PRIMARY KEY (id),
  UNIQUE (email)
);

CREATE TABLE user_avatars
(
  id SERIAL NOT NULL,
  user__id int4 NOT NULL,
  seed int4 NOT NULL,
  default_avatar_url VARCHAR(500) NOT NULL,
  is_active BOOLEAN NOT NULL DEFAULT true,
  img_url VARCHAR(500) NULL,
  created_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
  updated_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
  PRIMARY KEY (id),
  FOREIGN KEY (user__id) REFERENCES users(id),
  UNIQUE (seed)
);
------------------------------------------------------------------------

-- -----------------------------ACTIVITIES
-- 06 April 2019
ALTER TABLE users
ADD COLUMN password1 varchar(255) NULL,
ADD COLUMN password2 varchar(255) NULL,
ADD COLUMN password3 varchar(255) NULL,
ADD COLUMN is_active boolean NOT NULL DEFAULT TRUE,
ADD COLUMN is_blocked boolean NOT NULL DEFAULT FALSE,
ADD COLUMN is_deleted boolean NOT NULL DEFAULT FALSE;

-- 13 April 2019
-- Change user_avatars to user_avatars_old (For not use anymore)
ALTER TABLE user_avatars RENAME TO user_avatars_old;
-- Add new user_avatars table structure
CREATE TABLE user_avatars
(
  id SERIAL NOT NULL,
  user__id int4 NOT NULL,
  seed int4 NOT NULL,
  default_avatar_url VARCHAR(500) NOT NULL,
  is_active BOOLEAN NOT NULL DEFAULT true,
  img_url VARCHAR(500) NULL,
  created_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
  updated_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
  PRIMARY KEY (id),
  FOREIGN KEY (user__id) REFERENCES users(id),
  UNIQUE (seed)
);