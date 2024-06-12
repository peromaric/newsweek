<?php
require 'config.php';
global $dbc;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $isAdmin = isset($_POST['isAdmin']) ? 1 : 0;

    $sql = "INSERT INTO users (username, password, isAdmin) VALUES (?, ?, ?)";

    if ($stmt = $dbc->prepare($sql)) {
        $stmt->bind_param("ssi", $username, $password, $isAdmin);

        if ($stmt->execute()) {
            header("Location: login.php");
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    $dbc->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            width: 300px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .link {
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Register</h2>
    <form method="POST" action="registration.php">
        <label>Username:</label>
        <input type="text" name="username" required><br>
        <label>Password:</label>
        <input type="password" name="password" required><br>
        <label>Admin:</label>
        <input type="checkbox" name="isAdmin"><br>
        <input type="submit" value="Register">
    </form>
    <div class="link">
        <a href="login.php">Already have an account? Login here</a>
    </div>
</div>
</body>
</html>
