<?php
class FoodOrder {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Добавление нового заказа
    public function add($customer_name, $dish_count, $restaurant, $online_payment, $packaging_type) {
        // Рассчитываем общую стоимость (условно: 500 руб за блюдо)
        $total_price = $dish_count * 500;
        
        $stmt = $this->pdo->prepare(
            "INSERT INTO food_orders (customer_name, dish_count, restaurant, online_payment, packaging_type, total_price) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$customer_name, $dish_count, $restaurant, $online_payment, $packaging_type, $total_price]);
        return $this->pdo->lastInsertId();
    }

    // Получение всех заказов
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM food_orders ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    // Получение заказа по ID
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM food_orders WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Получение статистики
    public function getStats() {
        $stats = [];
        
        try {
            // Общее количество заказов
            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM food_orders");
            $stats['total'] = $stmt->fetch()['total'] ?? 0;
            
            // Общее количество блюд
            $stmt = $this->pdo->query("SELECT SUM(dish_count) as total_dishes FROM food_orders");
            $stats['total_dishes'] = $stmt->fetch()['total_dishes'] ?? 0;
            
            // Общая выручка
            $stmt = $this->pdo->query("SELECT SUM(total_price) as total_revenue FROM food_orders");
            $stats['total_revenue'] = $stmt->fetch()['total_revenue'] ?? 0;
            
            // Количество онлайн-оплат
            $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM food_orders WHERE online_payment = 1");
            $stats['online_payments'] = $stmt->fetch()['count'] ?? 0;
            
            // Количество заказов по ресторанам
            $stmt = $this->pdo->query("SELECT restaurant, COUNT(*) as count FROM food_orders GROUP BY restaurant");
            $stats['by_restaurant'] = $stmt->fetchAll();
            
            // Количество заказов по типам упаковки
            $stmt = $this->pdo->query("SELECT packaging_type, COUNT(*) as count FROM food_orders GROUP BY packaging_type");
            $stats['by_packaging'] = $stmt->fetchAll();
            
            // Количество уникальных ресторанов
            $stats['restaurants_count'] = count($stats['by_restaurant']);
            
            // Количество уникальных типов упаковки
            $stats['packaging_count'] = count($stats['by_packaging']);
            
        } catch (Exception $e) {
            // Если произошла ошибка, возвращаем значения по умолчанию
            $stats['total'] = 0;
            $stats['total_dishes'] = 0;
            $stats['total_revenue'] = 0;
            $stats['online_payments'] = 0;
            $stats['by_restaurant'] = [];
            $stats['by_packaging'] = [];
            $stats['restaurants_count'] = 0;
            $stats['packaging_count'] = 0;
        }
        
        return $stats;
    }

    // Удаление заказа
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM food_orders WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->rowCount();
    }
}
?>