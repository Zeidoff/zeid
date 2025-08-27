<?php
include '../../config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'participant') {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Participant Dashboard - EventSphere</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7ff 0%, #e6e9ff 100%);
        }
        .dashboard-card {
            transition: all 0.3s ease;
            border-radius: 16px;
            overflow: hidden;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .swiper {
            width: 100%;
            padding: 30px 10px 50px 10px;
        }
        .swiper-slide {
            background-position: center;
            background-size: cover;
            width: 300px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .swiper-pagination-bullet-active {
            background-color: #6366f1 !important;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        }
        .action-card {
            transition: all 0.3s ease;
            border-radius: 16px;
            overflow: hidden;
        }
        .action-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        .event-card {
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .event-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="min-h-screen">
    <!-- Include Navbar -->
    <?php include 'navbar.php'; ?>

    <main class="container mx-auto px-4 py-8">
        <!-- Welcome Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-800 mb-3">Welcome back, <?php echo htmlspecialchars($_SESSION['user']['username']); ?>! 👋</h1>
            <p class="text-gray-600 text-lg max-w-2xl mx-auto">Ready to discover your next unforgettable experience? Let's find amazing events for you.</p>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
            <a href="events.php" class="action-card gradient-bg text-white p-8 rounded-2xl shadow-lg relative overflow-hidden group">
                <div class="absolute top-4 right-4 opacity-10">
                    <i class="fas fa-search text-6xl"></i>
                </div>
                <div class="flex items-center mb-6">
                    <div class="bg-white bg-opacity-20 p-4 rounded-xl mr-5">
                        <i class="fas fa-search text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold">Browse Events</h3>
                </div>
                <p class="text-white text-opacity-90 mb-6">Discover and join upcoming events that match your interests and passions.</p>
                <div class="flex items-center text-white text-opacity-90 group-hover:text-opacity-100 transition">
                    <span class="font-medium">Explore now</span>
                    <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>
            
            <a href="my_participations.php" class="action-card bg-white p-8 rounded-2xl shadow-lg relative overflow-hidden group border border-gray-100">
                <div class="absolute top-4 right-4 opacity-10 text-indigo-600">
                    <i class="fas fa-ticket-alt text-6xl"></i>
                </div>
                <div class="flex items-center mb-6">
                    <div class="bg-indigo-100 p-4 rounded-xl mr-5">
                        <i class="fas fa-ticket-alt text-2xl text-indigo-600"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-800">My Participations</h3>
                </div>
                <p class="text-gray-600 mb-6">View and manage your current event participations and registrations.</p>
                <div class="flex items-center text-indigo-600 group-hover:text-indigo-800 transition">
                    <span class="font-medium">View my events</span>
                    <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>
        </div>

        <!-- Featured Events Swiper -->
        <div class="mb-16">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold text-gray-800">Featured Events</h2>
                <a href="events.php" class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
                    View all <i class="fas fa-arrow-right ml-2 text-xs"></i>
                </a>
            </div>

            <!-- Swiper -->
            <div class="swiper featured-events-swiper">
                <div class="swiper-wrapper">
                    <!-- Event 1 -->
                    <div class="swiper-slide">
                        <div class="event-card bg-white h-full flex flex-col">
                            <div class="h-48 bg-gradient-to-r from-purple-500 to-pink-500 relative overflow-hidden">
                                <div class="absolute top-4 left-4 bg-white text-purple-600 text-xs font-bold px-3 py-1 rounded-full">
                                    Popular
                                </div>
                            </div>
                            <div class="p-5 flex-grow">
                                <h3 class="font-bold text-lg text-gray-800 mb-2">Tech Innovation Summit</h3>
                                <p class="text-gray-600 text-sm mb-4">Explore the latest in technology and innovation with industry leaders.</p>
                                <div class="flex items-center text-gray-500 text-sm mb-4">
                                    <i class="fas fa-calendar-day mr-2"></i>
                                    <span>Jun 15, 2023</span>
                                </div>
                                <div class="flex items-center text-gray-500 text-sm">
                                    <i class="fas fa-map-marker-alt mr-2"></i>
                                    <span>Convention Center</span>
                                </div>
                            </div>
                            <div class="px-5 pb-5">
                                <a href="events.php" class="block w-full bg-indigo-600 text-white text-center py-2 rounded-lg font-medium hover:bg-indigo-700 transition">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Event 2 -->
                    <div class="swiper-slide">
                        <div class="event-card bg-white h-full flex flex-col">
                            <div class="h-48 bg-gradient-to-r from-blue-500 to-teal-400 relative overflow-hidden">
                                <div class="absolute top-4 left-4 bg-white text-blue-600 text-xs font-bold px-3 py-1 rounded-full">
                                    New
                                </div>
                            </div>
                            <div class="p-5 flex-grow">
                                <h3 class="font-bold text-lg text-gray-800 mb-2">Music Festival</h3>
                                <p class="text-gray-600 text-sm mb-4">Experience live performances from top artists in an unforgettable setting.</p>
                                <div class="flex items-center text-gray-500 text-sm mb-4">
                                    <i class="fas fa-calendar-day mr-2"></i>
                                    <span>Jul 22, 2023</span>
                                </div>
                                <div class="flex items-center text-gray-500 text-sm">
                                    <i class="fas fa-map-marker-alt mr-2"></i>
                                    <span>City Park</span>
                                </div>
                            </div>
                            <div class="px-5 pb-5">
                                <a href="events.php" class="block w-full bg-indigo-600 text-white text-center py-2 rounded-lg font-medium hover:bg-indigo-700 transition">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Event 3 -->
                    <div class="swiper-slide">
                        <div class="event-card bg-white h-full flex flex-col">
                            <div class="h-48 bg-gradient-to-r from-green-500 to-emerald-400 relative overflow-hidden">
                                <div class="absolute top-4 left-4 bg-white text-green-600 text-xs font-bold px-3 py-1 rounded-full">
                                    Free
                                </div>
                            </div>
                            <div class="p-5 flex-grow">
                                <h3 class="font-bold text-lg text-gray-800 mb-2">Wellness Workshop</h3>
                                <p class="text-gray-600 text-sm mb-4">Learn techniques for mindfulness and stress management in daily life.</p>
                                <div class="flex items-center text-gray-500 text-sm mb-4">
                                    <i class="fas fa-calendar-day mr-2"></i>
                                    <span>Aug 5, 2023</span>
                                </div>
                                <div class="flex items-center text-gray-500 text-sm">
                                    <i class="fas fa-map-marker-alt mr-2"></i>
                                    <span>Community Center</span>
                                </div>
                            </div>
                            <div class="px-5 pb-5">
                                <a href="events.php" class="block w-full bg-indigo-600 text-white text-center py-2 rounded-lg font-medium hover:bg-indigo-700 transition">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Event 4 -->
                    <div class="swiper-slide">
                        <div class="event-card bg-white h-full flex flex-col">
                            <div class="h-48 bg-gradient-to-r from-red-500 to-orange-400 relative overflow-hidden">
                                <div class="absolute top-4 left-4 bg-white text-red-600 text-xs font-bold px-3 py-1 rounded-full">
                                    Limited
                                </div>
                            </div>
                            <div class="p-5 flex-grow">
                                <h3 class="font-bold text-lg text-gray-800 mb-2">Food & Wine Expo</h3>
                                <p class="text-gray-600 text-sm mb-4">Taste exquisite cuisine and fine wines from around the world.</p>
                                <div class="flex items-center text-gray-500 text-sm mb-4">
                                    <i class="fas fa-calendar-day mr-2"></i>
                                    <span>Sep 12, 2023</span>
                                </div>
                                <div class="flex items-center text-gray-500 text-sm">
                                    <i class="fas fa-map-marker-alt mr-2"></i>
                                    <span>Exhibition Hall</span>
                                </div>
                            </div>
                            <div class="px-5 pb-5">
                                <a href="events.php" class="block w-full bg-indigo-600 text-white text-center py-2 rounded-lg font-medium hover:bg-indigo-700 transition">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Event 5 -->
                    <div class="swiper-slide">
                        <div class="event-card bg-white h-full flex flex-col">
                            <div class="h-48 bg-gradient-to-r from-indigo-500 to-blue-400 relative overflow-hidden">
                                <div class="absolute top-4 left-4 bg-white text-indigo-600 text-xs font-bold px-3 py-1 rounded-full">
                                    Exclusive
                                </div>
                            </div>
                            <div class="p-5 flex-grow">
                                <h3 class="font-bold text-lg text-gray-800 mb-2">Art Exhibition</h3>
                                <p class="text-gray-600 text-sm mb-4">Discover works from emerging and established artists in a curated show.</p>
                                <div class="flex items-center text-gray-500 text-sm mb-4">
                                    <i class="fas fa-calendar-day mr-2"></i>
                                    <span>Oct 8, 2023</span>
                                </div>
                                <div class="flex items-center text-gray-500 text-sm">
                                    <i class="fas fa-map-marker-alt mr-2"></i>
                                    <span>Art Gallery</span>
                                </div>
                            </div>
                            <div class="px-5 pb-5">
                                <a href="events.php" class="block w-full bg-indigo-600 text-white text-center py-2 rounded-lg font-medium hover:bg-indigo-700 transition">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>

        <!-- Upcoming Events -->
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Your Upcoming Events</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Event 1 -->
                <div class="dashboard-card bg-white rounded-2xl overflow-hidden shadow-md border border-gray-100">
                    <div class="h-40 bg-gradient-to-r from-purple-400 to-indigo-500 relative">
                        <div class="absolute bottom-4 left-4">
                            <span class="bg-white text-purple-600 text-xs font-bold px-3 py-1 rounded-full">Tomorrow</span>
                        </div>
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-lg text-gray-800 mb-2">Yoga Workshop</h3>
                        <div class="flex items-center text-gray-500 text-sm mb-3">
                            <i class="fas fa-clock mr-2"></i>
                            <span>9:00 AM - 11:00 AM</span>
                        </div>
                        <div class="flex items-center text-gray-500 text-sm mb-4">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            <span>Sunset Park</span>
                        </div>
                        <a href="#" class="text-indigo-600 text-sm font-medium hover:text-indigo-800 flex items-center">
                            <i class="fas fa-info-circle mr-2"></i> View details
                        </a>
                    </div>
                </div>

                <!-- Event 2 -->
                <div class="dashboard-card bg-white rounded-2xl overflow-hidden shadow-md border border-gray-100">
                    <div class="h-40 bg-gradient-to-r from-green-400 to-blue-500 relative">
                        <div class="absolute bottom-4 left-4">
                            <span class="bg-white text-green-600 text-xs font-bold px-3 py-1 rounded-full">In 3 days</span>
                        </div>
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-lg text-gray-800 mb-2">Startup Networking</h3>
                        <div class="flex items-center text-gray-500 text-sm mb-3">
                            <i class="fas fa-clock mr-2"></i>
                            <span>6:00 PM - 9:00 PM</span>
                        </div>
                        <div class="flex items-center text-gray-500 text-sm mb-4">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            <span>Innovation Hub</span>
                        </div>
                        <a href="#" class="text-indigo-600 text-sm font-medium hover:text-indigo-800 flex items-center">
                            <i class="fas fa-info-circle mr-2"></i> View details
                        </a>
                    </div>
                </div>

                <!-- Event 3 -->
                <div class="dashboard-card bg-white rounded-2xl overflow-hidden shadow-md border border-gray-100">
                    <div class="h-40 bg-gradient-to-r from-orange-400 to-red-500 relative">
                        <div class="absolute bottom-4 left-4">
                            <span class="bg-white text-orange-600 text-xs font-bold px-3 py-1 rounded-full">Next week</span>
                        </div>
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-lg text-gray-800 mb-2">Film Screening</h3>
                        <div class="flex items-center text-gray-500 text-sm mb-3">
                            <i class="fas fa-clock mr-2"></i>
                            <span>7:30 PM - 10:00 PM</span>
                        </div>
                        <div class="flex items-center text-gray-500 text-sm mb-4">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            <span>City Theater</span>
                        </div>
                        <a href="#" class="text-indigo-600 text-sm font-medium hover:text-indigo-800 flex items-center">
                            <i class="fas fa-info-circle mr-2"></i> View details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; 2025 EventSphere. All rights reserved.</p>
        </div>
    </footer>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    <script>
        // Initialize Swiper
        var swiper = new Swiper(".featured-events-swiper", {
            effect: "coverflow",
            grabCursor: true,
            centeredSlides: true,
            slidesPerView: "auto",
            coverflowEffect: {
                rotate: 0,
                stretch: 0,
                depth: 100,
                modifier: 2,
                slideShadows: true,
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            breakpoints: {
                640: {
                    slidesPerView: 1,
                },
                768: {
                    slidesPerView: 2,
                },
                1024: {
                    slidesPerView: 3,
                },
            },
        });
    </script>
</body>
</html>