<?php
session_start();
require 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "❌ Access denied. Please log in.";
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Admin sees all reports, others see their own
if ($role === 'admin') {
    $stmt = $conn->prepare("SELECT cr.*, cs.status, cs.updated_at, u2.name AS updated_by_name
        FROM crime_reports cr
        LEFT JOIN case_status cs ON cr.report_id = cs.report_id
        LEFT JOIN users u2 ON cs.updated_by = u2.user_id
        LEFT JOIN users u1 ON cr.user_id = u1.user_id
        ORDER BY cr.report_time DESC");
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $stmt = $conn->prepare("SELECT cr.*, cs.status, cs.updated_at, u2.name AS updated_by_name
        FROM crime_reports cr
        LEFT JOIN case_status cs ON cr.report_id = cs.report_id
        LEFT JOIN users u2 ON cs.updated_by = u2.user_id
        WHERE cr.user_id = ?
        ORDER BY cr.report_time DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CrimeSync Reports</title>
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
        .report-card:hover {
            transform: translateY(-4px);
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
        <div class="container w-full max-w-2xl rounded-2xl shadow-2xl p-8 space-y-6">
            <!-- Header Text -->
            <h1 class="text-3xl font-extrabold text-pink-900 text-center animate-pulse">
                CrimeSync: Your Crime Reports 📋
            </h1>

            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="report-card bg-white rounded-lg p-6 border border-pink-200 shadow-md transition duration-300">
                        <p class="text-pink-800 font-semibold">🆔 <strong>Report ID:</strong> <?php echo htmlspecialchars($row['report_id']); ?></p>
                        <p class="text-pink-800">🚨 <strong>Crime Type:</strong> <?php echo htmlspecialchars($row['crime_type']); ?></p>
                        <p class="text-pink-800">📍 <strong>Area:</strong> <?php echo htmlspecialchars($row['area']); ?></p>
                        <p class="text-pink-800">📝 <strong>Description:</strong> <?php echo htmlspecialchars($row['description']); ?></p>
                        <p class="text-pink-800">⏰ <strong>Report Time:</strong> <?php echo htmlspecialchars($row['report_time']); ?></p>
                        <p class="text-pink-800">📊 <strong>Status:</strong> <?php echo htmlspecialchars($row['status'] ?? 'Not Updated'); ?></p>
                        <?php if (!empty($row['updated_by_name'])): ?>
                            <p class="text-pink-800">👮 <strong>Last Updated By:</strong> Officer <?php echo htmlspecialchars($row['updated_by_name']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($row['updated_at'])): ?>
                            <p class="text-pink-800">🕒 <strong>Updated At:</strong> <?php echo htmlspecialchars($row['updated_at']); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center text-pink-700 text-lg">😔 No reports found.</p>
            <?php endif; ?>

            <!-- Back Link -->
            <div class="text-center">
                <a href="dashboard.php" class="inline-block bg-pink-500 text-white text-center py-3 px-6 rounded-lg font-semibold hover:bg-pink-600 transition duration-300">
                    ⬅️ Back to Dashboard
                </a>
            </div>

            <!-- Footer Text -->
            <div class="text-center text-sm text-pink-700">
                Stay proactive, stay safe with CrimeSync 🌸
            </div>
        </div>
    </div>
</body>
</html>