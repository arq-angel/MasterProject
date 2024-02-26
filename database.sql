CREATE TABLE IF NOT EXISTS users(
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    email varchar(248) NOT NULL,
    password varchar(248) NOT NULL,
    age tinyint(3) unsigned NOT NULL,
    country varchar(248) NOT NULL,
    social_media_url varchar(248) NOT NULL,
    created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    PRIMARY KEY(id),
    UNIQUE KEY(email)
);
