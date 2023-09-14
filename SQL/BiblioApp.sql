/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

 /**
 * Script for database creation.
 *
 * @author José Antonio Cuello Principal <yopli2000@gmail.com>
 * @version 1.0
 */

/* create database */
CREATE DATABASE biblioapp
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_spanish_ci
    DEFAULT ENCRYPTION='N';

/**
 * Create users table.
 * The users are the people who can access to the application
 * for manage the users, members, books and other data.
 */
CREATE TABLE users (
    email varchar(100),
    enabled bool NOT NULL DEFAULT true,
    logkey varchar(100),
    name varchar(100) NOT NULL,
    username varchar(50) NOT NULL,
    password varchar(255) NOT NULL,
    CONSTRAINT users_pk PRIMARY KEY (username)
);

INSERT INTO users (name, username, password) VALUES ('Administrador', 'admin', '$2y$10$/dO7uCXqeqVe32O1GRDFT.f6tN3VCSaKvF9oFwLfOsxzn9NF7HDJy');


/**
 * Create members table.
 * The members are the people who can borrow books.
 */
CREATE TABLE members (
     id int NOT NULL AUTO_INCREMENT,
     name varchar(100) NOT NULL,
     phone varchar(10) NOT NULL,
     CONSTRAINT members_pk PRIMARY KEY (id)
);

CREATE INDEX members_idx_name ON members (name);

/**
 * Create categories table.
 * The categories are the groups of books depending of his theme.
 */
CREATE TABLE categories (
    id int NOT NULL AUTO_INCREMENT,
    name varchar(100) NOT NULL,
    CONSTRAINT categories_pk PRIMARY KEY (id)
);

/**
 * Create books table.
 * The books are the main data of the application.
 */
CREATE TABLE books (
    author varchar(100) NOT NULL,
    editorial varchar(100) NOT NULL,
    id int NOT NULL AUTO_INCREMENT,
    isbn varchar(13) NOT NULL,
    pages int NOT NULL,
    publication int NOT NULL,
    synopsis text NOT NULL,
    title varchar(100) NOT NULL,
    CONSTRAINT books_pk PRIMARY KEY (id)
);

CREATE UNIQUE INDEX books_idx_isbn ON books (isbn);
CREATE INDEX books_idx_title ON books (title);

-- TODO: create table for book images


/**
 * Create books_categories table.
 * This table is the relation between books and categories.
 */
CREATE TABLE books_categories (
    book_id int NOT NULL,
    category_id int NOT NULL,
    CONSTRAINT books_categories_pk PRIMARY KEY (book_id, category_id),
    CONSTRAINT books_categories_book_fk FOREIGN KEY (book_id) REFERENCES books (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT books_categories_category_fk FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE CASCADE ON UPDATE CASCADE
);

/**
 * Create loans table.
 * This table stores book loans made by members.
 */
CREATE TABLE loans (
    book_id int NOT NULL,
    id int NOT NULL AUTO_INCREMENT,
    loan_date date NOT NULL,
    member_id int NOT NULL,
    return_date date NOT NULL,
    CONSTRAINT loans_pk PRIMARY KEY (id),
    CONSTRAINT loans_book_fk FOREIGN KEY (book_id) REFERENCES books (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT loans_member_fk FOREIGN KEY (member_id) REFERENCES members (id) ON DELETE CASCADE ON UPDATE CASCADE
);

/**
 * Create ratings table.
 * This table stores book ratings made by members.
 */
CREATE TABLE ratings (
    book_id int NOT NULL,
    id int NOT NULL AUTO_INCREMENT,
    member_id int NOT NULL,
    rating int NOT NULL,
    rating_date date NOT NULL,
    rating_time time NOT NULL,
    valoration text NOT NULL,
    CONSTRAINT ratings_pk PRIMARY KEY (id),
    CONSTRAINT ratings_book_fk FOREIGN KEY (book_id) REFERENCES books (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT ratings_member_fk FOREIGN KEY (member_id) REFERENCES members (id) ON DELETE CASCADE ON UPDATE CASCADE
);