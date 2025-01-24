<?php
session_start();
require_once 'config.php'; // დაკავშირება ბაზასთან

$error_message = '';
$first_name = $last_name = $email = $password = $confirm_password = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['password'], $_POST['confirm_password'])) {
    
        $first_name = htmlspecialchars(trim($_POST['first_name']));
        $last_name = htmlspecialchars(trim($_POST['last_name']));
        $email = htmlspecialchars(trim($_POST['email']));
        $password = trim($_POST['password']);
        $confirm_password = trim($_POST['confirm_password']);
        
        
        $error = false;
        
        // ამოწმებს არის თუ არა ყველა გვერდი შევსებული
        if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($confirm_password)) {
            $error = true;
            $error_message = "გთხოვთ შეავსოთ ყველა ველი";
        }
        
        // ამოწმებს ელ ფოსტის ფორმატს
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = true;
            $error_message = "არასწორი ელ-ფოსტის ფორმატი";
        }
        
        // ამოწმებს პაროლი ემთხვევა თუ არა
        if ($password !== $confirm_password) {
            $error = true;
            $error_message = "პაროლები არ ემთხვევა";
        }
        
        // პაროლის მინიმალური სიმბოლოების რაოდენობა
        if (strlen($password) < 8) {
            $error = true;
            $error_message = "პაროლი უნდა იყოს მინიმუმ 8 სიმბოლო";
        }
        
        if (!$error) {
           
            $plain_password = $password;

            
            try {
                // ამოწმებს ეს ემაილი უკვე გამოყენებულია თუ არა
                $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $error_message = "ასეთი ელ-ფოსტა უკვე არსებობს";
                } else {
                    // ამატებს ახალ user-ს
                    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("ssss", $first_name, $last_name, $email, $plain_password);
                    
                    if ($stmt->execute()) {
                        $_SESSION['success_message'] = "რეგისტრაცია წარმატებით დასრულდა!";
                        
                        header("Location: login.php");
                        exit();
                    } else {
                        $error_message = "დაფიქსირდა შეცდომა, სცადეთ თავიდან";
                    }
                }
            } catch (Exception $e) {
                $error_message = "დაფიქსირდა შეცდომა: " . $e->getMessage();
            }
        }
    } else {
        $error_message = "მოხდა შეცდომა, გთხოვთ სცადოთ თავიდან.";
    }
}


// Display error message if any
if (!empty($error_message)) {
    echo "<div class='error-message' style='color: red;'>" . $error_message . "</div>";
}
?>


 
<form method="POST" action="registration.php" class="registration-form">
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
<link rel="stylesheet" href="contact.css">

    <label for="first_name">სახელი:</label>
    <input type="text" id="first_name" name="first_name" value="<?= $first_name ?>" required>
    
    <label for="last_name">გვარი:</label>
    <input type="text" id="last_name" name="last_name" value="<?= $last_name ?>" required>
    
    <label for="email">ელ-ფოსტა:</label>
    <input type="email" id="email" name="email" value="<?= $email ?>" required>
    
    <label for="password">პაროლი:</label>
    <input type="password" id="password" name="password" required>
    
    <label for="confirm_password">გაიმეორე პაროლი:</label>
    <input type="password" id="confirm_password" name="confirm_password" required>
    
    <button type="submit">რეგისტრაცია</button>
</form>
<script src="cart.js"></script>
