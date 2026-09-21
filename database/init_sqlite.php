<?php
$dbFile = __DIR__ . '/parking_locator.sqlite';
$pdo = new PDO('sqlite:' . $dbFile);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdo->exec("CREATE TABLE IF NOT EXISTS admins (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS parking_facilities (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    address TEXT NOT NULL,
    area TEXT NOT NULL,
    latitude REAL NOT NULL,
    longitude REAL NOT NULL,
    payment_type TEXT NOT NULL DEFAULT 'paid',
    parking_fee TEXT DEFAULT NULL,
    vehicle_type TEXT NOT NULL DEFAULT 'both',
    total_capacity INTEGER NOT NULL DEFAULT 50,
    available_spaces INTEGER NOT NULL DEFAULT 20,
    capacity_bike INTEGER NOT NULL DEFAULT 30,
    available_bike INTEGER NOT NULL DEFAULT 12,
    capacity_car INTEGER NOT NULL DEFAULT 20,
    available_car INTEGER NOT NULL DEFAULT 8,
    opening_time TEXT DEFAULT '07:00:00',
    closing_time TEXT DEFAULT '21:00:00',
    contact TEXT DEFAULT NULL,
    description TEXT DEFAULT NULL,
    image TEXT DEFAULT NULL,
    status TEXT NOT NULL DEFAULT 'active',
    last_verified TEXT NOT NULL,
    last_spaces_update TEXT DEFAULT CURRENT_TIMESTAMP,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT DEFAULT CURRENT_TIMESTAMP
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS parking_reports (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    parking_id INTEGER NOT NULL,
    issue_type TEXT NOT NULL,
    message TEXT NOT NULL,
    reporter_contact TEXT DEFAULT NULL,
    status TEXT NOT NULL DEFAULT 'pending',
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parking_id) REFERENCES parking_facilities(id) ON DELETE CASCADE
)");

// Check if admins exist
$adminCheck = $pdo->query("SELECT COUNT(*) FROM admins")->fetchColumn();
if ($adminCheck == 0) {
    $stmt = $pdo->prepare("INSERT INTO admins (username, password, created_at) VALUES (?, ?, datetime('now'))");
    $stmt->execute(['admin', '$2y$10$FcYWiEB53kt68JdNVVs9S.bO//nIEhqh8VvSj6oHv9rYt6AQPElcS']);
}

// Check existing facilities or column migration
$columns = $pdo->query("PRAGMA table_info(parking_facilities)")->fetchAll(PDO::FETCH_ASSOC);
$columnNames = array_column($columns, 'name');

$neededCols = [
    'total_capacity' => 'INTEGER NOT NULL DEFAULT 50',
    'available_spaces' => 'INTEGER NOT NULL DEFAULT 20',
    'capacity_bike' => 'INTEGER NOT NULL DEFAULT 30',
    'available_bike' => 'INTEGER NOT NULL DEFAULT 12',
    'capacity_car' => 'INTEGER NOT NULL DEFAULT 20',
    'available_car' => 'INTEGER NOT NULL DEFAULT 8',
    'last_spaces_update' => 'TEXT DEFAULT NULL'
];

foreach ($neededCols as $col => $def) {
    if (!in_array($col, $columnNames)) {
        $pdo->exec("ALTER TABLE parking_facilities ADD COLUMN {$col} {$def}");
    }
}

