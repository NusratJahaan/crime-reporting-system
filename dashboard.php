<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CrimeSync Dashboard</title>
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
        .dashboard-container {
            backdrop-filter: blur(8px);
            background: rgba(255, 255, 255, 0.9);
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        .sidebar {
            background: rgba(255, 192, 203, 0.95);
            animation: slideIn 0.5s ease-out;
        }
        @keyframes slideIn {
            from { transform: translateX(-100%); }
            to { transform: translateX(0); }
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
        <div class="dashboard-container w-full max-w-md rounded-2xl shadow-2xl p-8 space-y-6">
            <!-- Header Text -->
            <div class="text-center">
                <h1 class="text-3xl font-extrabold text-pink-900 animate-pulse">
                    CrimeSync: Empowering Safer Communities
                </h1>
            </div>

            <div class="flex justify-center">
                <span class="inline-block bg-pink-200 text-pink-800 text-sm font-medium px-4 py-1 rounded-full animate-pulse">
                    Logged in as: <?php echo htmlspecialchars($_SESSION['role']); ?>
                </span>
            </div>
            <h2 class="text-2xl font-bold text-pink-900 text-center">
                Welcome to the <?php echo ucfirst($role); ?> Dashboard
            </h2>
            <div class="space-y-4">
                <?php if ($role === 'citizen'): ?>
                    <!-- Citizen options -->
                    <a href="submit_report.php" class="btn block w-full bg-pink-500 text-white text-center py-3 rounded-lg font-semibold hover:bg-pink-600 transition duration-300">
                        Submit New Report
                    </a>
                    <a href="view_report.php" class="btn block w-full bg-pink-500 text-white text-center py-3 rounded-lg font-semibold hover:bg-pink-600 transition duration-300">
                        View My Reports
                    </a>
                <?php elseif ($role === 'officer'): ?>
                    <!-- Officer options -->
                    <a href="view_all_reports.php" class="btn block w-full bg-pink-500 text-white text-center py-3 rounded-lg font-semibold hover:bg-pink-600 transition duration-300">
                        View All Reports
                    </a>
                    <a href="case_status.php" class="btn block w-full bg-pink-500 text-white text-center py-3 rounded-lg font-semibold hover:bg-pink-600 transition duration-300">
                        Add / Update Case Status
                    </a>
                    <a href="view_case_status.php" class="btn block w-full bg-pink-500 text-white text-center py-3 rounded-lg font-semibold hover:bg-pink-600 transition duration-300">
                        📄 View All Case Status
                    </a>
                <?php endif; ?>
                <a href="logout.php" class="btn block w-full bg-red-500 text-white text-center py-3 rounded-lg font-semibold hover:bg-red-600 transition duration-300">
                    Logout
                </a>
            </div>
            <!-- Footer Text -->
            <div class="text-center text-sm text-pink-700">
                Together, we build a safer tomorrow.
            </div>
        </div>
    </div>
</body>
</html>
