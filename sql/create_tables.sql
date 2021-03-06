CREATE TABLE IF NOT EXISTS users (
	name VARCHAR(50) NOT NULL,
	token CHAR(16) NOT NULL,
	last_activity TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	CONSTRAINT pk_users PRIMARY KEY (name)
);

CREATE TABLE IF NOT EXISTS messages (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT KEY,
	time TIMESTAMP,
	user_name VARCHAR(50) NOT NULL,
	text TEXT(1000),
	CONSTRAINT fk_user_name FOREIGN KEY (user_name) REFERENCES users(name)
);

