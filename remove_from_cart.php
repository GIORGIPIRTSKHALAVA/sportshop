<?php
session_start();
header('Content-Type: application/json');


if (!isset($_SESSION['cart'])) {
    echo json_encode(['success' => false, 'message' => 'კალათა უკვე ცარიელია.']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['index']) && is_numeric($input['index'])) {
    $index = (int)$input['index'];
    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // ინდექსების გადაკეთება

        echo json_encode(['success' => true, 'message' => 'პროდუქტი წაშლილია.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'პროდუქტი ვერ მოიძებნა.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'არასწორი მოთხოვნა.']);
}
