<?php
session_start();
header('Content-Type: application/json');

// სესიის კალათის გასუფთავება
if (isset($_SESSION['cart'])) {
    unset($_SESSION['cart']);
    echo json_encode(['success' => true, 'message' => 'კალათა გასუფთავებულია.']);
} else {
    echo json_encode(['success' => false, 'message' => 'კალათა უკვე ცარიელია.']);
}
?>
