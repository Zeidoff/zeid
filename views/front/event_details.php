<?php
include_once '../../config.php';
include_once '../../controllers/EventController.php';

$controller = new EventController($pdo);
if (!isset($_GET['id'])) {
    echo "<script>window.location.href = 'events.php';</script>";
    exit;
}
$result = $controller->getEventDetails($_GET['id']);

if ($result['status'] === 'error') {
    echo "<script>window.location.href = '{$result['redirect']}';</script>";
    exit;
}
$event = $result['data'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($event->name); ?> - EventSphere</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .hero-pattern {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%);
        }
        .btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .btn-secondary {
            background: white;
            color: #6366f1;
            border: 1px solid #6366f1;
            transition: all 0.3s ease;
        }
        .btn-secondary:hover {
            background: #f5f7ff;
            transform: translateY(-2px);
        }
        .detail-card {
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        .detail-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .event-image {
            height: 400px;
            object-fit: cover;
            width: 100%;
        }
        @media (max-width: 768px) {
            .event-image {
                height: 250px;
            }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Include Navbar -->
    <?php include 'navbar.php'; ?>

    <main class="container mx-auto px-4 py-8">
        <!-- Breadcrumb -->
        <div class="mb-6 flex items-center text-sm text-gray-500">
            <a href="index.php" class="hover:text-indigo-600">Home</a>
            <span class="mx-2"><i class="fas fa-chevron-right text-xs"></i></span>
            <a href="events.php" class="hover:text-indigo-600">Events</a>
            <span class="mx-2"><i class="fas fa-chevron-right text-xs"></i></span>
            <span class="text-gray-800"><?php echo htmlspecialchars($event->name); ?></span>
        </div>

        <!-- Event Header -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
            <?php if ($event->image): ?>
                <img src="../../<?php echo htmlspecialchars($event->image); ?>" alt="<?php echo htmlspecialchars($event->name); ?>" class="event-image w-full">
            <?php else: ?>
                <div class="event-image bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            <?php endif; ?>
            
            <div class="p-6 md:p-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($event->name); ?></h1>
                        <div class="flex items-center text-gray-600 mb-4">
                            <i class="fas fa-user-circle text-indigo-500 mr-2"></i>
                            <span>Organized by <?php echo htmlspecialchars($event->organizer); ?></span>
                        </div>
                    </div>
                    
                    <?php if ($_SESSION['user']['role'] === 'participant'): ?>
                        <form method="POST" action="participate.php" class="mt-4 md:mt-0">
                            <input type="hidden" name="event_id" value="<?php echo $event->id; ?>">
                            <button type="submit" class="btn-primary text-white py-3 px-6 rounded-lg font-medium w-full md:w-auto">
                                <i class="fas fa-calendar-check mr-2"></i>Participate Now
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
                
                <div class="flex flex-wrap gap-4 mt-6">
                    <div class="flex items-center bg-indigo-50 text-indigo-700 py-2 px-4 rounded-full">
                        <i class="fas fa-calendar-day mr-2"></i>
                        <span><?php echo htmlspecialchars($event->date); ?></span>
                    </div>
                    
                    <div class="flex items-center bg-purple-50 text-purple-700 py-2 px-4 rounded-full">
                        <i class="fas fa-clock mr-2"></i>
                        <span>Starts at 10:00 AM</span>
                    </div>
                    
                    <div class="flex items-center bg-blue-50 text-blue-700 py-2 px-4 rounded-full">
                        <i class="fas fa-users mr-2"></i>
                        <span>125 Participants</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Event Details -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-md p-6 mb-6 detail-card">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-info-circle text-indigo-500 mr-2"></i>Event Details
                    </h2>
                    <p class="text-gray-600 mb-6 leading-relaxed"><?php echo htmlspecialchars($event->description); ?></p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <div class="bg-indigo-100 p-3 rounded-full mr-4">
                                <i class="fas fa-map-marker-alt text-indigo-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Location</p>
                                <p class="font-medium">Convention Center, Main Hall</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <div class="bg-purple-100 p-3 rounded-full mr-4">
                                <i class="fas fa-tag text-purple-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Category</p>
                                <p class="font-medium">Conference</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <div class="bg-blue-100 p-3 rounded-full mr-4">
                                <i class="fas fa-language text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Language</p>
                                <p class="font-medium">English</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <div class="bg-green-100 p-3 rounded-full mr-4">
                                <i class="fas fa-ticket-alt text-green-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Ticket Type</p>
                                <p class="font-medium">Free Admission</p>
                            </div>
                        </div>
                    </div>
                </div>
                
          
             
            </div>
            
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Organizer Info -->
                <div class="bg-white rounded-xl shadow-md p-6 mb-6 detail-card">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-user-circle text-indigo-500 mr-2"></i>Organizer
                    </h2>
                    
                    <div class="flex items-center mb-4">
                        <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-building text-indigo-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-800"><?php echo htmlspecialchars($event->organizer); ?></h3>
                            <p class="text-gray-600 text-sm">Event Management Company</p>
                        </div>
                    </div>
                    
                    <p class="text-gray-600 text-sm mb-4">We specialize in creating memorable events that bring people together and foster community building.</p>
                    
                    <div class="flex space-x-3">
                        <a href="#" class="text-gray-400 hover:text-indigo-600">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-indigo-600">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-indigo-600">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-indigo-600">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Share Event -->
                <div class="bg-white rounded-xl shadow-md p-6 mb-6 detail-card">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-share-alt text-indigo-500 mr-2"></i>Share This Event
                    </h2>
                    
                    <div class="flex space-x-3 justify-center">
                        <a href="#" class="bg-blue-100 text-blue-600 w-10 h-10 rounded-full flex items-center justify-center hover:bg-blue-200 transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="bg-blue-200 text-blue-600 w-10 h-10 rounded-full flex items-center justify-center hover:bg-blue-300 transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="bg-pink-100 text-pink-600 w-10 h-10 rounded-full flex items-center justify-center hover:bg-pink-200 transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="bg-blue-700 text-white w-10 h-10 rounded-full flex items-center justify-center hover:bg-blue-800 transition">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="bg-green-100 text-green-600 w-10 h-10 rounded-full flex items-center justify-center hover:bg-green-200 transition">
                            <i class="fas fa-link"></i>
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
</body>
</html>