--Don't use this table anymore
CREATE TABLE trash_tokens
(
  id SERIAL PRIMARY KEY,
  token VARCHAR(1000) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT NOW()
);


CREATE TABLE user_avatars
(
  id SERIAL PRIMARY KEY,
  user__id int4 NOT NULL,
  first_initial VARCHAR(5) NOT NULL,
  bg_color_hex VARCHAR(10) NOT NULL,
  side_color_hex VARCHAR(10) NOT NULL,
  stroke_color_hex VARCHAR(10) NOT NULL,
  is_active BOOL NOT NULL DEFAULT true,
  img_url VARCHAR(500) NULL,
  created_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
  updated_at TIMESTAMP(0) NULL
);