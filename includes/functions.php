<?php
require_once dirname(__DIR__) . '/config/database.php';

function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
}

function getBaseUrl() {
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $dir = str_replace('\\', '/', dirname($scriptName));
    if ($dir === '/' || $dir === '.' || $dir === '') {
        return '';
    }
    // Strip trailing /admin or /api subdirectories if called from within them
    $dir = preg_replace('#/(admin|api)(/.*)?$#i', '', $dir);
    $normalized = '/' . trim($dir, '/');
    return $normalized === '/' ? '' : $normalized;
}

function formatTime($timeString) {
    if (empty($timeString)) {
        return 'Not specified';
    }
    $timestamp = strtotime($timeString);
    if (!$timestamp) {
        return $timeString;
    }
    return date('g:i A', $timestamp);
}

function formatDate($dateString) {
    if (empty($dateString)) {
        return 'Not specified';
    }
    $timestamp = strtotime($dateString);
    if (!$timestamp) {
        return $dateString;
    }
    return date('j F Y', $timestamp);
}

function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371000;
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat / 2) * sin($dLat / 2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLon / 2) * sin($dLon / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $meters = $earthRadius * $c;

    if ($meters < 1000) {
        $formatted = round($meters) . ' m away';
    } else {
        $km = round($meters / 1000, 1);
        $formatted = $km . ' km away';
    }

    return [
        'meters' => round($meters),
        'formatted' => $formatted
    ];
}

function getKathmanduLandmarks() {
    return [
        'thamel' => [
            'name' => 'Thamel',
            'latitude' => 27.7154,
            'longitude' => 85.3123
        ],
        'new road' => [
            'name' => 'New Road',
            'latitude' => 27.7025,
            'longitude' => 85.3120
        ],
        'baneshwor' => [
            'name' => 'New Baneshwor',
            'latitude' => 27.6915,
            'longitude' => 85.3420
        ],
        'putalisadak' => [
            'name' => 'Putalisadak',
            'latitude' => 27.7060,
            'longitude' => 85.3215
        ],
        'kalanki' => [
            'name' => 'Kalanki',
            'latitude' => 27.6935,
            'longitude' => 85.2815
        ],
        'patan' => [
            'name' => 'Patan Durbar Square',
            'latitude' => 27.6744,
            'longitude' => 85.3248
        ],
        'maharajgunj' => [
            'name' => 'Maharajgunj',
            'latitude' => 27.7350,
            'longitude' => 85.3310
        ],
        'kathmandu durbar square' => [
            'name' => 'Kathmandu Durbar Square',
            'latitude' => 27.7042,
            'longitude' => 85.3072
        ],
        'basantapur' => [
            'name' => 'Basantapur',
            'latitude' => 27.7035,
            'longitude' => 85.3085
        ],
        'pulchowk' => [
            'name' => 'Pulchowk',
            'latitude' => 27.6780,
            'longitude' => 85.3170
        ],
        'kamalpokhari' => [
            'name' => 'Kamalpokhari',
            'latitude' => 27.7090,
            'longitude' => 85.3245
        ],
        'durbarmarg' => [
            'name' => 'Durbar Marg',
            'latitude' => 27.7115,
            'longitude' => 85.3175
        ],
        'koteshwor' => [
            'name' => 'Koteshwor',
            'latitude' => 27.6775,
            'longitude' => 85.3485
        ],
        'chabahil' => [
            'name' => 'Chabahil',
            'latitude' => 27.7170,
            'longitude' => 85.3460
        ]
    ];
}

function resolveDestinationCoordinates($query) {
    $cleaned = strtolower(trim($query));
    $landmarks = getKathmanduLandmarks();

    foreach ($landmarks as $key => $data) {
        if (strpos($cleaned, $key) !== false || strpos($key, $cleaned) !== false) {
            return [
                'name' => $data['name'],
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude']
            ];
        }
    }

    $db = getDbConnection();
    $stmt = $db->prepare("SELECT name, area, latitude, longitude FROM parking_facilities WHERE status = 'active' AND (LOWER(area) LIKE :q OR LOWER(name) LIKE :q OR LOWER(address) LIKE :q) LIMIT 1");
    $stmt->execute([':q' => '%' . $cleaned . '%']);
    $facility = $stmt->fetch();

    if ($facility) {
        return [
            'name' => $facility['area'],
            'latitude' => (float)$facility['latitude'],
            'longitude' => (float)$facility['longitude']
        ];
    }

    return null;
}

