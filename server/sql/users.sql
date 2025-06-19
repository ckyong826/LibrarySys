-- Users table for authentication
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    role VARCHAR(255) NOT NULL
);

-- Insert a test user (password is 'password123')
INSERT INTO users (name, email, password) VALUES 
('Test User', 'test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Sample users for Library Management System
INSERT INTO users (id, name, email, password, role) VALUES
(1, 'Librarian', 'librarian@library.com', '$2y$10$examplelibrarianhash', 'librarian'),
(2, 'John Doe', 'john@library.com', '$2y$10$examplememberhash', 'member');
-- Replace the password hashes with real bcrypt hashes in production. 