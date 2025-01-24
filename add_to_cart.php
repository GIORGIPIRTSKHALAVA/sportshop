<?php
session_start();
header('Content-Type: application/json');

// შემოწმება: არის თუ არა მომხმარებელი ავტორიზებული
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'გთხოვთ გაიაროთ ავტორიზაცია.']);
    http_response_code(401); // Unauthorized
    exit();
}

// მიღებული პროდუქტის მონაცემები
$input = json_decode(file_get_contents('php://input'), true);
if (isset($input['name'], $input['price'])) {
    $productName = $input['name'];
    $productPrice = $input['price'];
    $productImgSrc = $input['imgSrc'];
    

    // შეიძლება კალათის სესია საჭირო გახდეს
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // პროდუქტის დამატება კალათაში
    $_SESSION['cart'][] = [
        'name' => $productName,
        'price' => $productPrice,
        'imgSrc' => $productImgSrc,
        
    ];

    echo json_encode(['success' => true, 'message' => "$productName დამატებულია კალათაში!"]);
} else {
    echo json_encode(['success' => false, 'message' => 'პროდუქტის მონაცემები არასწორია.']);
}
?>
