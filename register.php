<?php
session_start();
require 'db.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    $contact = $_POST['contact_number'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Security check: Ensure no one injected 'admin' via browser inspect tools
    if ($role === 'admin') {
        $error = "❌ Unauthorized role selection.";
    } else {
        // Check if user_id already exists
        $check = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
        $check->bind_param("i", $user_id);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error = "❌ User ID already exists. Choose another one.";
        } else {
            // Hash password for better security (Recommended)
            // $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("INSERT INTO users (user_id, name, contact_number, password, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issss", $user_id, $name, $contact, $password, $role);

            if ($stmt->execute()) {
                $success = "✅ User registered successfully. <a href='login.php' class='text-pink-600 font-semibold hover:underline'>➡ Go to Login</a>";
            } else {
                $error = "❌ Registration failed. " . $conn->error;
            }
            $stmt->close();
        }
        $check->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CrimeSync: Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: url('https://images.unsplash.com/photo-1618477461853-e627b754f4c9?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3') no-repeat center center fixed;
            background-size: cover;
            position: relative;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(45deg, rgba(255, 182, 193, 0.2), rgba(255, 105, 180, 0.2), rgba(255, 20, 147, 0.2));
            z-index: -1;
            animation: gradientShift 15s ease infinite;
        }
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .register-box {
            backdrop-filter: blur(8px);
            background: rgba(255, 255, 255, 0.95);
        }
        .sidebar {
            background: rgba(255, 192, 203, 0.95);
            animation: slideIn 0.5s ease-out;
        }
        @keyframes slideIn {
            from { transform: translateX(-100%); }
            to { transform: translateX(0); }
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body class="min-h-screen flex p-4">
    <div class="sidebar w-64 p-6 rounded-r-2xl shadow-lg space-y-4 text-pink-900 hidden md:block">
        <h3 class="text-lg font-semibold">CrimeSync Safety Tips</h3>
        <ul class="text-sm space-y-2">
            <li>🛡️ Report suspicious activity immediately.</li>
            <li>🔒 Keep personal information secure.</li>
            <li>📞 Contact authorities for emergencies.</li>
            <li>🕵️‍♂️ Stay vigilant in public spaces.</li>
        </ul>
    </div>

    <div class="flex-1 flex items-center justify-center">
        <div class="register-box w-full max-w-md rounded-2xl shadow-2xl p-8 space-y-6">
            <h1 class="text-3xl font-extrabold text-pink-900 text-center animate-pulse">
                CrimeSync: Join Us 🌟
            </h1>

            <?php if (!empty($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded relative text-center"><?php echo $error; ?></div>
            <?php elseif (!empty($success)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded relative text-center"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-pink-800 font-semibold">🆔 User ID</label>
                    <input type="number" name="user_id" required class="w-full p-3 border border-pink-300 rounded-lg focus:ring-2 focus:ring-pink-500" placeholder="Unique ID">
                </div>
                <div>
                    <label class="block text-pink-800 font-semibold">👤 Name</label>
                    <input type="text" name="name" required class="w-full p-3 border border-pink-300 rounded-lg focus:ring-2 focus:ring-pink-500" placeholder="Full Name">
                </div>
                <div>
                    <label class="block text-pink-800 font-semibold">📞 Contact Number</label>
                    <input type="text" name="contact_number" required class="w-full p-3 border border-pink-300 rounded-lg focus:ring-2 focus:ring-pink-500" placeholder="Phone">
                </div>
                <div>
                    <label class="block text-pink-800 font-semibold">🔒 Password</label>
                    <input type="password" name="password" required class="w-full p-3 border border-pink-300 rounded-lg focus:ring-2 focus:ring-pink-500" placeholder="Password">
                </div>
                <div>
                    <label class="block text-pink-800 font-semibold">🎭 Role</label>
                    <select name="role" required class="w-full p-3 border border-pink-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                        <option value="citizen">Citizen</option>
                        <option value="officer">Officer</option>
                    </select>
                </div>
                <button type="submit" class="btn w-full bg-pink-500 text-white py-3 rounded-lg font-semibold hover:bg-pink-600 transition duration-300">
                    Register 📝
                </button>
            </form>

            <div class="text-center">
                <p class="text-pink-800">
                    Already have an account? <a href="login.php" class="text-pink-600 font-semibold hover:underline">Back to Login ➡</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>