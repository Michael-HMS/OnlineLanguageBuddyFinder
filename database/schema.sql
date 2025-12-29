CREATE DATABASE IF NOT EXISTS language_buddy_finder;
 USE language_buddy_finder;
 CREATE TABLE conversations (
         id INT AUTO_INCREMENT PRIMARY KEY,
         user1_id INT NOT NULL,
         user2_id INT NOT NULL,
         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
         );

CREATE TABLE messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        conversation_id INT NOT NULL,
        sender_id INT NOT NULL,
        message TEXT NOT NULL,
        sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );


    CREATE TABLE users (
         id INT AUTO_INCREMENT PRIMARY KEY,
         username VARCHAR(50) NOT NULL,
         email VARCHAR(100) UNIQUE NOT NULL,
         password VARCHAR(255) NOT NULL,
         native_language VARCHAR(50),
         learning_language VARCHAR(50),
         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

      );
               ALTER TABLE users
ADD status ENUM('active', 'banned', 'suspended')
NOT NULL DEFAULT 'active';