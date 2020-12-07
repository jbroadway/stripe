alter table #prefix#payment change column stripe_id stripe_id char(255) not null;
alter table #prefix#payment change column plan plan char(255) not null;
