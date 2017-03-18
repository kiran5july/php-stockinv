create database mycompany;
use mycompany;



grant select, insert, update, delete
on mycompany.*
to km@localhost identified by 'km@1234';
