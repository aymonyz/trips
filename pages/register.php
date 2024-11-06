<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['emailAddress'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);  // Hash the password

    try {
        $stmt = $pdo->prepare("INSERT INTO user (name, emailAddress, password, role) VALUES (?, ?, ?, 'user')");
        if ($stmt->execute([$name, $email, $password])) {
            echo "Registration successful!";
        } else {
            echo "Error occurred during registration.";
        }
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
    
}
?>

<div class="container mt-5">
    <h2>Register</h2>
    <form method="POST" action="index.php?page=register">
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" class="form-control" id="email" name="emailAddress" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
    <p>Already have an account? <a href="index.php?page=login">Login here</a></p>
</div>
