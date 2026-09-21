# Parking Information and Locator System

### A Web-Based System for Finding Parking Facilities Near Selected Destinations in Kathmandu
Bachelor of Information Management (BIM) Academic Project

---

## 1. Project Overview

The **Parking Information and Locator System** is a responsive web-based geographic information application developed to address the acute parking accessibility challenges in Kathmandu, Nepal. Rather than browsing generic or unrelated lists of parking spaces, the system is designed around a destination-first workflow:

> **"I know where I am going. Help me find where I can park near there."**

The platform allows motorists, commuters, and visitors to select or search their destination in Kathmandu Valley (such as New Road, Thamel, Baneshwor, Putalisadak, Kalanki, Patan, Maharajgunj), calculates actual walking/driving distance using geographic coordinates via the Haversine formula, displays active parking facilities on an interactive OpenStreetMap via Leaflet.js, and provides essential verified details such as parking tariffs, vehicle type restrictions (cars/motorcycles), operating hours, and external turn-by-turn navigation directions.

---

## 2. Problem Statement

Kathmandu experiences dense vehicular congestion across major commercial hubs, shopping markets, corporate corridors, and historic heritage sites. Motorists traveling to destinations such as New Road, Indrachowk, Thamel, or Putalisadak often know their target destination with precision, yet lack reliable information regarding:

* Where authorized on-street or off-street parking facilities are located nearby.
* The approximate walking distance from the parking spot to their final destination.
* Whether parking is free or subject to municipal/private hourly fees.
* Which vehicle categories (two-wheelers, four-wheelers, or both) are permitted.
* The facility opening and closing schedules.
* Directions to reach the entrance safely.

Consequently, drivers waste substantial time circling crowded avenues, burning excess fuel, obstructing road lanes, and inquiring from local pedestrians. This project resolves this information gap by providing a reliable, centralized, and destination-centric parking discovery system.

---

## 3. Technologies Used

### Frontend
* **HTML5** (Semantic layout and markup)
* **CSS3** (Responsive design and clean custom utility styling)
* **JavaScript** (ES6+, asynchronous Fetch API, dynamic client-side filtering)
* **Bootstrap 5** (Responsive layout grid, modern card components, form controls)
* **Bootstrap Icons** (Standard iconography)

### Backend
* **PHP 8** (Server-side routing, request processing, session management, and templating)
* **PDO (PHP Data Objects)** (Secure database interaction with prepared statements)

### Database
* **MySQL 5.7+ / MariaDB 10.4+** (Relational relational schema with UTF-8 support)
* **SQLite3** (Lightweight runtime database fallback for local prototyping)

### Mapping & Geolocation
* **Leaflet.js 1.9** (Lightweight, mobile-friendly interactive mapping engine)
* **OpenStreetMap (OSM)** (Open-source map tiles)
* **Haversine Formula** (Mathematical spherical distance calculation)

### Development & Hosting Environment
* **XAMPP** (Apache, MySQL, PHP local distribution)
* **Visual Studio Code**

---

## 4. System Requirements

### Hardware Requirements
* Processor: Dual-core 1.8 GHz or higher
* RAM: 4 GB minimum (8 GB recommended)
* Storage: 200 MB free hard drive space

### Software Requirements
* Operating System: Windows 10/11, macOS 10.15+, or Ubuntu/Debian Linux
* Web Browser: Google Chrome, Mozilla Firefox, Microsoft Edge, Safari
* Server Stack: XAMPP for Windows/macOS/Linux (Apache 2.4+, PHP 8.0+, MySQL 5.7+ / MariaDB 10.4+)

---

## 5. XAMPP Installation

1. Download the latest installer for **XAMPP with PHP 8.x** from the official Apache Friends website:
   https://www.apachefriends.org/download.html
2. Run the installer and select the following core components:
   * Apache
   * MySQL
   * phpMyAdmin
   * PHP
3. Complete the setup wizard and allow the required firewall permissions for Apache and MySQL.

---

## 6. Project Setup

