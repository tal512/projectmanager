CREATE TABLE user (
	id INT UNSIGNED AUTO_INCREMENT,
	email VARCHAR(254) NOT NULL,
	password VARCHAR(255) NOT NULL,
	name VARCHAR(255) NOT NULL,
	public_key VARCHAR(255) NOT NULL DEFAULT '',
	private_key VARCHAR(255) NOT NULL DEFAULT '',
	deleted DATETIME,

	PRIMARY KEY (id),
	INDEX (email),
	INDEX (public_key),
	INDEX (deleted)
) Engine InnoDB DEFAULT COLLATE utf8_swedish_ci;