$facilityCount = $pdo->query("SELECT COUNT(*) FROM parking_facilities")->fetchColumn();
if ($facilityCount == 0) {
    $facilities = [
        [1, 'Chhaya Center Underground Parking', 'Amrit Marg, Thamel', 'Thamel', 27.71490000, 85.31220000, 'paid', 'Rs. 50/hr (Car), Rs. 25/hr (Bike)', 'both', 120, 38, 80, 26, 40, 12, '06:00:00', '23:00:00', '01-4256789', 'Multi-level basement parking with security personnel, lighting, and CCTV monitoring. Convenient for visitors to central Thamel and Chhaya Center shopping complex.', 'chhaya_center.jpg', 'active', '2026-09-01'],
        [2, 'Thamel Marg Open Parking Area', 'Thamel Marg, Near Kathmandu Guest House', 'Thamel', 27.71610000, 85.31050000, 'paid', 'Rs. 25/hr (Bike)', 'motorcycle', 60, 14, 60, 14, 0, 0, '07:00:00', '22:00:00', '9841234567', 'Designated outdoor two-wheeler parking lot managed by local ward committee for tourist area visitors.', null, 'active', '2026-08-28'],
        [3, 'Dharahara Underground Smart Parking', 'Khichapokhari, New Road', 'New Road', 27.70060000, 85.31220000, 'paid', 'Rs. 40/hr (Car), Rs. 20/hr (Bike)', 'both', 250, 95, 180, 70, 70, 25, '06:00:00', '22:00:00', '01-4221144', 'Spacious modern multi-level underground parking facility constructed near reconstructed Dharahara with dedicated lanes for two-wheelers and four-wheelers.', 'dharahara_parking.jpg', 'active', '2026-09-04'],
        [4, 'Khichapokhari Municipal Bike Stand', 'Khichapokhari Marg', 'New Road', 27.70280000, 85.31170000, 'paid', 'Rs. 20/hr', 'motorcycle', 80, 9, 80, 9, 0, 0, '08:00:00', '20:30:00', null, 'Open municipal motorcycle stand located within walking distance of Bishal Bazar and New Road gate.', null, 'active', '2026-09-02'],
        [5, 'Baneshwor Complex Customer Parking', 'New Baneshwor Chowk', 'Baneshwor', 27.69180000, 85.34210000, 'free', 'Free for customers', 'both', 50, 18, 35, 14, 15, 4, '08:00:00', '21:00:00', '01-4498877', 'Ground level dedicated customer parking lot situated behind commercial offices and banks at Baneshwor junction.', null, 'active', '2026-08-20'],
        [6, 'Eyeplex Mall Basement Parking', 'Main Road, New Baneshwor', 'Baneshwor', 27.69020000, 85.33750000, 'paid', 'Rs. 50/hr (Car), Rs. 25/hr (Bike)', 'both', 90, 27, 60, 19, 30, 8, '08:30:00', '22:30:00', '01-4471234', 'Covered underground parking for moviegoers and commercial shoppers with paved ramps and guard assistance.', 'eyeplex_mall.jpg', 'active', '2026-09-05'],
        [7, 'Putalisadak Commercial Complex Parking', 'Putalisadak Main Road', 'Putalisadak', 27.70580000, 85.32160000, 'paid', 'Rs. 40/hr (Car), Rs. 20/hr (Bike)', 'both', 45, 6, 30, 4, 15, 2, '07:30:00', '20:00:00', '9851098765', 'Off-street parking lot serving educational consultancies, computer hardware stores, and offices along Putalisadak corridor.', null, 'active', '2026-08-15'],
        [8, 'City Center Multi-Story Parking', 'Kamalpokhari, Near Putalisadak', 'Putalisadak', 27.70880000, 85.32430000, 'paid', 'Rs. 50/hr (Car), Rs. 25/hr (Bike)', 'both', 150, 52, 100, 36, 50, 16, '09:00:00', '22:00:00', '01-4011555', 'Covered multi-level parking complex inside City Center shopping mall with elevator access and automated barrier gates.', 'city_center.jpg', 'active', '2026-09-03'],
        [9, 'Kalanki Underpass Roadside Parking Bay', 'Kalanki Chowk West', 'Kalanki', 27.69370000, 85.28180000, 'free', 'Free public bay', 'both', 30, 0, 20, 0, 10, 0, '06:00:00', '20:00:00', null, 'Designated public roadside parking bay designated by traffic police near Kalanki junction for short halts.', null, 'active', '2026-08-10'],
        [10, 'Makalu Complex Parking', 'Ring Road, Kalanki', 'Kalanki', 27.69520000, 85.28340000, 'paid', 'Rs. 30/hr (Car), Rs. 15/hr (Bike)', 'both', 40, 15, 25, 10, 15, 5, '07:00:00', '21:00:00', '9803123456', 'Paved commercial ground parking facility suitable for commuter vehicles and local shoppers.', null, 'active', '2026-08-30'],
        [11, 'Labim Mall Underground Parking', 'Pulchowk, Lalitpur', 'Patan', 27.67780000, 85.31680000, 'paid', 'Rs. 60/hr (Car), Rs. 30/hr (Bike)', 'both', 180, 64, 120, 45, 60, 19, '08:00:00', '23:00:00', '01-5536789', 'Modern double-level basement facility with digital ticket validation, well-marked bays, and direct elevator access to mall floors.', 'labim_mall.jpg', 'active', '2026-09-05'],
        [12, 'Mangal Bazar Heritage Parking Lot', 'Patan Durbar Square West', 'Patan', 27.67290000, 85.32450000, 'paid', 'Rs. 25/hr (Bike), Rs. 50/hr (Car)', 'both', 70, 11, 50, 8, 20, 3, '07:00:00', '20:00:00', '01-5522334', 'Designated municipal vehicle parking lot for visitors to Patan Durbar Square and surrounding handicraft markets.', null, 'active', '2026-09-02'],
        [13, 'Bhatbhateni Maharajgunj Parking', 'Narayan Gopal Chowk, Maharajgunj', 'Maharajgunj', 27.73620000, 85.33120000, 'free', 'Free with store purchase', 'both', 110, 42, 70, 28, 40, 14, '07:30:00', '21:30:00', '01-4720123', 'Dedicated multi-story and ground customer parking with trolley bays and attendant assistance.', 'bhatbhateni_parking.jpg', 'active', '2026-09-01'],
        [14, 'TUTH Hospital Visitor Parking', 'Teaching Hospital Gate, Maharajgunj', 'Maharajgunj', 27.73480000, 85.33010000, 'paid', 'Rs. 20/hr (Bike), Rs. 40/hr (Car)', 'both', 100, 16, 65, 11, 35, 5, '00:00:00', '23:59:59', '01-4412303', 'Round-the-clock hospital visitor parking lot with paved sections for emergency vehicles, staff, and patient visitors.', null, 'active', '2026-08-25'],
        [15, 'Basantapur Public Scooter Stand', 'Ganga Path, Basantapur', 'Kathmandu Durbar Square', 27.70320000, 85.30890000, 'paid', 'Rs. 20/hr', 'motorcycle', 85, 3, 85, 3, 0, 0, '07:00:00', '21:00:00', null, 'Open air motorcycle and scooter parking space adjacent to ancient Durbar Square heritage perimeter.', null, 'active', '2026-09-04']
    ];

    $stmt = $pdo->prepare("INSERT INTO parking_facilities (id, name, address, area, latitude, longitude, payment_type, parking_fee, vehicle_type, total_capacity, available_spaces, capacity_bike, available_bike, capacity_car, available_car, opening_time, closing_time, contact, description, image, status, last_verified, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, datetime('now'))");
    foreach ($facilities as $facility) {
        $stmt->execute($facility);
    }
} else {
    // If table already existed, update capacity numbers for seed facilities
    $updates = [
        1 => [120, 38, 80, 26, 40, 12],
        2 => [60, 14, 60, 14, 0, 0],
        3 => [250, 95, 180, 70, 70, 25],
        4 => [80, 9, 80, 9, 0, 0],
        5 => [50, 18, 35, 14, 15, 4],
        6 => [90, 27, 60, 19, 30, 8],
        7 => [45, 6, 30, 4, 15, 2],
        8 => [150, 52, 100, 36, 50, 16],
        9 => [30, 0, 20, 0, 10, 0],
        10 => [40, 15, 25, 10, 15, 5],
        11 => [180, 64, 120, 45, 60, 19],
        12 => [70, 11, 50, 8, 20, 3],
        13 => [110, 42, 70, 28, 40, 14],
        14 => [100, 16, 65, 11, 35, 5],
        15 => [85, 3, 85, 3, 0, 0]
    ];
    $stmt = $pdo->prepare("UPDATE parking_facilities SET total_capacity = ?, available_spaces = ?, capacity_bike = ?, available_bike = ?, capacity_car = ?, available_car = ? WHERE id = ?");
    foreach ($updates as $id => $vals) {
        $stmt->execute([$vals[0], $vals[1], $vals[2], $vals[3], $vals[4], $vals[5], $id]);
    }
}

echo "Database initialized successfully.\n";
