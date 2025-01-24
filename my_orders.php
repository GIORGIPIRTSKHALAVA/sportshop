<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'config.php'; // Database connection

$userId = $_SESSION['user_id'];

// Fetch user first_name, orders, and order items
$stmt = $conn->prepare("
   SELECT 
    users.first_name,
    orders.id AS order_id,
    orders.total_price,
    orders.created_at,
    order_items.product_name,
    order_items.price,
    order_items.quantity
FROM orders
JOIN users ON orders.user_id = users.id
JOIN order_items ON orders.id = order_items.order_id
WHERE orders.user_id = ?
ORDER BY orders.created_at DESC;
");

$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[$row['order_id']]['details'] = [
        'total_price' => $row['total_price'],
        'created_at' => $row['created_at'],
    ];
    // Prepend '/images/' to the image_url to form the correct path
    $orders[$row['order_id']]['items'][] = [
        'product_name' => $row['product_name'],
        'price' => $row['price'],
        'quantity' => $row['quantity']
    ];
}
?>
<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ჩემი შეკვეთები</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav>
            <ul>
            <li><a href="my_orders.php"> მომხმარებლის შეკვეთა</a></li>
          

               
          <li><a href="index.html">მთავარი</a></li>
          <li><a href="about.html">ჩვენს შესახებ</a></li>
          <li><a href="services.html">სერვისები</a></li>
          <li><a href="Size.html">გაიგე ზომა მარტივად</a></li>
          <li><a href="registration.php">რეგისტრაცია</a></li>
          <li><a href="login.php">შესვლა</a></li>
        
          
          
          <li>
              <a href="logout.php">
                  გამოსვლა
                  <img src="imige/logout-icon-template-black-color-editable-log-out-icon-symbol-flat-illustration-for-graphic-and-web-design-free-vector.jpg"  alt="Logout" style="width:40px; height:40px; vertical-align:middle;">
                  
              </a>
          </li>
            </ul>
        </nav>
    </header>
    <main>
        <h2>ჩემი შეკვეთები</h2>
        <?php if (!empty($orders)): ?>
            <ul>
                <?php foreach ($orders as $orderId => $order): ?>
                    <li>
                        <strong>შეკვეთის ID:</strong> <?php echo $orderId; ?><br>
                        <strong>ჯამი:</strong> <?php echo number_format($order['details']['total_price'], 2); ?>₾<br>
                        <strong>თარიღი:</strong> <?php echo $order['details']['created_at']; ?><br>
                        <h3>პროდუქტები:</h3>
                        <ul>
                            <?php foreach ($order['items'] as $item): ?>
                                <li>
                                    <!-- Displaying the product image -->
                                    <strong>სახელი:</strong> <?php echo htmlspecialchars($item['product_name']); ?><br>
                                    <strong>ფასი:</strong> <?php echo number_format($item['price'], 2); ?>₾<br>
                                    <strong>რაოდენობა:</strong> <?php echo $item['quantity']; ?><br>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <hr>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>შეკვეთები ვერ მოიძებნა.</p>
        <?php endif; ?>
    </main>
</body>
<script src="cart.js"></script>
</html>
