create table #prefix#payment (
	id integer primary key,
	user_id int not null,
	stripe_id char(32) not null,
	description char(128) not null,
	amount int not null,
	plan char(24) not null,
	ts datetime not null,
	ip int not null,
	type char(24) not null,
	email char(72) not null default '',
	billing_name char(72) not null default '',
	billing_address char(72) not null default '',
	billing_city char(72) not null default '',
	billing_state char(72) not null default '',
	billing_country char(72) not null default '',
	billing_zip char(72) not null default '',
	shipping_name char(72) not null default '',
	shipping_address char(72) not null default '',
	shipping_city char(72) not null default '',
	shipping_state char(72) not null default '',
	shipping_country char(72) not null default '',
	shipping_zip char(72) not null default ''
);

create index #prefix#payment_ts on #prefix#payment (ts);
create index #prefix#payment_user on #prefix#payment (user_id, ts);
create index #prefix#payment_stripe on #prefix#payment (stripe_id, ts);
