CREATE DATABASE customer_api;

USE customer_api;

CREATE TABLE customers (
                           id INT AUTO_INCREMENT PRIMARY KEY,
                           name VARCHAR(100) NOT NULL,
                           surname VARCHAR(100) NOT NULL,
                           balance DECIMAL(10, 2) NOT NULL DEFAULT 0
);

CREATE TABLE transactions (
                              id INT AUTO_INCREMENT PRIMARY KEY,
                              customer_id INT,
                              type ENUM('deposit', 'withdraw', 'transfer') NOT NULL,
                              amount DECIMAL(10, 2) NOT NULL,
                              created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                              FOREIGN KEY (customer_id) REFERENCES customers(id)
);