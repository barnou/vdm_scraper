CREATE TABLE posts (
post_uid serial primary key,
post_datelog timestamp without time zone not null unique,
post_content text not null,
post_author text,
post_city text,
post_country text
);
COMMENT ON TABLE posts IS 'Table to store vdm posts';