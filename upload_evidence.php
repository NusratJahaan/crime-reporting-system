<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $report_id = $_POST['report_id'];

    if (isset($_FILES['evidence']) && $_FILES['evidence']['error'] === 0) {
        $file_name = $_FILES['evidence']['name'];
        $file_tmp = $_FILES['evidence']['tmp_name'];
        $file_type = $_FILES['evidence']['type'];

        // Create uploads folder if not exists
        $upload_dir = "uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Unique file path
        $target_path = $upload_dir . time() . '_' . basename($file_name);

        if (move_uploaded_file($file_tmp, $target_path)) {
            // Save file path in database
            $stmt = $conn->prepare("INSERT INTO evidence (report_id, file_path, file_type) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $report_id, $target_path, $file_type);

            if ($stmt->execute()) {
                $success = "✅ Evidence uploaded successfully.";
            } else {
                $error = "❌ Failed to insert evidence record.";
            }

            $stmt->close();
        } else {
            $error = "❌ Failed to move uploaded file.";
        }
    } else {
        $error = "❌ No file uploaded or upload error.";
    }
} else {
    $error = "❌ Invalid request.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CrimeSync: Upload Evidence</title>
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
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255, 182, 193, 0.2), rgba(255, 105, 180, 0.2), rgba(255, 20, 147, 0.2));
            z-index: -1;
            animation: gradientShift 15s ease infinite;
        }
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .container {
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
        .form-field:focus {
            outline: none;
            ring: 2px solid #ec4899;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body class="min-h-screen flex p-4">
    <!-- Sidebar with Crime Tips -->
    <div class="sidebar w-64 p-6 rounded-r-2xl shadow-lg space-y-4 text-pink-900">
        <h3 class="text-lg font-semibold">CrimeSync Safety Tips</h3>
        <ul class="text-sm space-y-2">
            <li>🛡️ Report suspicious activity immediately.</li>
            <li>🔒 Keep personal information secure.</li>
            <li>📞 Contact authorities for emergencies.</li>
            <li>🕵️‍♂️ Stay vigilant in public spaces.</li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex items-center justify-center">
        <div class="container w-full max-w-md rounded-2xl shadow-2xl p-8 space-y-6">
            <!-- Header Text -->
            <h1 class="text-3xl font-extrabold text-pink-900 text-center animate-pulse">
                CrimeSync: Upload Evidence 📎
            </h1>

            <div class="flex justify-center">
                <span class="inline-block bg-pink-200 text-pink-800 text-sm font-medium px-4 py-1 rounded-full animate-pulse">
                    Logged in as: <?php echo htmlspecialchars($_SESSION['role']); ?>
                </span>
            </div>

            <!-- Success/Error Message -->
            <?php if (isset($success)): ?>
                <p class="text-center text-green-600"><?php echo $success; ?></p>
            <?php elseif (isset($error)): ?>
                <p class="text-center text-red-600"><?php echo $error; ?></p>
            <?php endif; ?>

            <!-- Back Link -->
            <div class="text-center">
                <a href="dashboard.php" class="inline-block bg-pink-500 text-white text-center py-3 px-6 rounded-lg font-semibold hover:bg-pink-600 transition duration-300 btn">
                    ⬅️ Back to Dashboard
                </a>
            </div>

            <!-- Footer Text -->
            <div class="text-center text-sm text-pink-700">
                Supporting justice with evidence 🌸
            </div>
        </div>
    </div>
</body>
</html>