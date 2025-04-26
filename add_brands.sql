USE electrician_db;

INSERT INTO brands (name) VALUES 
('Havells'),
('Crompton'),
('Philips'),
('Anchor'),
('Polycab'),
('Syska'),
('Orient'),
('Bajaj'),
('Schneider'),
('Legrand'),
('V-Guard'),
('Wipro'),
('GM'),
('HPL')
ON DUPLICATE KEY UPDATE name = VALUES(name);
