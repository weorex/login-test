<?php

// Database connection details
$host = "127.0.0.1";
$port = "3306";
$dbname = "registration_db"; // Updated to your new database name
$username = "root";
$password = "root1234"; // Replace with your actual database password

// Establish database connection
try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST["name"];
    $email = $_POST["email"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Perform basic input validation
    if (empty($name) || empty($email) || empty($username) || empty($password) || empty($confirm_password)) {
        echo "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        echo "Passwords do not match.";
    } else {
        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare the SQL statement to insert user data
        $sql = "INSERT INTO users (name, email, username, password) VALUES (:name, :email, :username, :password)";
        $stmt = $pdo->prepare($sql);

        // Bind parameters
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":password", $hashed_password);

        // Execute the statement
        try {
            $stmt->execute();
            echo "Registration successful!";
            // Optionally, redirect the user to a success page
            // header("Location: registration_success.php");
            exit();
        } catch (PDOException $e) {
            // Check for duplicate username or email errors
            if ($e->getCode() == '23000') {
                if (strpos($e->getMessage(), 'username') !== false) {
                    echo "Username already exists. Please choose a different one.";
                } elseif (strpos($e->getMessage(), 'email') !== false) {
                    echo "Email address already exists.";
                } else {
                    echo "Error during registration: " . $e->getMessage();
                }
            } else {
                echo "Error during registration: " . $e->getMessage();
            }
        }
    }

    // Close the database connection
    $pdo = null;
} else {
    // If the script is accessed directly without submitting the form
    echo "Invalid request.";
}

?>