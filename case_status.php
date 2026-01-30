<?php
session_start();
require 'db.php';

// Check role
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'officer' && $_SESSION['role'] !== 'admin')) {
    echo "❌ Access Denied.";
    exit();
}

// Update logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $report_id = $_POST['report_id'];
    $status = $_POST['status'];
    $officer_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO case_status (report_id, status, updated_by)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE
        status = VALUES(status),
        updated_at = CURRENT_TIMESTAMP,
        updated_by = VALUES(updated_by)");

    $stmt->bind_param("isi", $report_id, $status, $officer_id);

    if ($stmt->execute()) {
        $success = true;
    } else {
        $error = true;
    }
}

// Fetch reports
$reports = $conn->query("SELECT report_id, crime_type, area FROM crime_reports ORDER BY report_time DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CrimeSync: Update Case Status</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: url('https://images.unsplash.com/photo-1618477461853-e627b754f4c9?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3') no-repeat center center fixed;
            background-size: cover;
            position: relative;
            overflow: hidden;
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
                CrimeSync: Update Case Status 📊
            </h1>

            <!-- Success/Error Messages -->
            <?php if (isset($success)): ?>
                <p class="text-center text-green-600">✅ Case status updated successfully!</p>
            <?php elseif (isset($error)): ?>
                <p class="text-center text-red-600">❌ Failed to update status.</p>
            <?php endif; ?>

            <!-- Form -->
            <form method="POST" class="space-y-4">
                <div>
                    <label for="report_id" class="block text-pink-800 font-semibold">📋 Select Report</label>
                    <select id="report_id" name="report_id" required class="form-field w-full p-3 border border-pink-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                        <option value="">-- Choose Report --</option>
                        <?php while ($row = $reports->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($row['report_id']); ?>">
                                [ID: <?= htmlspecialchars($row['report_id']); ?>] <?= htmlspecialchars($row['crime_type']); ?> - <?= htmlspecialchars($row['area']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-pink-800 font-semibold">📈 Status</label>
                    <select id="status" name="status" required class="form-field w-full p-3 border border-pink-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                        <option value="">-- Select Status --</option>
                        <option value="Pending">Pending</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Solved">Solved</option>
                    </select>
                </div>
                <button type="submit" class="btn w-full bg-pink-500 text-white py-3 rounded-lg font-semibold hover:bg-pink-600 transition duration-300">
                    Update ✅
                </button>
            </form>

            <!-- Back Link -->
            <div class="text-center">
                <a href="dashboard.php" class="inline-block bg-pink-500 text-white text-center py-3 px-6 rounded-lg font-semibold hover:bg-pink-600 transition duration-300">
                    ⬅️ Back to Dashboard
                </a>
            </div>

            <!-- Footer Text -->
            <div class="text-center text-sm text-pink-700">
                Keep our community safe with timely updates 🌸
            </div>
        </div>
    </div>
</body>
</html>