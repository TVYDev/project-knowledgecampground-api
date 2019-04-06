--Don't use this table anymore
CREATE TABLE trash_tokens
(
  id SERIAL PRIMARY KEY,
  token VARCHAR(1000) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT NOW()
);

-- Edited 16/03/2019
CREATE TABLE user_avatars
(
  id SERIAL PRIMARY KEY,
  user__id int4 NOT NULL,
  first_initial VARCHAR(5) NOT NULL,
  middle_color_hex VARCHAR(10) NOT NULL,
  side_lg_color_hex VARCHAR(10) NOT NULL,
  side_sm_color_hex VARCHAR(10) NOT NULL,
  border_color_hex VARCHAR(10) NOT NULL,
  angle int4 NOT NULL,
  is_active BOOL NOT NULL DEFAULT true,
  img_url VARCHAR(500) NULL,
  created_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
  updated_at TIMESTAMP(0) NOT NULL DEFAULT NOW()
);

-- -----------------------------ACTIVITIES
-- 06 April 2019
ALTER TABLE users
ADD COLUMN password1 varchar(255) NULL,
ADD COLUMN password2 varchar(255) NULL,
ADD COLUMN password3 varchar(255) NULL,
ADD COLUMN is_active boolean NOT NULL DEFAULT TRUE,
ADD COLUMN is_blocked boolean NOT NULL DEFAULT FALSE,
ADD COLUMN is_deleted boolean NOT NULL DEFAULT FALSE;