<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'გთხოვთ გაიაროთ ავტორიზაცია.']);
        exit;
    }

    if (empty($data['cart'])) {
        echo json_encode(['success' => false, 'message' => 'კალათა ცარიელია.']);
        exit;
    }

    // მონაცემთა ბაზის კავშირი
    include 'config.php'; 

    $userId = $_SESSION['user_id'];
    $cart = $data['cart'];
    $orderTotal = array_sum(array_column($cart, 'price'));

    // მომხმარებლის სახელის მიღება (first_name)
    $stmtUser = $conn->prepare("SELECT first_name FROM users WHERE id = ?");
    $stmtUser->bind_param("i", $userId);
    $stmtUser->execute();
    $resultUser = $stmtUser->get_result();
    $user = $resultUser->fetch_assoc();
    $firstName = $user['first_name'] ?? 'უცნობი მომხმარებელი';
    $stmtUser->close();

    // შეკვეთის დამატება
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_price, created_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("id", $userId, $orderTotal);

    if ($stmt->execute()) {
        $orderId = $stmt->insert_id;

        // შეკვეთის დეტალების შენახვა
        $stmtDetails = $conn->prepare("INSERT INTO order_items (order_id, product_name, price, quantity) VALUES (?, ?, ?, ?)");

        foreach ($cart as $item) {
            // Get the quantity from the cart data; default to 1 if not provided
            $quantity = isset($item['quantity']) ? (int)$item['quantity'] : 1;
            $stmtDetails->bind_param("isdi", $orderId, $item['name'], $item['price'], $quantity);
            $stmtDetails->execute();
        }

        $stmtDetails->close();

        // წარმატებული პასუხი
        echo json_encode([
            'success' => true,
            'message' => 'შეკვეთა წარმატებით განხორციელდა!',
            'orderId' => $orderId,
            'firstName' => $firstName
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'შეკვეთის შენახვა ვერ მოხერხდა.']);
    }

    $stmt->close();
    $conn->close();
}
?>
