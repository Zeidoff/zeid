<?php
include '../../config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo "<script>window.location.href = '../../views/front/login.php';</script>";
    exit;
}

include '../../controllers/EventController.php';
include '../../controllers/UserController.php';
include '../../controllers/ParticipationController.php';

// Get statistics
$eventController = new EventController($pdo);
$statsResult = $eventController->stats();
$stats = $statsResult['status'] === 'success' ? $statsResult['data'] : [];

// Get user count
$userController = new UserController($pdo);
$usersResult = $userController->index();
$userCount = $usersResult['status'] === 'success' ? count($usersResult['data']) : 0;

// Get total participations
$participationController = new ParticipationController($pdo);
$totalParticipations = 0;
$totalRevenue = 0;

foreach ($stats as $stat) {
    $totalParticipations += $stat['participants'];
    $totalRevenue += $stat['sales'] * 49.99; // Assuming $49.99 per participation
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - EventSphere</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            overflow-x: hidden;
        }
        .dashboard-card {
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
        }
        .stats-card {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            border: 1px solid #334155;
            border-radius: 12px;
        }
        .nav-item {
            transition: all 0.3s ease;
        }
        .nav-item:hover {
            background: rgba(99, 102, 241, 0.1);
        }
        .nav-item.active {
            background: rgba(99, 102, 241, 0.2);
            border-left: 4px solid #6366f1;
        }
        .chart-container {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            border-radius: 12px;
            border: 1px solid #334155;
            height: 350px;
            position: relative;
        }
        .chart-wrapper {
            width: 100%;
            height: 300px;
            position: relative;
        }
    </style>