1. Locate your local XAMPP `htdocs` directory:
   * **Windows:** `C:\xampp\htdocs\`
   * **macOS:** `/Applications/XAMPP/xamppfiles/htdocs/`
   * **Linux:** `/opt/lampp/htdocs/`
2. Place the project folder into `htdocs` so the directory structure is:
   ```text
   C:\xampp\htdocs\parking-system\
   ```
3. Ensure directory write permissions are enabled for the `uploads/` folder if saving uploaded photos.

---

## 7. Database Creation

1. Launch the **XAMPP Control Panel**.
2. Start both the **Apache** and **MySQL** services.
3. Open your web browser and navigate to:
   ```text
   http://localhost/phpmyadmin/
   ```
4. Click **New** or select the **Databases** tab.
5. In the **Database name** field, enter:
   ```text
   parking_locator
   ```
6. Set the collation to `utf8mb4_unicode_ci` and click **Create**.

---

## 8. SQL Import

1. In phpMyAdmin, click on the newly created `parking_locator` database in the left sidebar.
2. Click on the **Import** tab in the top navigation bar.
3. Under **File to import**, click **Choose File** (or Browse) and select:
   ```text
   C:\xampp\htdocs\parking-system\database\parking_locator.sql
   ```
4. Leave the default format as **SQL** and click the **Import** (or **Go**) button at the bottom of the page.
5. Verify that the three required tables have been created:
   * `admins`
   * `parking_facilities`
   * `parking_reports`
   And that the 15 verified initial sample Kathmandu facilities are populated.

---

## 9. Configuration

The database connection parameters are centralized in `config/database.php`:

```php
$dbHost = '127.0.0.1';
$dbName = 'parking_locator';
$dbUser = 'root';
$dbPass = '';
```

* For standard XAMPP installations, the default MySQL user is `root` with an empty password `""`.
* If your local MySQL instance has a custom password or uses a non-standard port (such as 3307), update `config/database.php` accordingly.

---

## 10. How to Run the Application

1. Verify Apache and MySQL are running in the XAMPP Control Panel.
2. Open any web browser and go to:
   ```text
   http://localhost/parking-system/
   ```
3. Test the core user workflow:
   * Enter a destination (e.g. **New Road**, **Thamel**, or **Baneshwor**) into the search bar.
   * Click **Find Parking**.
   * View the interactive Leaflet map centering your destination and plotting nearby parking pins.
   * Review cards sorted by distance (showing meters or kilometers).
   * Apply filters for vehicle type (Car, Motorcycle, Both) and payment status (Free, Paid).
   * Click **View Details** to read opening hours, rates, and verified information.
   * Click **Directions** to launch GPS coordinates in Google Maps/OpenStreetMap.

---

## 11. Admin Login Setup

The administrative dashboard allows authorized staff to manage parking facilities and review user feedback reports.

1. Navigate to:
   ```text
   http://localhost/parking-system/admin/login.php
   ```
2. Enter your administrator credentials.
3. Click **Sign In**.
4. The administrator can:
   * View live statistics (total facilities, active locations, pending reports).
   * Register new parking facilities with geographic coordinates and operating hours.
   * Edit tariffs, vehicle restrictions, and update the "Last Verified" date.
   * Safely delete obsolete facilities with a confirmation prompt.
   * Review crowdsourced citizen discrepancy reports and change status (*Pending*, *Reviewed*, *Resolved*).

---

## 12. Project Structure

```text
parking-system/
│
├── index.php                 # Homepage with destination search & quick chips
├── find-parking.php          # Main locator with filters and interactive Leaflet map
├── parking-details.php       # Facility profile, specifications, and navigation
├── areas.php                 # Kathmandu study areas overview
├── about.php                 # Academic BIM project background and details
├── report.php                # Discrepancy reporting form
│
├── config/
│   └── database.php          # Database connection file (XAMPP MySQL)
│
├── includes/
│   ├── header.php            # HTML header and top navigation bar
│   ├── footer.php            # Footer and script includes
│   ├── auth.php              # Simple admin session check
│   └── functions.php         # Haversine distance formula and helper queries
│
├── assets/
│   ├── css/
│   │   └── style.css         # Clean custom styles
│   └── js/
│       └── main.js           # Single unified JavaScript file for maps and UI
│
├── admin/
│   ├── login.php             # Admin login page
│   ├── index.php             # Admin dashboard with summary cards
│   ├── parking.php           # Facilities management list
│   ├── add-parking.php       # Add new parking facility
│   ├── edit-parking.php      # Edit facility details
│   ├── delete-parking.php    # Delete parking facility
│   ├── reports.php           # Review user issue reports
│   ├── admin_header.php      # Admin panel header and navigation
│   ├── admin_footer.php      # Admin panel footer
│   └── logout.php            # Admin logout
│
├── database/
│   ├── parking_locator.sql   # Complete MySQL database export (for phpMyAdmin)
│   └── parking_locator.sqlite# Local preview SQLite database
│
└── uploads/
    └── parking/              # Uploaded facility photos
```

---

## 13. System Limitations

1. **No Live Space Availability:** This system is an informational locator and does NOT guarantee vacant parking bays at the time of arrival. Displaying fake real-time bay occupancy counts without citywide automated sensors would be misleading.
2. **Periodic Verification Dependency:** Facility operational schedules and tariffs are subject to changes by local ward authorities or private owners. The system prominently displays the "Last Verified" date on each record.
3. **Crowdsourced Validation:** The platform includes an integrated reporting form allowing motorists to report discrepancies (such as revised tariffs or closures) for administrative auditing.
