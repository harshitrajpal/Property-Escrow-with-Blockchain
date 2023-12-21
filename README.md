# Property-Escrow-with-Blockchain

#Set up steps
##Setting up MySQL first

apt-get install mysql-server
sudo systemctl start mysqld
sudo systemctl enable mysqld
mysql -u root -p
CREATE DATABASE smartestate;
USE smartestate;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    account VARCHAR(255) NOT NULL UNIQUE
);
CREATE TABLE properties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    address VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    image_path VARCHAR(255),  -- Path to the stored image
    FOREIGN KEY (user_id) REFERENCES users(id)
);
