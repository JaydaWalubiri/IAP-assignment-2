<?php
class User {
    private $username;
    private $email;
    private $phone;
    private $password;

    public function __construct($username, $email, $phone, $password) {
        $this->username = $username;
        $this->email = $email;
        $this->phone = $phone;
        $this->password = password_hash($password, PASSWORD_BCRYPT); // Hash password
    }

    public function validateInput() {
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            return "Invalid email format.";
        }
        if (!preg_match("/^[0-9]{10,15}$/", $this->phone)) {
            return "Invalid phone number.";
        }
        return null; // No errors
    }

    public function saveToDatabase($pdo) {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, phone, password) VALUES (?, ?, ?, ?)");
        $stmt->execute([$this->username, $this->email, $this->phone, $this->password]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = new User($_POST['username'], $_POST['email'], $_POST['phone'], $_POST['password']);
    $error = $user->validateInput();

    if ($error) {
        echo "<p style='color:red;'>$error</p>";
    } else {
        // PDO connection
        $pdo = new PDO('mysql:host=localhost;dbname=user_data', 'root', '');
        $user->saveToDatabase($pdo);
        echo "User registered successfully!";
    }
}


?>
