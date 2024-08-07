<?php
require_once 'backend/sdbh.php';
$dbh = new sdbh();

function getError($message, $errorCode = 400) {
    echo json_encode(['error' => $message]);
    return http_response_code($errorCode);
}

function calculatePrice(sdbh $dbh, $productRow, $days, $services) {
    // Получаем информацию о продукте
    $product = $productRow[0];

    if (!$product)
        return getError('Product not found');

    $tariff = $product['TARIFF'] ? unserialize($product['TARIFF']) : null;

    $price = $product['PRICE'];
    if ($tariff) {
        foreach ($tariff as $period => $tariffPrice) {
            if ($days >= $period) {
                $price = $tariffPrice;
            }
        }
    }

    // Рассчитываем итоговую стоимость
    $totalPrice = $price * $days;

    foreach ($services as $servicePrice) {
        $totalPrice += $servicePrice * $days;
    }

    return json_encode(['total' => $totalPrice]);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Проверяем, что переданы обязательные параметры
    if (!isset($_POST['product'])) {
        return getError('Product not set');
    }

    if (!isset($_POST['days'])) {
        return getError('Days not set');
    }

    // Получаем параметры из POST-запроса
    $productId = $_POST['product'];
    $days = $_POST['days'];
    $services = isset($_POST['services']) ? $_POST['services'] : [];

    $productRow = $dbh->mselect_rows('a25_products', ['ID' => $productId], 0, 1, 'ID');
    if ($productRow)
        echo calculatePrice($dbh, $productRow, $days, $services);
    else
        return getError('Product not found');
    
} else
    return getError('Invalid request method', 405);
