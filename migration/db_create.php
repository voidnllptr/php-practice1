<?php
include_once __DIR__ . '/../config/database.php';

$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    die("Не удалось подключиться к БД\n");
}

$sql = [
    "CREATE DATABASE IF NOT EXISTS comfort_otdyh",
    "USE comfort_otdyh",
    
    "CREATE TABLE IF NOT EXISTS countries (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL UNIQUE,
        code VARCHAR(10) NOT NULL UNIQUE,
        visa_required BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    "CREATE TABLE IF NOT EXISTS clients (
        id INT PRIMARY KEY AUTO_INCREMENT,
        full_name VARCHAR(200) NOT NULL,
        passport_number VARCHAR(50) UNIQUE NOT NULL,
        phone VARCHAR(20) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        birth_date DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    "CREATE TABLE IF NOT EXISTS tours (
        id INT PRIMARY KEY AUTO_INCREMENT,
        country_id INT NOT NULL,
        name VARCHAR(200) NOT NULL,
        description TEXT,
        start_date DATE NOT NULL,
        end_date DATE NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        max_people INT NOT NULL,
        available_spots INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (country_id) REFERENCES countries(id) ON DELETE CASCADE
    )",

    "CREATE TABLE IF NOT EXISTS bookings (
        id INT PRIMARY KEY AUTO_INCREMENT,
        client_id INT NOT NULL,
        tour_id INT NOT NULL,
        booking_date DATE NOT NULL,
        status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
        total_price DECIMAL(10, 2) NOT NULL,
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
        FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE,
        UNIQUE KEY unique_booking (client_id, tour_id, booking_date)
    )"
];

try {
    foreach ($sql as $query) {
        $conn->exec($query);
    }
    echo "Таблицы созданы успешно\n";
} catch (PDOException $e) {
    echo "Ошибка создания таблиц: " . $e->getMessage() . "\n";
}
?>