# Property-Escrow-with-Blockchain
The project serves as a final submission for the CSGY9223 A Introduction to Blockchain and Distributed Ledger Technology. The project demonstrates a system where a user can upload a real estate listing, verify it with blockchain adn then enforce an escrow smart contract for the buyer. The solution describes a secure and verifiable real estate sale/purchase solution
![Credits- DALL.E](https://github.com/harshitrajpal/Property-Escrow-with-Blockchain/blob/main/photo.png)
## Setting up the project
<h3>Setting up MySQL first</h3>

<br>apt-get install mysql-server
<br><br>sudo systemctl start mysqld
<br>sudo systemctl enable mysqld
<br><br>mysql -u root -p
<br>CREATE DATABASE smartestate;
<br>USE smartestate;
<br><br>CREATE TABLE users (
    <br>id INT AUTO_INCREMENT PRIMARY KEY,
    <br>username VARCHAR(50) NOT NULL UNIQUE,
    <br>password VARCHAR(255) NOT NULL,
    <br>account VARCHAR(255) NOT NULL UNIQUE
<br>);
<br><br>CREATE TABLE properties (
    <br>id INT AUTO_INCREMENT PRIMARY KEY,
    <br>user_id INT,
    <br>address VARCHAR(255) NOT NULL,
    <br>price DECIMAL(10, 2) NOT NULL,
    <br>image_path VARCHAR(255),
    <br>FOREIGN KEY (user_id) REFERENCES users(id)
<br>);
<br>
<br>sudo systemctl stop mysql
<br>sudo mysqld_safe --skip-grant-tables &
