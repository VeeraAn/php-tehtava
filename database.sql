drop database if exists webbilomake;

create database webbilomake;

use webbilomake;

create table info (
    email varchar(100) unique not null
);