</head>
<body class="min-h-screen text-gray-200">
    <div class="flex">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-900 min-h-screen p-4 fixed h-full">
            <div class="flex items-center justify-center mb-8 mt-4">
                <div class="bg-indigo-600 p-2 rounded-lg mr-3">
                    <i class="fas fa-calendar-alt text-white text-xl"></i>
                </div>
                <h1 class="text-xl font-bold text-white">EventSphere Admin</h1>
            </div>
            
            <nav class="space-y-1">
                <a href="admin_dashboard.php" class="nav-item active flex items-center px-4 py-3 text-white rounded-lg">
                    <i class="fas fa-chart-pie mr-3"></i>
                    Dashboard
                </a>
                <a href="users.php" class="nav-item flex items-center px-4 py-3 text-gray-400 hover:text-white rounded-lg">
                    <i class="fas fa-users mr-3"></i>
                    Manage Users
                </a>
                <a href="events.php" class="nav-item flex items-center px-4 py-3 text-gray-400 hover:text-white rounded-lg">
                    <i class="fas fa-calendar mr-3"></i>
                    Manage Events
                </a>
               
                <a href="../front/logout.php" class="nav-item flex items-center px-4 py-3 text-gray-400 hover:text-white rounded-lg">
                    <i class="fas fa-sign-out-alt mr-3"></i>
                    Logout
                </a>
            </nav>
            
            <div class="mt-8 p-4 bg-gray-800 rounded-lg">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center mr-3">
                        <span class="text-white font-semibold"><?php echo strtoupper(substr($_SESSION['user']['username'], 0, 1)); ?></span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-white"><?php echo htmlspecialchars($_SESSION['user']['username']); ?></p>
                        <p class="text-xs text-gray-400">Administrator</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="ml-64 flex-1 p-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-white">Admin Dashboard</h1>
                    <p class="text-gray-400">Welcome back, <?php echo htmlspecialchars($_SESSION['user']['username']); ?></p>
                </div>
                <div class="flex items-center">
                    <div class="mr-4 relative">
                        <i class="fas fa-bell text-gray-400 hover:text-white cursor-pointer"></i>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center">3</span>
                    </div>
                    <div class="bg-gray-800 p-2 rounded-lg">
                        <i class="fas fa-cog text-gray-400 hover:text-white cursor-pointer"></i>
                    </div>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="stats-card p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-400 text-sm">Total Events</p>
                            <h2 class="text-2xl font-bold text-white mt-1"><?php echo count($stats); ?></h2>
                            <p class="text-green-400 text-xs mt-2"><i class="fas fa-calendar mr-1"></i> All events</p>
                        </div>
                        <div class="bg-indigo-500 p-3 rounded-lg">
                            <i class="fas fa-calendar text-white"></i>
                        </div>
                    </div>
                </div>
                
                <div class="stats-card p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-400 text-sm">Total Users</p>
                            <h2 class="text-2xl font-bold text-white mt-1"><?php echo $userCount; ?></h2>
                            <p class="text-green-400 text-xs mt-2"><i class="fas fa-users mr-1"></i> Registered users</p>
                        </div>
                        <div class="bg-green-500 p-3 rounded-lg">
                            <i class="fas fa-users text-white"></i>
                        </div>
                    </div>
                </div>
                
                <div class="stats-card p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-400 text-sm">Total Participations</p>
                            <h2 class="text-2xl font-bold text-white mt-1"><?php echo $totalParticipations; ?></h2>
                            <p class="text-green-400 text-xs mt-2"><i class="fas fa-ticket-alt mr-1"></i> All participations</p>
                        </div>
                        <div class="bg-yellow-500 p-3 rounded-lg">
                            <i class="fas fa-ticket-alt text-white"></i>
                        </div>
                    </div>
                </div>
                
               
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Participation Chart -->
                <div class="chart-container p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Event Participation Overview</h3>
                    <div class="chart-wrapper">
                        <canvas id="participationChart"></canvas>
                    </div>
                </div>
                
                <!-- Revenue Chart -->
                <div class="chart-container p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Top Events by Participation</h3>
                    <div class="chart-wrapper">
                        <canvas id="eventsChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <h2 class="text-xl font-bold text-white mb-6">Quick Actions</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <a href="users.php" class="dashboard-card bg-gradient-to-r from-indigo-700 to-indigo-600 p-6 rounded-xl text-center group">
                    <div class="bg-white bg-opacity-20 w-14 h-14 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">Manage Users</h3>
                    <p class="text-indigo-200 text-sm">Create, edit, or delete user accounts</p>
                    <div class="mt-4 text-white opacity-0 group-hover:opacity-100 transition-opacity">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
                
                <a href="events.php" class="dashboard-card bg-gradient-to-r from-purple-700 to-purple-600 p-6 rounded-xl text-center group">
                    <div class="bg-white bg-opacity-20 w-14 h-14 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-calendar text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">Manage Events</h3>
                    <p class="text-purple-200 text-sm">Oversee all events in the system</p>
                    <div class="mt-4 text-white opacity-0 group-hover:opacity-100 transition-opacity">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
                
               
            </div>

            <!-- Event Statistics Table -->
            <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden mb-8">
                <div class="p-6 border-b border-gray-700">
                    <h2 class="text-xl font-bold text-white">Event Statistics</h2>
                    <p class="text-gray-400">Detailed overview of event participations and sales</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-750">
                                <th class="p-4 text-left font-semibold text-gray-300">Event Name</th>
                                <th class="p-4 text-left font-semibold text-gray-300">Participants</th>
                                <th class="p-4 text-left font-semibold text-gray-300">Sales (Participations)</th>
                                <th class="p-4 text-left font-semibold text-gray-300">Revenue</th>
                                <th class="p-4 text-left font-semibold text-gray-300">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($stats)): ?>
                                <?php foreach ($stats as $stat): ?>
                                    <tr class="border-t border-gray-700 hover:bg-gray-750 transition">
                                        <td class="p-4"><?php echo htmlspecialchars($stat['name']); ?></td>
                                        <td class="p-4">
                                            <div class="flex items-center">
                                                <span class="mr-2"><?php echo $stat['participants']; ?></span>
                                                <div class="w-16 bg-gray-600 rounded-full h-2">
                                                    <?php 
                                                    $maxParticipants = max(array_column($stats, 'participants'));
                                                    $width = $maxParticipants > 0 ? ($stat['participants'] / $maxParticipants) * 100 : 0;
                                                    ?>
                                                    <div class="bg-indigo-500 h-2 rounded-full" style="width: <?php echo $width; ?>%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-4"><?php echo $stat['sales']; ?></td>
                                        <td class="p-4">$<?php echo number_format($stat['sales'] * 49.99, 2); ?></td>
                                        <td class="p-4">
                                            <?php if ($stat['participants'] > 0): ?>
                                                <?php if ($stat['participants'] >= 50): ?>
                                                    <span class="bg-green-500 text-white text-xs px-2 py-1 rounded-full">Popular</span>
                                                <?php elseif ($stat['participants'] >= 20): ?>
                                                    <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full">Active</span>
                                                <?php else: ?>
                                                    <span class="bg-yellow-500 text-white text-xs px-2 py-1 rounded-full">Moderate</span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="bg-gray-500 text-white text-xs px-2 py-1 rounded-full">No Participants</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="p-4 text-center text-gray-400">No event statistics available</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Participation Chart - Using real data
        document.addEventListener('DOMContentLoaded', function() {
            const participationCtx = document.getElementById('participationChart').getContext('2d');
            const participationChart = new Chart(participationCtx, {
                type: 'bar',
                data: {
                    labels: [
                        <?php 
                        if (!empty($stats)) {
                            $eventNames = array_map(function($stat) {
                                return "'" . addslashes(substr($stat['name'], 0, 15)) . (strlen($stat['name']) > 15 ? '...' : '') . "'";
                            }, $stats);
                            echo implode(', ', $eventNames);
                        } else {
                            echo "'No Events'";
                        }
                        ?>
                    ],
                    datasets: [{
                        label: 'Participants',
                        data: [
                            <?php 
                            if (!empty($stats)) {
                                $participants = array_map(function($stat) {
                                    return $stat['participants'];
                                }, $stats);
                                echo implode(', ', $participants);
                            } else {
                                echo "0";
                            }
                            ?>
                        ],
                        backgroundColor: 'rgba(99, 102, 241, 0.7)',
                        borderColor: 'rgba(99, 102, 241, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: {
                                color: '#e2e8f0'
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            },
                            ticks: {
                                color: '#e2e8f0'
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            },
                            ticks: {
                                color: '#e2e8f0',
                                maxRotation: 45,
                                minRotation: 45
                            }
                        }
                    }
                }
            });

            // Events Chart - Top events by participation
            const eventsCtx = document.getElementById('eventsChart').getContext('2d');
            const eventsChart = new Chart(eventsCtx, {
                type: 'doughnut',
                data: {
                    labels: [
                        <?php 
                        if (!empty($stats)) {
                            // Sort by participants and take top 5
                            usort($stats, function($a, $b) {
                                return $b['participants'] - $a['participants'];
                            });
                            $topEvents = array_slice($stats, 0, 5);
                            $eventNames = array_map(function($stat) {
                                return "'" . addslashes(substr($stat['name'], 0, 15)) . (strlen($stat['name']) > 15 ? '...' : '') . "'";
                            }, $topEvents);
                            echo implode(', ', $eventNames);
                        } else {
                            echo "'No Events'";
                        }
                        ?>
                    ],
                    datasets: [{
                        data: [
                            <?php 
                            if (!empty($stats)) {
                                $participants = array_map(function($stat) {
                                    return $stat['participants'];
                                }, $topEvents);
                                echo implode(', ', $participants);
                            } else {
                                echo "0";
                            }
                            ?>
                        ],
                        backgroundColor: [
                            'rgba(99, 102, 241, 0.8)',
                            'rgba(139, 92, 246, 0.8)',
                            'rgba(236, 72, 153, 0.8)',
                            'rgba(249, 115, 22, 0.8)',
                            'rgba(16, 185, 129, 0.8)'
                        ],
                        borderColor: [
                            'rgba(99, 102, 241, 1)',
                            'rgba(139, 92, 246, 1)',
                            'rgba(236, 72, 153, 1)',
                            'rgba(249, 115, 22, 1)',
                            'rgba(16, 185, 129, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#e2e8f0',
                                padding: 20
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>