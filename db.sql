-- auto-generated definition
create table if not exists users
(
  id          int auto_increment
    primary key,
  username    varchar(255) not null,
  password    varchar(255) not null,
  first_name  varchar(255) not null,
  last_name   varchar(255) not null,
  email       varchar(50)  not null,
  phone       varchar(50)  null,
  gender      varchar(1)   not null,
  data_create varchar(255) not null,
  constraint UNIQ_1483A5E9E7927C74
    unique (email),
  constraint UNIQ_1483A5E9F85E0677
    unique (username)
)
  collate = utf8_unicode_ci;

create table if not exists articles
(
  id         int auto_increment
    primary key,
  title      varchar(255) not null,
  content    longtext     not null,
  date_added datetime     not null,
  author_id  int          not null,
  constraint FK_BFDD3168A196F9FD
    foreign key (author_id) references users (id)
)
  collate = utf8_unicode_ci;

create index IDX_BFDD3168A196F9FD
  on articles (author_id);