# Property-Escrow-with-Blockchain

# Set up steps
## Setting up MySQL first

<br>apt-get install mysql-server
<br>sudo systemctl start mysqld
<br>sudo systemctl enable mysqld
<br>mysql -u root -p
<br>CREATE DATABASE smartestate;
<br>USE smartestate;
<br>CREATE TABLE users (
    <br>id INT AUTO_INCREMENT PRIMARY KEY,
    <br>username VARCHAR(50) NOT NULL UNIQUE,
    <br>password VARCHAR(255) NOT NULL,
    <br>account VARCHAR(255) NOT NULL UNIQUE
<br>);
<br>CREATE TABLE properties (
    <br>id INT AUTO_INCREMENT PRIMARY KEY,
    <br>user_id INT,
    <br>address VARCHAR(255) NOT NULL,
    <br>price DECIMAL(10, 2) NOT NULL,
    <br>image_path VARCHAR(255),
    <br>FOREIGN KEY (user_id) REFERENCES users(id)
<br>);
