<?php
session_start();
require 'db.php';

// Check if user is logged in and has citizen role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'citizen') {
    echo "❌ Access denied. Please log in as a citizen.";
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $crime_type = $_POST['crime_type'];
    $area = $_POST['area'];
    $description = $_POST['description'];
    $report_time = date('Y-m-d H:i:s');

    // Insert crime report
    $stmt_report = $conn->prepare("INSERT INTO crime_reports (user_id, crime_type, area, description, report_time) VALUES (?, ?, ?, ?, ?)");
    $stmt_report->bind_param("issss", $user_id, $crime_type, $area, $description, $report_time);

    if ($stmt_report->execute()) {
        $report_id = $conn->insert_id; // Get the ID of the newly inserted report
        $success = "✅ Report submitted successfully!";
        $stmt_report->close(); // Close the report statement

        // Handle evidence upload if provided
        if (isset($_FILES['evidence']) && $_FILES['evidence']['error'] === UPLOAD_ERR_OK) {
            $file_name = $_FILES['evidence']['name'];
            $file_tmp = $_FILES['evidence']['tmp_name'];

            // Create uploads folder if not exists
            $upload_dir = "uploads/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Unique file path
            $target_path = $upload_dir . time() . '_' . basename($file_name);

            if (move_uploaded_file($file_tmp, $target_path)) {
                // Save file path in evidence table
                $stmt_evidence = $conn->prepare("INSERT INTO evidence (report_id, file_path) VALUES (?, ?)");
                $stmt_evidence->bind_param("is", $report_id, $target_path);

                if ($stmt_evidence->execute()) {
                    $success .= " 📎 Evidence uploaded successfully!";
                } else {
                    $error = "❌ Failed to insert evidence record.";
                }
                $stmt_evidence->close(); // Close the evidence statement
            } else {
                $error = "❌ Failed to move uploaded file.";
            }
        }
    } else {
        $error = "❌ Error submitting report.";
        $stmt_report->close(); // Close the report statement on failure
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CrimeSync: Submit Report</title>
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
                CrimeSync: Report a Crime 🚨
            </h1>

            <div class="flex justify-center">
                <span class="inline-block bg-pink-200 text-pink-800 text-sm font-medium px-4 py-1 rounded-full animate-pulse">
                    Logged in as: <?php echo htmlspecialchars($_SESSION['role']); ?>
                </span>
            </div>

            <!-- Success/Error Messages -->
            <?php if (isset($success)): ?>
                <p class="text-center text-green-600"><?php echo $success; ?></p>
            <?php elseif (isset($error)): ?>
                <p class="text-center text-red-600"><?php echo $error; ?></p>
            <?php endif; ?>

            <!-- Form -->
            <form method="POST" enctype="multipart/form-data" class="space-y-4">
                <div>
                    <label for="crime_type" class="block text-pink-800 font-semibold">🚨 Crime Type</label>
                    <input type="text" id="crime_type" name="crime_type" required class="form-field w-full p-3 border border-pink-300 rounded-lg focus:ring-2 focus:ring-pink-500" placeholder="e.g., Theft, Vandalism">
                </div>
                <div>
                    <label for="area" class="block text-pink-800 font-semibold">📍 Area</label>
                    <input type="text" id="area" name="area" required class="form-field w-full p-3 border border-pink-300 rounded-lg focus:ring-2 focus:ring-pink-500" placeholder="e.g., Downtown, Park Street">
                </div>
                <div>
                    <label for="description" class="block text-pink-800 font-semibold">📝 Description</label>
                    <textarea id="description" name="description" required class="form-field w-full p-3 border border-pink-300 rounded-lg focus:ring-2 focus:ring-pink-500" rows="5" placeholder="Describe the incident..."></textarea>
                </div>
                <div>
                    <label for="evidence" class="block text-pink-800 font-semibold">📎 Upload Evidence (optional)</label>
                    <input type="file" id="evidence" name="evidence" class="form-field w-full p-3 border border-pink-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                </div>
                <button type="submit" class="btn w-full bg-pink-500 text-white py-3 rounded-lg font-semibold hover:bg-pink-600 transition duration-300">
                    Submit Report ✅
                </button>
            </form>

            <!-- Navigation Links -->
            <div class="text-center">
                <a href="dashboard.php" class="inline-block bg-pink-500 text-white text-center py-3 px-6 rounded-lg font-semibold hover:bg-pink-600 transition duration-300 btn">
                    ⬅️ Back to Dashboard
                </a>
            </div>

            <!-- Footer Text -->
            <div class="text-center text-sm text-pink-700">
                Your vigilance makes our community safer 🌸
            </div>
        </div>
    </div>
</body>
</html>