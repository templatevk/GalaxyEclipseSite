create table players (
	player_id 		integer auto_increment primary key,
	username		varchar(16) not null unique key,
	password		varchar(64) not null,
	nickname		varchar(64) not null,
	email			varchar(64) not null,
	is_banned		bit default 0,
	is_activated 	bit default 0
);

create table players_activation_hashes (
	players_activation_hashes_id	integer auto_increment primary key,
	activation_hash					varchar(32) not null,
	player_id						integer not null
);

alter table players_activation_hashes
		add constraint fk_players_activation_hashes_player foreign key (player_id) references players (player_id);