function getParkingById($id) {
    $db = getDbConnection();
    $stmt = $db->prepare("SELECT * FROM parking_facilities WHERE id = ?");
    $stmt->execute([(int)$id]);
    return $stmt->fetch();
}

function getActiveParking($filters = []) {
    $db = getDbConnection();
    $sql = "SELECT * FROM parking_facilities WHERE status = 'active'";
    $params = [];

    if (!empty($filters['payment_type']) && $filters['payment_type'] !== 'all') {
        $sql .= " AND payment_type = :payment";
        $params[':payment'] = $filters['payment_type'];
    }

    if (!empty($filters['vehicle_type']) && $filters['vehicle_type'] !== 'all') {
        if ($filters['vehicle_type'] === 'car') {
            $sql .= " AND (vehicle_type = 'car' OR vehicle_type = 'both')";
        } elseif ($filters['vehicle_type'] === 'motorcycle') {
            $sql .= " AND (vehicle_type = 'motorcycle' OR vehicle_type = 'both')";
        } elseif ($filters['vehicle_type'] === 'both') {
            $sql .= " AND vehicle_type = 'both'";
        }
    }

    if (!empty($filters['area'])) {
        $sql .= " AND area = :area";
        $params[':area'] = $filters['area'];
    }

    if (!empty($filters['search'])) {
        $sql .= " AND (name LIKE :s OR address LIKE :s OR area LIKE :s)";
        $params[':s'] = '%' . $filters['search'] . '%';
    }

    $sort = $filters['sort'] ?? 'name_asc';
    if ($sort === 'fee_asc') {
        $sql .= " ORDER BY payment_type ASC, name ASC";
    } elseif ($sort === 'fee_desc') {
        $sql .= " ORDER BY payment_type DESC, name ASC";
    } elseif ($sort === 'latest') {
        $sql .= " ORDER BY last_verified DESC, id DESC";
    } else {
        $sql .= " ORDER BY area ASC, name ASC";
    }

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getAllActiveParking($filters = []) {
    return getActiveParking($filters);
}

function getNearbyParking($destLat, $destLon, $filters = []) {
    $facilities = getActiveParking($filters);
    $results = [];

    foreach ($facilities as $facility) {
        $distanceInfo = calculateDistance(
            $destLat,
            $destLon,
            (float)$facility['latitude'],
            (float)$facility['longitude']
        );

        $facility['distance_meters'] = $distanceInfo['meters'];
        $facility['distance_formatted'] = $distanceInfo['formatted'];
        $results[] = $facility;
    }

    $sort = $filters['sort'] ?? 'nearest';
    if ($sort === 'farthest') {
        usort($results, function($a, $b) {
            return $b['distance_meters'] <=> $a['distance_meters'];
        });
    } elseif ($sort === 'fee_asc') {
        usort($results, function($a, $b) {
            if ($a['payment_type'] === 'free' && $b['payment_type'] !== 'free') return -1;
            if ($a['payment_type'] !== 'free' && $b['payment_type'] === 'free') return 1;
            return $a['distance_meters'] <=> $b['distance_meters'];
        });
    } elseif ($sort === 'fee_desc') {
        usort($results, function($a, $b) {
            if ($a['payment_type'] === 'paid' && $b['payment_type'] !== 'paid') return -1;
            if ($a['payment_type'] !== 'paid' && $b['payment_type'] === 'paid') return 1;
            return $a['distance_meters'] <=> $b['distance_meters'];
        });
    } else {
        usort($results, function($a, $b) {
            return $a['distance_meters'] <=> $b['distance_meters'];
        });
    }

    return $results;
}

function getParkingAreas() {
    $db = getDbConnection();
    $sql = "SELECT area, COUNT(*) as total_facilities, 
            SUM(CASE WHEN payment_type = 'free' THEN 1 ELSE 0 END) as free_count,
            SUM(CASE WHEN payment_type = 'paid' THEN 1 ELSE 0 END) as paid_count
            FROM parking_facilities 
            WHERE status = 'active' 
            GROUP BY area 
            ORDER BY area ASC";
    $stmt = $db->query($sql);
    return $stmt->fetchAll();
}

function getAllParkingAdmin($search = '', $area = '', $status = '') {
    $db = getDbConnection();
    $sql = "SELECT * FROM parking_facilities WHERE 1=1";
    $params = [];

    if (!empty($search)) {
        $sql .= " AND (name LIKE :s OR address LIKE :s OR area LIKE :s)";
        $params[':s'] = '%' . $search . '%';
    }

    if (!empty($area)) {
        $sql .= " AND area = :area";
        $params[':area'] = $area;
    }

    if (!empty($status)) {
        $sql .= " AND status = :status";
        $params[':status'] = $status;
    }

    $sql .= " ORDER BY id DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getAdminStatistics() {
    $db = getDbConnection();

    $total = (int)$db->query("SELECT COUNT(*) FROM parking_facilities")->fetchColumn();
    $free = (int)$db->query("SELECT COUNT(*) FROM parking_facilities WHERE payment_type = 'free'")->fetchColumn();
    $paid = (int)$db->query("SELECT COUNT(*) FROM parking_facilities WHERE payment_type = 'paid'")->fetchColumn();
    $active = (int)$db->query("SELECT COUNT(*) FROM parking_facilities WHERE status = 'active'")->fetchColumn();
    $reports = (int)$db->query("SELECT COUNT(*) FROM parking_reports WHERE status = 'pending'")->fetchColumn();

    return [
        'total' => $total,
        'free' => $free,
        'paid' => $paid,
        'active' => $active,
        'pending_reports' => $reports
    ];
}

function createParking($data) {
    $db = getDbConnection();
    $sql = "INSERT INTO parking_facilities 
            (name, address, area, latitude, longitude, payment_type, parking_fee, vehicle_type, total_capacity, available_spaces, capacity_bike, available_bike, capacity_car, available_car, opening_time, closing_time, contact, description, image, status, last_verified, created_at) 
            VALUES (:name, :address, :area, :latitude, :longitude, :payment_type, :parking_fee, :vehicle_type, :total_capacity, :available_spaces, :capacity_bike, :available_bike, :capacity_car, :available_car, :opening_time, :closing_time, :contact, :description, :image, :status, :last_verified, datetime('now'))";

    $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
    if ($driver === 'mysql') {
        $sql = str_replace("datetime('now')", "NOW()", $sql);
    }

    $capBike = isset($data['capacity_bike']) ? (int)$data['capacity_bike'] : 30;
    $availBike = isset($data['available_bike']) ? (int)$data['available_bike'] : min(12, $capBike);
    $capCar = isset($data['capacity_car']) ? (int)$data['capacity_car'] : 20;
    $availCar = isset($data['available_car']) ? (int)$data['available_car'] : min(8, $capCar);
    $totalCap = isset($data['total_capacity']) ? (int)$data['total_capacity'] : ($capBike + $capCar);
    $availSpaces = isset($data['available_spaces']) ? (int)$data['available_spaces'] : ($availBike + $availCar);

    $stmt = $db->prepare($sql);
    return $stmt->execute([
        ':name' => $data['name'],
        ':address' => $data['address'],
        ':area' => $data['area'],
        ':latitude' => $data['latitude'],
        ':longitude' => $data['longitude'],
        ':payment_type' => $data['payment_type'],
        ':parking_fee' => $data['parking_fee'] ?? null,
        ':vehicle_type' => $data['vehicle_type'],
        ':total_capacity' => $totalCap,
        ':available_spaces' => $availSpaces,
        ':capacity_bike' => $capBike,
        ':available_bike' => $availBike,
        ':capacity_car' => $capCar,
        ':available_car' => $availCar,
        ':opening_time' => $data['opening_time'] ?? '07:00:00',
        ':closing_time' => $data['closing_time'] ?? '21:00:00',
        ':contact' => $data['contact'] ?? null,
        ':description' => $data['description'] ?? null,
        ':image' => $data['image'] ?? null,
        ':status' => $data['status'] ?? 'active',
        ':last_verified' => $data['last_verified']
    ]);
}

function updateParking($id, $data) {
    $db = getDbConnection();
    $sql = "UPDATE parking_facilities SET 
            name = :name,
            address = :address,
            area = :area,
            latitude = :latitude,
            longitude = :longitude,
            payment_type = :payment_type,
            parking_fee = :parking_fee,
            vehicle_type = :vehicle_type,
            total_capacity = :total_capacity,
            available_spaces = :available_spaces,
            capacity_bike = :capacity_bike,
            available_bike = :available_bike,
            capacity_car = :capacity_car,
            available_car = :available_car,
            opening_time = :opening_time,
            closing_time = :closing_time,
            contact = :contact,
            description = :description,
            image = :image,
            status = :status,
            last_verified = :last_verified";

    $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
    if ($driver === 'mysql') {
        $sql .= ", updated_at = NOW() WHERE id = :id";
    } else {
        $sql .= ", updated_at = datetime('now') WHERE id = :id";
    }

    $capBike = isset($data['capacity_bike']) ? (int)$data['capacity_bike'] : 30;
    $availBike = isset($data['available_bike']) ? (int)$data['available_bike'] : 12;
    $capCar = isset($data['capacity_car']) ? (int)$data['capacity_car'] : 20;
    $availCar = isset($data['available_car']) ? (int)$data['available_car'] : 8;
    $totalCap = isset($data['total_capacity']) ? (int)$data['total_capacity'] : ($capBike + $capCar);
    $availSpaces = isset($data['available_spaces']) ? (int)$data['available_spaces'] : ($availBike + $availCar);

    $stmt = $db->prepare($sql);
    return $stmt->execute([
        ':id' => (int)$id,
        ':name' => $data['name'],
        ':address' => $data['address'],
        ':area' => $data['area'],
        ':latitude' => $data['latitude'],
        ':longitude' => $data['longitude'],
        ':payment_type' => $data['payment_type'],
        ':parking_fee' => $data['parking_fee'] ?? null,
        ':vehicle_type' => $data['vehicle_type'],
        ':total_capacity' => $totalCap,
        ':available_spaces' => $availSpaces,
        ':capacity_bike' => $capBike,
        ':available_bike' => $availBike,
        ':capacity_car' => $capCar,
        ':available_car' => $availCar,
        ':opening_time' => $data['opening_time'],
        ':closing_time' => $data['closing_time'],
        ':contact' => $data['contact'] ?? null,
        ':description' => $data['description'] ?? null,
        ':image' => $data['image'] ?? null,
        ':status' => $data['status'],
        ':last_verified' => $data['last_verified']
    ]);
}

function deleteParking($id) {
    $db = getDbConnection();
    $existing = getParkingById($id);
    if ($existing && !empty($existing['image'])) {
        $imagePath = dirname(__DIR__) . '/uploads/parking/' . $existing['image'];
        if (file_exists($imagePath)) {
            @unlink($imagePath);
        }
    }
    $stmt = $db->prepare("DELETE FROM parking_facilities WHERE id = ?");
    return $stmt->execute([(int)$id]);
}

function createReport($parkingId, $issueType, $message, $contact = '') {
    $db = getDbConnection();
    $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
    $timeFunc = ($driver === 'mysql') ? 'NOW()' : "datetime('now')";
    $sql = "INSERT INTO parking_reports (parking_id, issue_type, message, reporter_contact, status, created_at) VALUES (?, ?, ?, ?, 'pending', {$timeFunc})";
    $stmt = $db->prepare($sql);
    return $stmt->execute([(int)$parkingId, $issueType, $message, $contact]);
}

function getAllReports($status = '') {
    $db = getDbConnection();
    $sql = "SELECT r.*, p.name as parking_name, p.area as parking_area 
            FROM parking_reports r 
            LEFT JOIN parking_facilities p ON r.parking_id = p.id";
    $params = [];
    if (!empty($status)) {
        $sql .= " WHERE r.status = ?";
        $params[] = $status;
    }
    $sql .= " ORDER BY r.id DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function updateReportStatus($reportId, $status) {
    $db = getDbConnection();
    $stmt = $db->prepare("UPDATE parking_reports SET status = ? WHERE id = ?");
    return $stmt->execute([$status, (int)$reportId]);
}

/**
 * Real-Time Parking Availability Functions
 * Academic BIM Project - Realtime Parking Tracker
 */

function formatRealtimeSpacesData($facility) {
    $totalCapacity = (int)($facility['total_capacity'] ?? 50);
    $availableSpaces = (int)($facility['available_spaces'] ?? 20);
    $capacityBike = (int)($facility['capacity_bike'] ?? 30);
    $availableBike = (int)($facility['available_bike'] ?? 12);
    $capacityCar = (int)($facility['capacity_car'] ?? 20);
    $availableCar = (int)($facility['available_car'] ?? 8);

    // Ensure non-negative bounds
    $availableSpaces = max(0, min($totalCapacity, $availableSpaces));
    $availableBike = max(0, min($capacityBike, $availableBike));
    $availableCar = max(0, min($capacityCar, $availableCar));

    $occupied = max(0, $totalCapacity - $availableSpaces);
    $occupancyPercent = ($totalCapacity > 0) ? round(($occupied / $totalCapacity) * 100) : 0;

    // Determine status badge and label
    if ($availableSpaces <= 0) {
        $statusLevel = 'full';
        $statusText = 'FULL (0 Spots)';
        $badgeClass = 'bg-danger';
        $textColor = 'text-danger';
        $markerColor = '#dc3545'; // Red
    } elseif ($availableSpaces <= max(3, round($totalCapacity * 0.15))) {
        $statusLevel = 'limited';
        $statusText = 'Filling Fast (' . $availableSpaces . ' Left)';
        $badgeClass = 'bg-warning text-dark';
        $textColor = 'text-warning';
        $markerColor = '#fd7e14'; // Orange/Amber
    } else {
        $statusLevel = 'available';
        $statusText = 'Available (' . $availableSpaces . ' Free)';
        $badgeClass = 'bg-success';
        $textColor = 'text-success';
        $markerColor = '#198754'; // Green
    }

    return [
        'id' => (int)$facility['id'],
        'name' => $facility['name'],
        'area' => $facility['area'],
        'vehicle_type' => $facility['vehicle_type'],
        'total_capacity' => $totalCapacity,
        'available_spaces' => $availableSpaces,
        'occupied_spaces' => $occupied,
        'occupancy_percent' => $occupancyPercent,
        'capacity_bike' => $capacityBike,
        'available_bike' => $availableBike,
        'capacity_car' => $capacityCar,
        'available_car' => $availableCar,
        'status_level' => $statusLevel,
        'status_text' => $statusText,
        'badge_class' => $badgeClass,
        'text_color' => $textColor,
        'marker_color' => $markerColor,
        'last_updated' => $facility['last_spaces_update'] ?? date('Y-m-d H:i:s')
    ];
}

function adjustFacilitySpace($id, $vehicleType, $direction) {
    $facility = getParkingById($id);
    if (!$facility) {
        return ['success' => false, 'error' => 'Facility not found'];
    }

    $capBike = (int)($facility['capacity_bike'] ?? 30);
    $availBike = (int)($facility['available_bike'] ?? 12);
    $capCar = (int)($facility['capacity_car'] ?? 20);
    $availCar = (int)($facility['available_car'] ?? 8);

    if ($vehicleType === 'bike') {
        if ($direction === 'entry') {
            if ($availBike <= 0) return ['success' => false, 'error' => 'No bike spaces available'];
            $availBike--;
        } elseif ($direction === 'exit') {
            if ($availBike >= $capBike) return ['success' => false, 'error' => 'Bike spaces already at maximum capacity'];
            $availBike++;
        }
    } elseif ($vehicleType === 'car') {
        if ($direction === 'entry') {
            if ($availCar <= 0) return ['success' => false, 'error' => 'No car spaces available'];
            $availCar--;
        } elseif ($direction === 'exit') {
            if ($availCar >= $capCar) return ['success' => false, 'error' => 'Car spaces already at maximum capacity'];
            $availCar++;
        }
    } else {
        // General vehicle
        if ($direction === 'entry') {
            if ($availBike > 0 && ($availCar <= 0 || rand(0, 1) === 0)) {
                $availBike--;
            } elseif ($availCar > 0) {
                $availCar--;
            } else {
                return ['success' => false, 'error' => 'Parking facility is completely full'];
            }
        } else {
            if ($availCar < $capCar && ($availBike >= $capBike || rand(0, 1) === 0)) {
                $availCar++;
            } elseif ($availBike < $capBike) {
                $availBike++;
            }
        }
    }

    $totalAvail = $availBike + $availCar;

    $db = getDbConnection();
    $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
    $timeFunc = ($driver === 'mysql') ? 'NOW()' : "datetime('now')";

    $stmt = $db->prepare("UPDATE parking_facilities SET 
        available_spaces = ?,
        available_bike = ?,
        available_car = ?,
        last_spaces_update = {$timeFunc}
        WHERE id = ?");
    $stmt->execute([$totalAvail, $availBike, $availCar, (int)$id]);

    $updated = getParkingById($id);
    return [
        'success' => true,
        'facility' => formatRealtimeSpacesData($updated),
        'message' => ucfirst($vehicleType) . ' ' . ($direction === 'entry' ? 'entry recorded (-1 spot)' : 'exit recorded (+1 spot)')
    ];
}

function setFacilitySpaces($id, $availableBike, $availableCar) {
    $facility = getParkingById($id);
    if (!$facility) {
        return ['success' => false, 'error' => 'Facility not found'];
    }

    $capBike = (int)($facility['capacity_bike'] ?? 30);
    $capCar = (int)($facility['capacity_car'] ?? 20);

    $availBike = max(0, min($capBike, (int)$availableBike));
    $availCar = max(0, min($capCar, (int)$availableCar));
    $totalAvail = $availBike + $availCar;

    $db = getDbConnection();
    $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
    $timeFunc = ($driver === 'mysql') ? 'NOW()' : "datetime('now')";

    $stmt = $db->prepare("UPDATE parking_facilities SET 
        available_spaces = ?,
        available_bike = ?,
        available_car = ?,
        last_spaces_update = {$timeFunc}
        WHERE id = ?");
    $stmt->execute([$totalAvail, $availBike, $availCar, (int)$id]);

    $updated = getParkingById($id);
    return [
        'success' => true,
        'facility' => formatRealtimeSpacesData($updated),
        'message' => 'Spaces updated successfully'
    ];
}

function getAllRealtimeFacilities() {
    $facilities = getActiveParking();
    $data = [];
    foreach ($facilities as $facility) {
        $data[] = formatRealtimeSpacesData($facility);
    }
    return $data;
}

function simulateRandomTraffic() {
    $facilities = getActiveParking();
    if (empty($facilities)) {
        return ['success' => false, 'error' => 'No parking facilities available'];
    }

    // Pick a random facility
    $randomFacility = $facilities[array_rand($facilities)];
    $id = $randomFacility['id'];
    $vehicleType = ($randomFacility['vehicle_type'] === 'motorcycle') ? 'bike' : (($randomFacility['vehicle_type'] === 'car') ? 'car' : (rand(0, 1) ? 'bike' : 'car'));
    
    // Choose entry or exit based on current availability
    $avail = (int)($randomFacility['available_spaces'] ?? 20);
    $total = (int)($randomFacility['total_capacity'] ?? 50);

    if ($avail <= 2) {
        $direction = 'exit';
    } elseif ($avail >= $total - 2) {
        $direction = 'entry';
    } else {
        $direction = rand(0, 1) ? 'entry' : 'exit';
    }

    $result = adjustFacilitySpace($id, $vehicleType, $direction);
    if ($result['success']) {
        $result['log'] = date('H:i:s') . " - " . $randomFacility['name'] . " (" . $randomFacility['area'] . "): 1 " . ucfirst($vehicleType) . " " . ($direction === 'entry' ? 'ENTERED' : 'EXITED') . " (Available: " . $result['facility']['available_spaces'] . "/" . $result['facility']['total_capacity'] . ")";
    }
    return $result;
}

