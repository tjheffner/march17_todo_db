## Developers
Tanner Heffner && John Franti

# App
This to-do app is a continuation of the march17_todo repository with added database support.
It keeps a list of tasks and categories which can be linked together for fun and profit.

# PSQL stuff
CREATE DATABASE to_do;
\c to_do
CREATE TABLE tasks (id serial PRIMARY KEY, description varchar);
CREATE TABLE categories (id serial PRIMARY KEY, name varchar);
CREATE TABLE categories_tasks (id serial PRIMARY KEY, category_id int, task_id int);
CREATE DATABASE to_do_test WITH TEMPLATE to_do;
