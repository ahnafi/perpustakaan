CREATE DATABASE IF NOT EXISTS perpustakaan;
USE perpustakaan;

CREATE TABLE IF NOT EXISTS user (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert admin with hashed password (password: 'password')
INSERT INTO user (name, email, password, role) VALUES 
('Admin', 'admin@example.com', 'password', 'admin'),
('budi', 'budi@example.com', 'password', 'user');

CREATE TABLE IF NOT EXISTS category (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS book (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    cover VARCHAR(255) DEFAULT NULL,
    publisher VARCHAR(255) NOT NULL,
    year INT NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    category_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES category(id) ON DELETE RESTRICT
);

-- Insert sample categories
INSERT INTO category (name) VALUES 
('Fiksi'),
('Non-Fiksi'),
('Teknologi'),
('Sains'),
('Sejarah');

-- Insert sample books
INSERT INTO book (title, author, cover, publisher, year, stock, category_id) VALUES
('Laskar Pelangi', 'Andrea Hirata', NULL, 'Bentang Pustaka', 2005, 10, 1),
('Bumi Manusia', 'Pramoedya Ananta Toer', NULL, 'Hasta Mitra', 1980, 5, 1),
('Atomic Habits', 'James Clear', NULL, 'Penguin Random House', 2018, 8, 2),
('Clean Code', 'Robert C. Martin', NULL, 'Prentice Hall', 2008, 3, 3),
('Sapiens', 'Yuval Noah Harari', NULL, 'Harper', 2011, 6, 5);

-- Borrowing table
CREATE TABLE IF NOT EXISTS borrowing (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    borrow_date DATE NOT NULL,
    due_date DATE NOT NULL,
    return_date DATE DEFAULT NULL,
    phone VARCHAR(255) NOT NULL,
    address TEXT NOT NULL,
    status ENUM('borrowed', 'returned', 'overdue') NOT NULL DEFAULT 'borrowed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES book(id) ON DELETE CASCADE
);
