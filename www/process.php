<?php
ob_start();

require 'db.php';
require 'FoodOrder.php';

$foodOrder = new FoodOrder($pdo);

$customer_name = htmlspecialchars(trim($_POST['customer_name'] ?? ''));
$dish_count = intval($_POST['dish_count'] ?? 0);
$restaurant = htmlspecialchars(trim($_POST['restaurant'] ?? ''));
$online_payment = isset($_POST['online_payment']) ? 1 : 0;
$packaging_type = htmlspecialchars(trim($_POST['packaging_type'] ?? ''));

$errors = [];

if (empty($customer_name)) {
    $errors[] = "Имя обязательно для заполнения";
}

if ($dish_count < 1 || $dish_count > 10) {
    $errors[] = "Количество блюд должно быть от 1 до 10";
}

if (empty($restaurant)) {
    $errors[] = "Необходимо выбрать ресторан";
}

if (empty($packaging_type)) {
    $errors[] = "Необходимо выбрать тип упаковки";
}

if (!empty($errors)) {
    session_start();
    $_SESSION['errors'] = $errors;
    header("Location: form.html");
    exit();
}

try {
    $order_id = $foodOrder->add($customer_name, $dish_count, $restaurant, $online_payment, $packaging_type);
    
    session_start();
    $_SESSION['success'] = "Заказ успешно оформлен! Номер вашего заказа: #" . $order_id;
    
} catch (Exception $e) {
    session_start();
    $_SESSION['errors'] = ["Ошибка при сохранении заказа: " . $e->getMessage()];
}

ob_end_clean();
header("Location: index.php");
exit();
?>