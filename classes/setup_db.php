<?php
require_once "Config.php";

try {
    // Connect without selecting DB to drop/create it
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    // Recreate database clean
    $pdo->exec("DROP DATABASE IF EXISTS " . DB_NAME);
    $pdo->exec("CREATE DATABASE " . DB_NAME);
    echo "Database '" . DB_NAME . "' created successfully.<br>";
    
    $pdo->exec("USE " . DB_NAME);
    
    // Create Admins table
    $adminsTable = "CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($adminsTable);
    echo "Admins table created successfully.<br>";

    // Create Users table (roles restricted to client, provider)
    $usersTable = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('client', 'provider') NOT NULL DEFAULT 'client',
        phone VARCHAR(20) NULL,
        profile_image VARCHAR(255) DEFAULT 'default_avatar.png',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($usersTable);
    echo "Users table created successfully.<br>";
    
    // Create Providers table
    $providersTable = "CREATE TABLE IF NOT EXISTS providers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        category VARCHAR(50) NOT NULL,
        bio TEXT NULL,
        price_per_hour DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
        experience_years INT DEFAULT 0,
        location VARCHAR(100) NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($providersTable);
    echo "Providers table created successfully.<br>";
    
    // Create Bookings table
    $bookingsTable = "CREATE TABLE IF NOT EXISTS bookings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        client_id INT NOT NULL,
        provider_id INT NOT NULL,
        booking_date DATE NOT NULL,
        booking_time TIME NOT NULL,
        status ENUM('pending', 'approved', 'cancelled') NOT NULL DEFAULT 'pending',
        notes TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (provider_id) REFERENCES providers(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($bookingsTable);
    echo "Bookings table created successfully.<br>";
    
    // Create Admin
    $adminPassword = password_hash("admin123", PASSWORD_DEFAULT);
    $pdo->exec("INSERT INTO admins (full_name, email, password) 
                VALUES ('System Admin', 'admin@connectpro.com', '$adminPassword')");
    
    // Create Client
    $clientPassword = password_hash("client123", PASSWORD_DEFAULT);
    $pdo->exec("INSERT INTO users (full_name, email, password, role, phone) 
                VALUES ('John Doe', 'john@gmail.com', '$clientPassword', 'client', '08011112222')");
    
    // Create Providers
    $providerPassword = password_hash("provider123", PASSWORD_DEFAULT);
    
    // Provider 1: Barber
    $pdo->exec("INSERT INTO users (full_name, email, password, role, phone) 
                VALUES ('Alex Barber', 'alex@barber.com', '$providerPassword', 'provider', '08033334444')");
    $u1 = $pdo->lastInsertId();
    $pdo->exec("INSERT INTO providers (user_id, category, bio, price_per_hour, experience_years, location) 
                VALUES ($u1, 'Barber', 'Professional male and female stylist with experience in modern haircut trends.', 25.00, 5, 'Lekki, Lagos')");
                
    // Provider 2: Photographer
    $pdo->exec("INSERT INTO users (full_name, email, password, role, phone) 
                VALUES ('Sarah Photos', 'sarah@photo.com', '$providerPassword', 'provider', '08055556666')");
    $u2 = $pdo->lastInsertId();
    $pdo->exec("INSERT INTO providers (user_id, category, bio, price_per_hour, experience_years, location) 
                VALUES ($u2, 'Photographer', 'Outdoor, wedding, and corporate event photographer with high-end camera equipment.', 50.00, 4, 'Ikeja, Lagos')");
                
    // Provider 3: Cleaner
    $pdo->exec("INSERT INTO users (full_name, email, password, role, phone) 
                VALUES ('Dave Clean', 'dave@clean.com', '$providerPassword', 'provider', '08077778888')");
    $u3 = $pdo->lastInsertId();
    $pdo->exec("INSERT INTO providers (user_id, category, bio, price_per_hour, experience_years, location) 
                VALUES ($u3, 'Cleaner', 'Detail-oriented deep house cleaning and commercial cleaning expert.', 15.00, 3, 'Surulere, Lagos')");

    echo "Seed data inserted successfully.<br>";
    echo "<strong>Setup completed successfully!</strong>";
} catch (PDOException $e) {
    echo "Error during database setup: " . $e->getMessage();
}
?>
