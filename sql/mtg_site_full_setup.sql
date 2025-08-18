-- One-shot setup for a fresh environment (schema + updates + mock data)

DROP DATABASE IF EXISTS mtg_site;
CREATE DATABASE IF NOT EXISTS mtg_site;
USE mtg_site;

-- users table uses plaintext password
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    avatar VARCHAR(255) NULL
);

CREATE TABLE IF NOT EXISTS inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    card_name VARCHAR(100),
    quantity INT DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES users(id),
    UNIQUE KEY uniq_user_card (user_id, card_name)
);

CREATE TABLE IF NOT EXISTS friends (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    friend_username VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS decks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS deck_cards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    deck_id INT NOT NULL,
    inventory_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    FOREIGN KEY (deck_id) REFERENCES decks(id) ON DELETE CASCADE,
    FOREIGN KEY (inventory_id) REFERENCES inventory(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_deck_inventory (deck_id, inventory_id)
);

-- mock users (password is the same as username)
INSERT INTO users (username, password, avatar) VALUES
('user1','user1','/mtg-website/img/avatars/avatar1.svg'),
('user2','user2','/mtg-website/img/avatars/avatar2.svg'),
('user3','user3','/mtg-website/img/avatars/avatar3.svg'),
('user4','user4','/mtg-website/img/avatars/avatar4.svg'),
('user5','user5','/mtg-website/img/avatars/avatar5.svg'),
('user6','user6','/mtg-website/img/avatars/avatar6.svg'),
('user7','user7',NULL),
('user8','user8',NULL),
('user9','user9',NULL),
('user10','user10',NULL);

-- mock inventory for user1
INSERT INTO inventory (user_id, card_name, quantity) VALUES
(1, 'Lightning Bolt', 2),
(1, 'Counterspell', 2),
(1, 'Llanowar Elves', 4),
(1, 'Dark Ritual', 3),
(1, 'Giant Growth', 2),
(1, 'Sol Ring', 1),
(1, 'Wrath of God', 1),
(1, 'Path to Exile', 2),
(1, 'Shivan Dragon', 1),
(1, 'Serra Angel', 1);

-- sample deck for user1
INSERT INTO decks (user_id, name) VALUES (1, 'Sample Deck');

-- some cards moved into the deck (deduct from inventory in app when assigning)
INSERT INTO deck_cards (deck_id, inventory_id, quantity)
SELECT 1, i.id, 1 FROM inventory i WHERE i.user_id=1 AND i.card_name IN ('Lightning Bolt','Counterspell','Llanowar Elves');
