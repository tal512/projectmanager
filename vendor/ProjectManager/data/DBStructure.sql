CREATE TABLE user (
	id INT UNSIGNED AUTO_INCREMENT,
	email VARCHAR(254) NOT NULL,
	password VARCHAR(255) NOT NULL,
	name VARCHAR(255) NOT NULL,
	public_key VARCHAR(255) NOT NULL DEFAULT '',
	private_key VARCHAR(255) NOT NULL DEFAULT '',
	deleted TINYINT UNSIGNED NOT NULL DEFAULT 0,

	PRIMARY KEY (id),
	INDEX (email),
	INDEX (public_key),
	INDEX (deleted)
) Engine InnoDB DEFAULT COLLATE utf8_swedish_ci;


CREATE TABLE role (
	id INT UNSIGNED AUTO_INCREMENT,
	name VARCHAR(255) NOT NULL,

	PRIMARY KEY (id)
) Engine InnoDB DEFAULT COLLATE utf8_swedish_ci;
INSERT INTO role (id, name) VALUES (1, 'admin');
INSERT INTO role (id, name) VALUES (2, 'member');


CREATE TABLE user_role (
	id INT UNSIGNED AUTO_INCREMENT,
	user_id INT UNSIGNED NOT NULL,
	role_id INT UNSIGNED NOT NULL,
	deleted TINYINT UNSIGNED NOT NULL DEFAULT 0,

	PRIMARY KEY (id),
	FOREIGN KEY (user_id) REFERENCES user(id),
	FOREIGN KEY (role_id) REFERENCES role(id),
	INDEX (deleted)
) Engine InnoDB DEFAULT COLLATE utf8_swedish_ci;


CREATE TABLE project_status (
	id INT UNSIGNED AUTO_INCREMENT,
	name VARCHAR(255) NOT NULL,

	PRIMARY KEY (id)
) Engine InnoDB DEFAULT COLLATE utf8_swedish_ci;
INSERT INTO project_status (id, name) VALUES (1, 'open');
INSERT INTO project_status (id, name) VALUES (2, 'closed');


CREATE TABLE project (
	id INT UNSIGNED AUTO_INCREMENT,
	project_status_id INT UNSIGNED NOT NULL,
	name VARCHAR(255) NOT NULL,
	description TEXT,
	deleted TINYINT UNSIGNED NOT NULL DEFAULT 0,

	PRIMARY KEY (id),
	FOREIGN KEY (project_status_id) REFERENCES project_status(id),
	INDEX (deleted)
) Engine InnoDB DEFAULT COLLATE utf8_swedish_ci;


CREATE TABLE user_project_role (
	id INT UNSIGNED AUTO_INCREMENT,
	user_id INT UNSIGNED NOT NULL,
	project_id INT UNSIGNED NOT NULL,
	role_id INT UNSIGNED NOT NULL,
	deleted TINYINT UNSIGNED NOT NULL DEFAULT 0,

	PRIMARY KEY (id),
	FOREIGN KEY (user_id) REFERENCES user(id),
	FOREIGN KEY (project_id) REFERENCES project(id),
	FOREIGN KEY (role_id) REFERENCES role(id),
	INDEX (deleted)
) Engine InnoDB DEFAULT COLLATE utf8_swedish_ci;


CREATE TABLE task_status (
	id INT UNSIGNED AUTO_INCREMENT,
	name VARCHAR(255) NOT NULL,

	PRIMARY KEY (id)
) Engine InnoDB DEFAULT COLLATE utf8_swedish_ci;
INSERT INTO task_status (id, name) VALUES (1, 'new');
INSERT INTO task_status (id, name) VALUES (2, 'in_progress');
INSERT INTO task_status (id, name) VALUES (3, 'testing');
INSERT INTO task_status (id, name) VALUES (4, 'feedback');
INSERT INTO task_status (id, name) VALUES (5, 'resolved');
INSERT INTO task_status (id, name) VALUES (6, 'rejected');


CREATE TABLE task_type (
	id INT UNSIGNED AUTO_INCREMENT,
	name VARCHAR(255) NOT NULL,

	PRIMARY KEY (id)
) Engine InnoDB DEFAULT COLLATE utf8_swedish_ci;
INSERT INTO task_type (id, name) VALUES (1, 'feature');
INSERT INTO task_type (id, name) VALUES (2, 'bug');


CREATE TABLE task (
	id INT UNSIGNED AUTO_INCREMENT,
	project_id INT UNSIGNED NOT NULL,
	parent_task_id INT UNSIGNED,
	author_id INT UNSIGNED NOT NULL,
	assignee_id INT UNSIGNED,
	task_status_id INT UNSIGNED NOT NULL,
	task_type_id INT UNSIGNED NOT NULL,
	short_description VARCHAR(255) NOT NULL,
	long_description TEXT,
	priority INT UNSIGNED NOT NULL DEFAULT 0,
	deleted TINYINT UNSIGNED NOT NULL DEFAULT 0,

	PRIMARY KEY (id),
	FOREIGN KEY (project_id) REFERENCES project(id),
	FOREIGN KEY (parent_task_id) REFERENCES task(id),
	FOREIGN KEY (author_id) REFERENCES user(id),
	FOREIGN KEY (assignee_id) REFERENCES user(id),
	FOREIGN KEY (task_status_id) REFERENCES task_status(id),
	FOREIGN KEY (task_type_id) REFERENCES task_type(id),
	INDEX (deleted)
) Engine InnoDB DEFAULT COLLATE utf8_swedish_ci;
