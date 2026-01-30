<?php
session_start();
require 'db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $password = $_POST['password'];

    $stmt = $conn->prepare(
        "SELECT user_id, role FROM users WHERE user_id = ? AND password = ?"
    );
    $stmt->bind_param("is", $user_id, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];

        header("Location: dashboard.php");
        exit();
    } else {
        $error = "❌ Invalid User ID or Password";
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CrimeSync Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen flex items-center justify-center bg-pink-100">

<div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md">
    <h2 class="text-2xl font-bold text-center text-pink-600 mb-6">
        CrimeSync Login 🔐
    </h2>

    <?php if (!empty($error)): ?>
        <p class="text-red-600 text-center mb-4"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <div>
            <label class="font-semibold">User ID</label>
            <input type="number" name="user_id" required
                   class="w-full p-2 border rounded">
        </div>

        <div>
            <label class="font-semibold">Password</label>
            <input type="password" name="password" required
                   class="w-full p-2 border rounded">
        </div>

        <button class="w-full bg-pink-500 text-white py-2 rounded hover:bg-pink-600">
            Login
        </button>
    </form>

    <p class="text-center mt-4">
        New user?
        <a href="register.php" class="text-pink-600 font-semibold">
            Register here
        </a>
    </p>
</div>

</body>
</html>
