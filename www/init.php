<?php
require 'db.php';

try {
    // Создаем таблицу food_orders
    $sql = "CREATE TABLE IF NOT EXISTS food_orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_name VARCHAR(100) NOT NULL,
        dish_count INT NOT NULL,
        restaurant VARCHAR(100) NOT NULL,
        online_payment TINYINT(1) DEFAULT 0,
        packaging_type VARCHAR(50) NOT NULL,
        total_price DECIMAL(10,2),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    echo "✅ Таблица 'food_orders' успешно создана или уже существует";
    
} catch (\PDOException $e) {
    echo "❌ Ошибка при создании таблицы: " . $e->getMessage();
}
?>