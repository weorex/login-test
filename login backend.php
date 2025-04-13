<?php
session_start(); // Start the session to manage user login

// Database connection details
$host = "127.0.0.1";
$port = "3306";
$dbname = "registration_db"; // Use the database you created for registration
$username = "root";
$password = "root1234"; // Replace with your actual database password

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve username and password from the form
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Basic input validation (you might want to add more robust validation)
    if (empty($username) || empty($password)) {
        echo "Please enter both username and password.";
    } else {
        // Prepare the SQL query to fetch the user by username
        $sql = "SELECT id, username, password FROM users WHERE username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":username", $username);

        try {
            $stmt->execute();

            if ($stmt->rowCount() == 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $stored_hashed_password = $row["password"];

                // Verify the entered password against the stored hashed password
                if (password_verify($password, $stored_hashed_password)) {
                    // Password is correct, log the user in
                    $_SESSION["user_id"] = $row["id"];
                    $_SESSION["username"] = $row["username"];
                    // Optionally, you can redirect the user to a logged-in area
                    header("Location: welcome.php"); // Create a welcome.php page
                    exit();
                } else {
                    echo "Incorrect password.";
                }
            } else {
                echo "User not found.";
            }
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
        }
    }

    // Close the database connection
    $pdo = null;
} else {
    // If the script is accessed directly
    echo "Invalid request.";
}
?>