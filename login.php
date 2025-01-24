<?php
session_start();
require_once 'config.php'; // Include the database connection configuration

// Check if the user is already logged in
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // მიღებული მონაცემები
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // ვალიდაცია
    if (empty($email) || empty($password)) {
        $error_message = "გთხოვთ შეავსოთ ყველა ველი"; // "Please fill out all fields"
    } else {
        // SQL query-ი მომხმარებლის მოძებნისთვის
        $stmt = $conn->prepare("SELECT id, first_name, last_name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // პაროლის შედარება plain text-ით
            if ($password == $user['password']) {
                // პაროლი სწორია, პარამეტრები უნდა დაინიშნოს
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];
        
                header("Location: index.html"); // გადამისამართება მთავარ გვერდზე
                exit();
            } else {
                $error_message = "არასწორი ემაილი ან პაროლი"; // "Incorrect email or password"
            }
        } else {
            $error_message = "არასწორი ემაილი ან პაროლი"; // "Incorrect email or password"
        }
        
        $stmt->close(); // Close the statement
    }
}


?>


<!-- HTML part for the login form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="contact.css">
</head>
<header>
        <nav style="display: flex; align-items: center;">
            <ul style="flex-grow: 1; display: flex; list-style: none; padding: 0; margin: 0;">
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
<body>
    <h1>შესვლა</h1>
    
    <?php if (!empty($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <form action="login.php" method="POST" class="login-form">
        <label for="email">ელ-ფოსტა:</label>
        <input type="email" name="email" required><br>

        <label for="password">პაროლი:</label>
        <input type="password" name="password" required><br>

        <button type="submit">შესვლა</button>
        <script src="cart.js"></script>
    </form>
</body>
</html>
