create database if not exists fitness_manager_app;

use fitness_manager_app;

create table activities
(
  id               int auto_increment
    primary key,
  name             varchar(255) not null,
  created_at       datetime     not null,
  updated_at       datetime     not null,
  valid_from       datetime     not null,
  valid_to         datetime     not null,
  capacity         int          not null,
  current_capacity int          not null
)
  collate = utf8_unicode_ci;

create table roles
(
  id   int auto_increment
    primary key,
  name varchar(255) null,
  constraint UNIQ_B63E2EC75E237E06
    unique (name)
)
  collate = utf8_unicode_ci;

create table users
(
  id         int auto_increment
    primary key,
  username   varchar(255) not null,
  password   varchar(191) not null,
  email      varchar(255) not null,
  created_at datetime     not null,
  updated_at datetime     not null,
  enabled    tinyint(1)   null,
  constraint UNIQ_1483A5E9E7927C74
    unique (email),
  constraint UNIQ_1483A5E9F85E0677
    unique (username)
)
  collate = utf8_unicode_ci;

create table cards
(
  id          int auto_increment
    primary key,
  user_id     int          not null,
  card_number varchar(255) not null,
  created_at  datetime     not null,
  updated_at  datetime     not null,
  valid_from  datetime     not null,
  valid_to    datetime     not null,
  constraint FK_4C258FDA76ED395
    foreign key (user_id) references users (id)
)
  collate = utf8_unicode_ci;

create table card_orders
(
  id           int auto_increment
    primary key,
  card_id      int      not null,
  start_date   datetime not null,
  due_date     datetime not null,
  visits_order int      not null,
  visits_left  int      not null,
  constraint FK_9710AE9A4ACC9A20
    foreign key (card_id) references cards (id)
)
  collate = utf8_unicode_ci;

create index IDX_9710AE9A4ACC9A20
  on card_orders (card_id);

create index IDX_4C258FDA76ED395
  on cards (user_id);

create table clients_users
(
  user_id   int not null,
  client_id int not null,
  primary key (user_id, client_id),
  constraint FK_C300512E19EB6921
    foreign key (client_id) references activities (id),
  constraint FK_C300512EA76ED395
    foreign key (user_id) references users (id)
)
  collate = utf8_unicode_ci;

create index IDX_C300512E19EB6921
  on clients_users (client_id);

create index IDX_C300512EA76ED395
  on clients_users (user_id);

create table roles_users
(
  user_id int not null,
  role_id int not null,
  primary key (user_id, role_id),
  constraint FK_3D80FB2CA76ED395
    foreign key (user_id) references users (id),
  constraint FK_3D80FB2CD60322AC
    foreign key (role_id) references roles (id)
)
  collate = utf8_unicode_ci;

create index IDX_3D80FB2CA76ED395
  on roles_users (user_id);

create index IDX_3D80FB2CD60322AC
  on roles_users (role_id);

create table trainers_users
(
  user_id    int not null,
  trainer_id int not null,
  primary key (user_id, trainer_id),
  constraint FK_A5E84029A76ED395
    foreign key (user_id) references users (id),
  constraint FK_A5E84029FB08EDF6
    foreign key (trainer_id) references activities (id)
)
  collate = utf8_unicode_ci;

create index IDX_A5E84029A76ED395
  on trainers_users (user_id);

create index IDX_A5E84029FB08EDF6
  on trainers_users (trainer_id);

INSERT INTO roles (name)
VALUES ('ROLE_SUPER_ADMIN'),
       ('ROLE_RECEPTIONIST'),
       ('ROLE_TRAINER'),
       ('ROLE_CLIENT');
