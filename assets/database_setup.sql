CREATE DATABASE IF NOT EXISTS OnlineLibrary;
USE OnlineLibrary;

CREATE TABLE Users (
                       user_id INT AUTO_INCREMENT PRIMARY KEY,
                       username VARCHAR(50) NOT NULL UNIQUE,
                       password VARCHAR(255) NOT NULL,
                       email VARCHAR(100) NOT NULL UNIQUE,
                       registration_date DATE NOT NULL
);

CREATE TABLE Authors (
                         author_id INT AUTO_INCREMENT PRIMARY KEY,
                         name VARCHAR(255) NOT NULL
);


CREATE TABLE Genres (
                        genre_id INT AUTO_INCREMENT PRIMARY KEY,
                        genre_name VARCHAR(50) NOT NULL
);

CREATE TABLE Books (
                       book_id INT AUTO_INCREMENT PRIMARY KEY,
                       title VARCHAR(255) NOT NULL,
                       author VARCHAR(255) NOT NULL,
                       path VARCHAR(255) NOT NULL,
                       cover_image VARCHAR(255),
                       author_id INT,
                       genre_id INT,
                       publication_year INT,
                       description TEXT,
                       FOREIGN KEY (author_id) REFERENCES Authors(author_id),
                       FOREIGN KEY (genre_id) REFERENCES Genres(genre_id)
);

CREATE TABLE UserBooks (
                           userbook_id INT AUTO_INCREMENT PRIMARY KEY,
                           user_id INT,
                           book_id INT,
                           status ENUM('planned', 'read', 'reading') NOT NULL,
                           UNIQUE KEY unique_user_book (user_id, book_id)
);
