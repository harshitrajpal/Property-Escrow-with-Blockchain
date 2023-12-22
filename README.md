# Property-Escrow-with-Blockchain
The project serves as a final submission for the CSGY9223 A Introduction to Blockchain and Distributed Ledger Technology. The project demonstrates a system where a user can upload a real estate listing, verify it with blockchain adn then enforce an escrow smart contract for the buyer. The solution describes a secure and verifiable real estate sale/purchase solution
![Credits- DALL.E](https://github.com/harshitrajpal/Property-Escrow-with-Blockchain/blob/main/photo.png)

## Setting up the project
<h3>Setting up the requirementst</h3>
<h5>Automated Databse setup coming soon...</h5>
<br>apt-get install mysql-server
<br>apt install apache2
<br>apt install php
<br>cd /var/www/html
<br>mkdir image_uploads
<br>mkdir registry_uploads
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
    <br>paper_hash VARCHAR(255),
    <br>paper_path VARCHAR(255),
    <br>FOREIGN KEY (user_id) REFERENCES users(id)
<br>);
<br>
<br>CREATE USER 'hex'@'localhost' IDENTIFIED BY '123';
<br>GRANT ALL PRIVILEGES ON smartestate.* TO 'hex'@'localhost';
<br>FLUSH PRIVILEGES;
<br>EXIT;

<br>mysql -u hex -p smartestate
<br>->password: 123

<br><br><b>Make sure the third input is the valid test ethereum wallet address!</b>
<br><br>signup page in progress to automate wallet address insertion<br><br>
<br>INSERT INTO users (username, password, account) VALUES ('alice', '123', '0x78fA07....g46aA2');
<br>INSERT INTO users (username, password, account) VALUES ('bob', '12345', '0x5b437b8fA07...f864a2A');
