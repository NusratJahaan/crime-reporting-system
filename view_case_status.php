<?php
session_start();
require 'db.php';

// Only officers and admins are allowed
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['officer', 'admin'])) {
    echo "❌ Access denied.";
    exit();
}

// Get all case statuses along with report and officer info
$query = "SELECT cs.report_id, cs.status, cs.updated_at, cs.updated_by, u.name AS officer_name
FROM case_status cs
LEFT JOIN users u ON cs.updated_by = u.user_id
ORDER BY cs.updated_at DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CrimeSync: View Case Status</title>
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
        .status-row:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
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
        <div class="container w-full max-w-4xl rounded-2xl shadow-2xl p-8 space-y-6">
            <!-- Header Text -->
            <h1 class="text-3xl font-extrabold text-pink-900 text-center animate-pulse">
                CrimeSync: All Case Status Records 📋
            </h1>

            <?php if ($result->num_rows > 0): ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-pink-500 text-white">
                                <th class="p-4">🆔 Report ID</th>
                                <th class="p-4">📊 Status</th>
                                <th class="p-4">⏰ Last Updated</th>
                                <th class="p-4">👮 Officer ID</th>
                                <th class="p-4">👤 Officer Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr class="status-row bg-white border-b border-pink-200 transition duration-300">
                                    <td class="p-4"><?php echo htmlspecialchars($row['report_id']); ?></td>
                                    <td class="p-4"><?php echo htmlspecialchars($row['status']); ?></td>
                                    <td class="p-4"><?php echo htmlspecialchars($row['updated_at']); ?></td>
                                    <td class="p-4"><?php echo htmlspecialchars($row['updated_by']); ?></td>
                                    <td class="p-4"><?php echo htmlspecialchars($row['officer_name'] ?? 'N/A'); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center text-pink-700 text-lg">😔 No case status records found.</p>
            <?php endif; ?>

            <!-- Back Link -->
            <div class="text-center">
                <a href="dashboard.php" class="inline-block bg-pink-500 text-white text-center py-3 px-6 rounded-lg font-semibold hover:bg-pink-600 transition duration-300">
                    ⬅️ Back to Dashboard
                </a>
            </div>

            <!-- Footer Text -->
            <div class="text-center text-sm text-pink-700">
                Tracking progress for a safer community 🌸
            </div>
        </div>
    </div>
</body>
</html>