--Don't use this table anymore
CREATE TABLE trash_tokens
(
  id SERIAL PRIMARY KEY,
  token VARCHAR(1000) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT NOW()
);