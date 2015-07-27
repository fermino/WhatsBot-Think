CREATE TABLE `thinks` (
	`id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	`from`	TEXT,
	`user`	TEXT,
	`mid`	TEXT,
	`time`	INTEGER,
	`name`	TEXT,
	`think`	TEXT NOT NULL,
	`ip`	INTEGER
)