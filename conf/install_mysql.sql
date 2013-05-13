create table #prefix#payment (
	id int not null auto_increment primary key,
	user_id int not null,
	stripe_id char(32) not null,
	description char(128) not null,
	amount int not null,
	plan char(24) not null,
	ts datetime not null,
	ip int not null,
	type char(24) not null,
	index (ts),
	index (user_id, ts),
	index (stripe_id, ts)
);