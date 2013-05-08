create table #prefix#payment (
	id integer primary key,
	user_id int not null,
	stripe_id char(32) not null,
	description char(128) not null,
	amount int not null,
	plan char(24) not null,
	ts datetime not null
);

create index #prefix#payment_ts on #prefix#payment (ts);
create index #prefix#payment_user on #prefix#payment (user_id, ts);
create index #prefix#payment_stripe on #prefix#payment (stripe_id, ts);
