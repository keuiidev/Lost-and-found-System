-- Lost and Found System database
-- Import this file in phpMyAdmin (XAMPP or InfinityFree).
-- On InfinityFree: create the database in the control panel first, select it in phpMyAdmin,
-- then import this file. (Remove the CREATE DATABASE / USE lines if your host does not allow them.)


-- ---------- Items table ----------
CREATE TABLE IF NOT EXISTS items (
    id             INT(11) NOT NULL AUTO_INCREMENT,       -- primary key
    item_name      VARCHAR(100) NOT NULL,                 -- e.g. Black Umbrella
    category       VARCHAR(50)  NOT NULL,                 -- e.g. Bag, Phone, ID, Keys
    description    TEXT NULL,                             -- color, brand, marks
    location       VARCHAR(150) NOT NULL,                 -- where it was lost / found
    date_reported  DATE NOT NULL,                         -- date lost / found
    status         ENUM('Lost','Found','Claimed') NOT NULL DEFAULT 'Lost',
    contact_name   VARCHAR(100) NOT NULL,                 -- person who reported it
    contact_number VARCHAR(20)  NOT NULL,
    found_by       VARCHAR(100) NULL,                     -- person who found the item (optional)
    photo          VARCHAR(255) NULL,                     -- photo file name in uploads/ (optional)
    created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

-- ---------- Admins table ----------
CREATE TABLE IF NOT EXISTS admins (
    id         INT(11) NOT NULL AUTO_INCREMENT,           -- primary key
    username   VARCHAR(50)  NOT NULL UNIQUE,              -- login name
    password   VARCHAR(255) NOT NULL,                     -- hashed password (never plain text)
    full_name  VARCHAR(100) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

-- Default admin account:  username = admin   password = admin123
-- Change the password after your first login (Dashboard -> Change Password).
INSERT IGNORE INTO admins (username, password, full_name) VALUES
('admin', '$2y$10$uaFNfT6jBUfGX0xzoPWON.tT8uSTPYIXuiQKV6xmGOkW7YJTNOuHW', 'Administrator');

-- Sample items (optional)
INSERT INTO items (item_name, category, description, location, date_reported, status, contact_name, contact_number, found_by) VALUES
('Black Umbrella', 'Others', 'Folding umbrella with a red handle', 'Library entrance', '2026-11-10', 'Found', 'Maria Santos', '09171234567', 'Maria Santos'),
('Student ID', 'ID', 'ID of a 2nd year BSIT student', 'Canteen', '2026-11-12', 'Found', 'Juan Dela Cruz', '09181234567', 'Canteen staff'),
('Blue Water Bottle', 'Bottle', 'Hydro Flask with a sticker on the side', 'Gymnasium', '2026-11-13', 'Lost', 'Ana Reyes', '09191234567', NULL);
