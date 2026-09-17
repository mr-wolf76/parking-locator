CREATE DATABASE IF NOT EXISTS parking_locator CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE parking_locator;

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS parking_facilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    address VARCHAR(255) NOT NULL,
    area VARCHAR(100) NOT NULL,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    payment_type ENUM('free', 'paid') NOT NULL DEFAULT 'paid',
    parking_fee VARCHAR(100) DEFAULT NULL,
    vehicle_type ENUM('car', 'motorcycle', 'both') NOT NULL DEFAULT 'both',
    opening_time TIME DEFAULT '07:00:00',
    closing_time TIME DEFAULT '21:00:00',
    contact VARCHAR(50) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    image VARCHAR(255) DEFAULT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    last_verified DATE NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS parking_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parking_id INT NOT NULL,
    issue_type VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    reporter_contact VARCHAR(100) DEFAULT NULL,
    status ENUM('pending', 'reviewed', 'resolved') NOT NULL DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parking_id) REFERENCES parking_facilities(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO admins (username, password, created_at) VALUES 
('admin', '$2y$10$FcYWiEB53kt68JdNVVs9S.bO//nIEhqh8VvSj6oHv9rYt6AQPElcS', NOW())
ON DUPLICATE KEY UPDATE username=username;

INSERT INTO parking_facilities (id, name, address, area, latitude, longitude, payment_type, parking_fee, vehicle_type, opening_time, closing_time, contact, description, image, status, last_verified, created_at) VALUES
(1, 'Chhaya Center Underground Parking', 'Amrit Marg, Thamel', 'Thamel', 27.71490000, 85.31220000, 'paid', 'Rs. 50/hr (Car), Rs. 25/hr (Bike)', 'both', '06:00:00', '23:00:00', '01-4256789', 'Multi-level basement parking with security personnel, lighting, and CCTV monitoring. Convenient for visitors to central Thamel and Chhaya Center shopping complex.', 'chhaya_center.jpg', 'active', '2026-09-01', NOW()),
(2, 'Thamel Marg Open Parking Area', 'Thamel Marg, Near Kathmandu Guest House', 'Thamel', 27.71610000, 85.31050000, 'paid', 'Rs. 25/hr (Bike)', 'motorcycle', '07:00:00', '22:00:00', '9841234567', 'Designated outdoor two-wheeler parking lot managed by local ward committee for tourist area visitors.', NULL, 'active', '2026-08-28', NOW()),
(3, 'Dharahara Underground Smart Parking', 'Khichapokhari, New Road', 'New Road', 27.70060000, 85.31220000, 'paid', 'Rs. 40/hr (Car), Rs. 20/hr (Bike)', 'both', '06:00:00', '22:00:00', '01-4221144', 'Spacious modern multi-level underground parking facility constructed near reconstructed Dharahara with dedicated lanes for two-wheelers and four-wheelers.', 'dharahara_parking.jpg', 'active', '2026-09-04', NOW()),
(4, 'Khichapokhari Municipal Bike Stand', 'Khichapokhari Marg', 'New Road', 27.70280000, 85.31170000, 'paid', 'Rs. 20/hr', 'motorcycle', '08:00:00', '20:30:00', NULL, 'Open municipal motorcycle stand located within walking distance of Bishal Bazar and New Road gate.', NULL, 'active', '2026-09-02', NOW()),
(5, 'Baneshwor Complex Customer Parking', 'New Baneshwor Chowk', 'Baneshwor', 27.69180000, 85.34210000, 'free', 'Free for customers', 'both', '08:00:00', '21:00:00', '01-4498877', 'Ground level dedicated customer parking lot situated behind commercial offices and banks at Baneshwor junction.', NULL, 'active', '2026-08-20', NOW()),
(6, 'Eyeplex Mall Basement Parking', 'Main Road, New Baneshwor', 'Baneshwor', 27.69020000, 85.33750000, 'paid', 'Rs. 50/hr (Car), Rs. 25/hr (Bike)', 'both', '08:30:00', '22:30:00', '01-4471234', 'Covered underground parking for moviegoers and commercial shoppers with paved ramps and guard assistance.', 'eyeplex_mall.jpg', 'active', '2026-09-05', NOW()),
(7, 'Putalisadak Commercial Complex Parking', 'Putalisadak Main Road', 'Putalisadak', 27.70580000, 85.32160000, 'paid', 'Rs. 40/hr (Car), Rs. 20/hr (Bike)', 'both', '07:30:00', '20:00:00', '9851098765', 'Off-street parking lot serving educational consultancies, computer hardware stores, and offices along Putalisadak corridor.', NULL, 'active', '2026-08-15', NOW()),
(8, 'City Center Multi-Story Parking', 'Kamalpokhari, Near Putalisadak', 'Putalisadak', 27.70880000, 85.32430000, 'paid', 'Rs. 50/hr (Car), Rs. 25/hr (Bike)', 'both', '09:00:00', '22:00:00', '01-4011555', 'Covered multi-level parking complex inside City Center shopping mall with elevator access and automated barrier gates.', 'city_center.jpg', 'active', '2026-09-03', NOW()),
(9, 'Kalanki Underpass Roadside Parking Bay', 'Kalanki Chowk West', 'Kalanki', 27.69370000, 85.28180000, 'free', 'Free public bay', 'both', '06:00:00', '20:00:00', NULL, 'Designated public roadside parking bay designated by traffic police near Kalanki junction for short halts.', NULL, 'active', '2026-08-10', NOW()),
(10, 'Makalu Complex Parking', 'Ring Road, Kalanki', 'Kalanki', 27.69520000, 85.28340000, 'paid', 'Rs. 30/hr (Car), Rs. 15/hr (Bike)', 'both', '07:00:00', '21:00:00', '9803123456', 'Paved commercial ground parking facility suitable for commuter vehicles and local shoppers.', NULL, 'active', '2026-08-30', NOW()),
(11, 'Labim Mall Underground Parking', 'Pulchowk, Lalitpur', 'Patan', 27.67780000, 85.31680000, 'paid', 'Rs. 60/hr (Car), Rs. 30/hr (Bike)', 'both', '08:00:00', '23:00:00', '01-5536789', 'Modern double-level basement facility with digital ticket validation, well-marked bays, and direct elevator access to mall floors.', 'labim_mall.jpg', 'active', '2026-09-05', NOW()),
(12, 'Mangal Bazar Heritage Parking Lot', 'Patan Durbar Square West', 'Patan', 27.67290000, 85.32450000, 'paid', 'Rs. 25/hr (Bike), Rs. 50/hr (Car)', 'both', '07:00:00', '20:00:00', '01-5522334', 'Designated municipal vehicle parking lot for visitors to Patan Durbar Square and surrounding handicraft markets.', NULL, 'active', '2026-09-02', NOW()),
(13, 'Bhatbhateni Maharajgunj Parking', 'Narayan Gopal Chowk, Maharajgunj', 'Maharajgunj', 27.73620000, 85.33120000, 'free', 'Free with store purchase', 'both', '07:30:00', '21:30:00', '01-4720123', 'Dedicated multi-story and ground customer parking with trolley bays and attendant assistance.', 'bhatbhateni_parking.jpg', 'active', '2026-09-01', NOW()),
(14, 'TUTH Hospital Visitor Parking', 'Teaching Hospital Gate, Maharajgunj', 'Maharajgunj', 27.73480000, 85.33010000, 'paid', 'Rs. 20/hr (Bike), Rs. 40/hr (Car)', 'both', '00:00:00', '23:59:59', '01-4412303', 'Round-the-clock hospital visitor parking lot with paved sections for emergency vehicles, staff, and patient visitors.', NULL, 'active', '2026-08-25', NOW()),
(15, 'Basantapur Public Scooter Stand', 'Ganga Path, Basantapur', 'Kathmandu Durbar Square', 27.70320000, 85.30890000, 'paid', 'Rs. 20/hr', 'motorcycle', '07:00:00', '21:00:00', NULL, 'Open air motorcycle and scooter parking space adjacent to ancient Durbar Square heritage perimeter.', NULL, 'active', '2026-09-04', NOW())
ON DUPLICATE KEY UPDATE name=VALUES(name);
