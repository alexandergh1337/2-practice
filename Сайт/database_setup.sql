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
                         name VARCHAR(100) NOT NULL,
                         biography TEXT
);

CREATE TABLE Genres (
                        genre_id INT AUTO_INCREMENT PRIMARY KEY,
                        genre_name VARCHAR(50) NOT NULL
);

CREATE TABLE Books (
                       book_id INT AUTO_INCREMENT PRIMARY KEY,
                       title VARCHAR(255) NOT NULL,
                       author_id INT,
                       genre_id INT,
                       publication_year INT,
                       description TEXT,
                       FOREIGN KEY (author_id) REFERENCES Authors(author_id),
                       FOREIGN KEY (genre_id) REFERENCES Genres(genre_id)
);

CREATE TABLE BookPages (
                           page_id INT AUTO_INCREMENT PRIMARY KEY,
                           book_id INT,
                           page_number INT,
                           content TEXT,
                           FOREIGN KEY (book_id) REFERENCES Books(book_id)
);

CREATE TABLE Loans (
                       loan_id INT AUTO_INCREMENT PRIMARY KEY,
                       user_id INT,
                       book_id INT,
                       loan_date DATE NOT NULL,
                       return_date DATE,
                       FOREIGN KEY (user_id) REFERENCES Users(user_id),
                       FOREIGN KEY (book_id) REFERENCES Books(book_id)
);
