-- Run this ONLY if you already imported the previous database.sql
-- (the version that has the found_by and photo columns).
-- It adds the admins table and the default admin account, and keeps your items.
-- phpMyAdmin: click lost_and_found_db -> SQL tab -> paste -> Go

CREATE TABLE IF NOT EXISTS admins (
    id         INT(11) NOT NULL AUTO_INCREMENT,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    full_name  VARCHAR(100) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

-- Default admin account:  username = admin   password = admin123
INSERT IGNORE INTO admins (username, password, full_name) VALUES
('admin', '$2y$10$uaFNfT6jBUfGX0xzoPWON.tT8uSTPYIXuiQKV6xmGOkW7YJTNOuHW', 'Administrator');
