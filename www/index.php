<?php
require 'db.php';
require 'FoodOrder.php';

$foodOrder = new FoodOrder($pdo);
$orders = $foodOrder->getAll();
$stats = $foodOrder->getStats();

session_start();
$success = $_SESSION['success'] ?? '';
$errors = $_SESSION['errors'] ?? [];

unset($_SESSION['success']);
unset($_SESSION['errors']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Система заказов еды</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #ff7e5f 0%, #feb47b 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
        }
        
        .nav {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .nav a {
            display: inline-block;
            background: #ff7e5f;
            color: white;
            padding: 10px 20px;
            margin: 0 10px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .nav a:hover {
            background: #ff6b4a;
            transform: translateY(-2px);
        }
        
        .stats-section {
            background: #fff5f2;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #ff7e5f;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        .stat-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #ff7e5f;
        }
        
        .stat-label {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        
        .orders-section {
            background: #fffaf0;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .orders-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        .orders-table th,
        .orders-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .orders-table th {
            background: #ff7e5f;
            color: white;
        }
        
        .orders-table tr:hover {
            background: #f5f5f5;
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #28a745;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #dc3545;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        .payment-yes {
            color: #28a745;
            font-weight: bold;
        }
        
        .payment-no {
            color: #dc3545;
        }
        
        .price {
            color: #ff7e5f;
            font-weight: bold;
        }
        
        .restaurant-badge {
            background: #e7f3ff;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🍕 Система заказов доставки еды</h1>
        
        <div class="nav">
            <a href="form.html">📝 Новый заказ</a>
            <a href="init.php" onclick="return confirm('Это служебная страница для инициализации БД. Перейти?')">🛠️ Инициализация БД</a>
            <a href="http://localhost:8083" target="_blank">📊 Adminer</a>
        </div>

        <!-- Сообщения об успехе/ошибках -->
        <?php if (!empty($success)): ?>
            <div class="success-message">
                ✅ <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="error-message">
                ❌ <strong>Ошибки:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Статистика -->
        <div class="stats-section">
            <h3>📊 Статистика заказов</h3>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['total']; ?></div>
                    <div class="stat-label">Всего заказов</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['total_dishes']; ?></div>
                    <div class="stat-label">Всего блюд</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo number_format($stats['total_revenue'], 0, '.', ' '); ?> ₽</div>
                    <div class="stat-label">Общая выручка</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['online_payments']; ?></div>
                    <div class="stat-label">Онлайн оплат</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['restaurants_count']; ?></div>
                    <div class="stat-label">Ресторанов</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['packaging_count']; ?></div>
                    <div class="stat-label">Типов упаковки</div>
                </div>
            </div>
        </div>

        <!-- Список заказов -->
        <div class="orders-section">
            <h3>📋 История заказов</h3>
            
            <?php if (empty($orders)): ?>
                <div class="empty-state">
                    <p>😔 Пока нет заказов</p>
                    <p><a href="form.html" style="color: #ff7e5f;">Сделайте первый заказ!</a></p>
                </div>
            <?php else: ?>
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Клиент</th>
                            <th>Блюда</th>
                            <th>Ресторан</th>
                            <th>Оплата</th>
                            <th>Упаковка</th>
                            <th>Стоимость</th>
                            <th>Дата заказа</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?php echo htmlspecialchars($order['id']); ?></td>
                                <td><strong><?php echo htmlspecialchars($order['customer_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($order['dish_count']); ?> шт.</td>
                                <td>
                                    <span class="restaurant-badge"><?php echo htmlspecialchars($order['restaurant']); ?></span>
                                </td>
                                <td class="<?php echo $order['online_payment'] ? 'payment-yes' : 'payment-no'; ?>">
                                    <?php echo $order['online_payment'] ? '✅ Онлайн' : '💵 Наличные'; ?>
                                </td>
                                <td><?php echo htmlspecialchars($order['packaging_type']); ?></td>
                                <td class="price"><?php echo number_format($order['total_price'], 0, '.', ' '); ?> ₽</td>
                                <td><?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Информация о БД -->
        <div style="text-align: center; margin-top: 20px; color: #666; font-size: 14px;">
            <p>💾 Данные хранятся в MySQL | 🐳 Работает на Docker | 🔗 <a href="http://localhost:8083" target="_blank" style="color: #ff7e5f;">Adminer: localhost:8083</a></p>
        </div>
    </div>
</body>
</html>