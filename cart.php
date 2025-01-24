<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ka">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>კალათა</title>
    <link rel="stylesheet" href="formebi.css">
    <style>
        
        .cart-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-left: 300px;
            margin-right: 300px;
        }
        .cart-item img {
            width: 60px;
            height: 60px;
            margin-right: 15px;
            border-radius: 5px;
        }
        .cart-item .details {
            flex: 1;
        }
        .cart-item .details span {
            color:Green;
            display: block;
        }
        
        .cart-item button {
            background: #f44336;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
        }
        .cart-item button:hover {
            background: #d32f2f;
        }
        .total-box {
            font-size: 1.2em;
            margin-top: 20px;
        }
        .cart-actions button {
            margin: 10px 5px;
            padding: 10px 20px;
            font-size: 1em;
            cursor: pointer;
        }
        .cart-actions .clear-cart {
            background-color: #ff9800;
            color: white;
            border: none;
            border-radius: 5px;
            margin-left: 300px;

        }
        .cart-actions .clear-cart:hover {
            background-color: #e68a00;
        }
        .cart-actions .checkout {
            background-color: #4caf50;
            color: white;
            border: none;
            border-radius: 5px;
            margin-right: 300px;
        }
        .cart-actions .checkout:hover {
            background-color: #45a049;
        }

        #notification {
            display: none;
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            opacity: 0;
            transition: opacity 0.5s;
        }
        
    </style>
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
        <div class="cart-icon" onclick="goToCart()">
            🛒 <span id="cart-count"><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?></span>
        </div>
    </header>
    <div id="notification"></div> 

    <main>
        <div class="cart">
            <h2 class="kalata">კალათა</h2>
            <ul id="cartItems">
                <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
                    <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                        <li class="cart-item">
                        <img src="<?php echo htmlspecialchars($item['imgSrc']); ?>" alt="პროდუქტის ფოტო"> <!-- Product image -->
                            <div class="details">
                                <span><strong><?php echo htmlspecialchars($item['name']); ?></strong></span>
                                <span><?php echo number_format($item['price'], 2); ?>₾</span>
                                <span>რაოდენობა: <?php echo $item['quantity']; ?></span> <!-- Display quantity -->
                            </div>
                            <button onclick="removeFromCart(<?php echo $index; ?>)">წაშლა</button>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>კალათა ცარიელია.</li>
                <?php endif; ?>
            </ul>
            <div class="total-box">
                ჯამი: <span id="total">
                    <?php
                    echo isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'price')) : 0;
                    ?>₾
                </span>
            </div>
            <div class="cart-actions">
                <button class="clear-cart" onclick="clearCart()">კალათის გასუფთავება</button>
                <button class="checkout" onclick="checkout()">
                    <i class="fas fa-shopping-cart"></i> შეკვეთის გაკეთება
                </button>
            </div>
        </div>
    </main>

    <footer>
        <p>© 2024 ჩვენი კომპანია</p>
    </footer>

    <script src="cart.js"></script>
</body>
</html>
