USE electrician_db;

INSERT INTO categories (name) VALUES 
('Switches'),
('LED Bulbs'),
('Tubelights'),
('MCB'),
('Wires'),
('Sockets'),
('Fans'),
('Meters'),
('Circuit Breakers'),
('Conduits'),
('Distribution Boards'),
('Cables')
ON DUPLICATE KEY UPDATE name = VALUES(name);
