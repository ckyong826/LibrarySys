-- Create database if not exists
CREATE DATABASE IF NOT EXISTS libraryManagement;
USE libraryManagement;
-- Library Management System Schema

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('member', 'librarian') DEFAULT 'member',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Authors table
CREATE TABLE IF NOT EXISTS authors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    bio TEXT
);

-- Books table
CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    author_id INT NOT NULL,
    isbn VARCHAR(20) UNIQUE,
    published_year INT,
    available_copies INT DEFAULT 1,
    total_copies INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES authors(id) ON DELETE CASCADE
);

-- Loans table
CREATE TABLE IF NOT EXISTS loans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    loan_date DATE NOT NULL,
    due_date DATE NOT NULL,
    return_date DATE,
    status ENUM('borrowed', 'returned', 'overdue') DEFAULT 'borrowed',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);

-- Seed data for authors
INSERT INTO authors (name, bio) VALUES
('J.K. Rowling', 'British author, best known for the Harry Potter series.'),
('George Orwell', 'English novelist, essayist, journalist and critic.'),
('Jane Austen', 'English novelist known for her six major novels.');

-- Seed data for books
INSERT INTO books (title, author_id, isbn, published_year, available_copies, total_copies) VALUES
('Harry Potter and the Sorcerer''s Stone', 1, '9780747532699', 1997, 3, 5),
('1984', 2, '9780451524935', 1949, 2, 2),
('Pride and Prejudice', 3, '9780141439518', 1813, 1, 3);

-- Seed data for loans
INSERT INTO loans (user_id, book_id, loan_date, due_date, return_date, status) VALUES
(1, 1, '2024-06-01', '2024-06-15', NULL, 'borrowed'),
(2, 2, '2024-05-20', '2024-06-03', '2024-06-02', 'returned'); 