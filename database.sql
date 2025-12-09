create database perpustakaan;
use perpustakaan;

create table user (
    id int primary key auto_increment,
    name varchar(255) not null,
    email varchar(255) not null,
    password varchar(255) not null,
    role enum('admin', 'user') not null,
);

insert into user (name, email, password, role) values ('admin', 'admin@example.com', 'password', 'admin');

create table book (
    id int primary key auto_increment,
    title varchar(255) not null,
    author varchar(255) not null,
    cover varchar(255) not null,
    publisher varchar(255) not null,
    year int not null,
    stock int not null,
);

create table category (
    id int primary key auto_increment,
    name varchar(255) not null,
);

alter table book add column category_id int not null;
alter table book add foreign key (category_id) references category(id);


