<?php
require_once dirname(__DIR__, 2) . '/config.php';
require_once dirname(__DIR__, 2) . '/controllers/EventController.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'organizer') {
    echo "<script>window.location.href = '../front/login.php';</script>";
    exit;
}

$eventController = new EventController($pdo);

// Handle form submission for create or edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $result = $eventController->createEvent($_POST, $_FILES);
        if ($result['status'] === 'success') {
            echo "<script>alert('Event created successfully!'); window.location.href = 'organizer_dashboard.php';</script>";
            exit;
        } else {
            $errorMessage = $result['message'];
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'edit' && isset($_POST['id'])) {
        $result = $eventController->updateEvent($_POST, $_FILES);
        if ($result['status'] === 'success') {
            echo "<script>alert('Event updated successfully!'); window.location.href = 'organizer_dashboard.php';</script>";
            exit;
        } else {
            $errorMessage = $result['message'];
        }
    }
}

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $deleteResult = $eventController->delete($_GET['id']);
    if ($deleteResult['status'] === 'success') {
        echo "<script>alert('Event deleted successfully!'); window.location.href = 'organizer_dashboard.php';</script>";
        exit;
    } else {
        $errorMessage = $deleteResult['message'];
    }
}

$eventsResult = $eventController->index();
$events = $eventsResult['status'] === 'success' ? $eventsResult['data'] : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organizer Dashboard - EventSphere</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1a365d 0%, #2a4365 100%);
        }
        .btn-primary {
            background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #3182ce 0%, #2b6cb0 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .table-row:hover {
            background-color: #2d3748;
        }
        .modal {
            transition: opacity 0.3s ease, transform 0.3s ease;
            transform: scale(0.9);
            opacity: 0;
        }
        .modal.active {
            transform: scale(1);
            opacity: 1;
        }
    </style>
</head>
<body class="min-h-screen text-gray-200">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white">Organizer Dashboard</h1>
                <p class="text-blue-200">Welcome back, <?php echo htmlspecialchars($_SESSION['user']['username']); ?></p>
            </div>
            <div class="flex items-center space-x-4">
                <button type="button" onclick="openModal('create')" class="btn-primary text-white px-6 py-3 rounded-lg font-medium inline-flex items-center justify-center cursor-pointer">
                    <i class="fas fa-plus mr-2"></i>
                    <span>Create New Event</span>
                </button>
                <a href="../front/logout.php" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </a>
            </div>
        </div>

        <!-- Error Message -->
        <?php if (isset($errorMessage)): ?>
            <div class="bg-red-600 text-white p-4 rounded-lg mb-6">
                <i class="fas fa-exclamation-circle mr-2"></i> <?php echo htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>

        <!-- Events Table -->
        <div class="bg-blue-900 rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 border-b border-blue-700 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-white">Your Events</h2>
                    <p class="text-blue-200">Manage all your created events</p>
                </div>
                <span class="bg-blue-600 text-white text-sm px-3 py-1 rounded-full">
                    <?php echo count($events); ?> Events
                </span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-blue-800">
                            <th class="p-4 text-left font-semibold text-blue-200">Event Name</th>
                            <th class="p-4 text-left font-semibold text-blue-200">Date</th>
                            <th class="p-4 text-left font-semibold text-blue-200">Participants</th>
                            <th class="p-4 text-left font-semibold text-blue-200">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($events)): ?>
                            <?php foreach ($events as $event): 
                                // Get participation count for each event
                                $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM participations WHERE event_id = ?");
                                $stmt->execute([$event->id]);
                                $participation = $stmt->fetch(PDO::FETCH_ASSOC);
                            ?>
                                <tr class="table-row border-t border-blue-800">
                                    <td class="p-4">
                                        <div class="flex items-center">
                                            <?php if ($event->image): ?>
                                                <div class="w-10 h-10 bg-cover bg-center rounded-full mr-3" style="background-image: url('../../<?php echo htmlspecialchars($event->image); ?>')"></div>
                                            <?php else: ?>
                                                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center mr-3">
                                                    <i class="fas fa-calendar text-white"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <p class="font-medium text-white"><?php echo htmlspecialchars($event->name); ?></p>
                                                <p class="text-blue-300 text-sm"><?php echo htmlspecialchars($event->description); ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 text-blue-200"><?php echo date('M j, Y', strtotime($event->date)); ?></td>
                                    <td class="p-4">
                                        <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded-full">
                                            <?php echo $participation['count']; ?> participants
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex space-x-2">
                                            <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode($event)); ?>)" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-500 transition">
                                                <i class="fas fa-edit mr-1"></i> Edit
                                            </button>
                                            <a href="?action=delete&id=<?php echo $event->id; ?>" onclick="return confirm('Are you sure you want to delete this event?')" class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-500 transition">
                                                <i class="fas fa-trash mr-1"></i> Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="p-8 text-center">
                                    <div class="text-blue-300 mb-4">
                                        <i class="fas fa-calendar-plus text-4xl"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-white mb-2">No events yet</h3>
                                    <p class="text-blue-200 mb-4">Create your first event to get started</p>
                                    <button onclick="openModal('create')" class="btn-primary text-white px-4 py-2 rounded-lg">
                                        Create Your First Event
                                    </button>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="eventModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 modal hidden">
        <div class="bg-blue-900 rounded-xl w-full max-w-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 id="modalTitle" class="text-xl font-bold text-white">Create New Event</h3>
                <button onclick="closeModal()" class="text-blue-300 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="eventForm" action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="eventId">
                
                <div class="mb-4">
                    <label class="block text-blue-200 text-sm mb-2" for="name">Event Name</label>
                    <input type="text" id="name" name="name" required class="w-full bg-blue-800 border border-blue-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500">
                </div>
                
                <div class="mb-4">
                    <label class="block text-blue-200 text-sm mb-2" for="description">Description</label>
                    <textarea id="description" name="description" rows="3" class="w-full bg-blue-800 border border-blue-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500"></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="block text-blue-200 text-sm mb-2" for="date">Event Date</label>
                    <input type="date" id="date" name="date" required class="w-full bg-blue-800 border border-blue-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500">
                </div>
                
                <div class="mb-4">
                    <label class="block text-blue-200 text-sm mb-2" for="image">Event Image</label>
                    <input type="file" id="image" name="image" accept="image/*" class="w-full bg-blue-800 border border-blue-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500">
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal()" class="text-blue-300 hover:text-white">Cancel</button>
                    <button type="submit" id="submitButton" class="btn-primary text-white px-4 py-2 rounded-lg">Create Event</button>
                </div>
            </form>
        </div>
    </div>

  <script>
// Modal functions
function openModal(mode = 'create') {
    const modal = document.getElementById('eventModal');
    const form = document.getElementById('eventForm');
    const modalTitle = document.getElementById('modalTitle');
    const formAction = document.getElementById('formAction');
    const submitButton = document.getElementById('submitButton');

    // Reset form for create mode
    if (mode === 'create') {
        form.reset();
        document.getElementById('eventId').value = '';
        modalTitle.textContent = 'Create New Event';
        formAction.value = 'create';
        submitButton.textContent = 'Create Event';
        document.getElementById('image').required = true;
    }

    // Show modal
    modal.classList.remove('hidden');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function openEditModal(event) {
    const modal = document.getElementById('eventModal');
    const formAction = document.getElementById('formAction');
    const modalTitle = document.getElementById('modalTitle');
    const submitButton = document.getElementById('submitButton');
    const eventId = document.getElementById('eventId');
    const name = document.getElementById('name');
    const description = document.getElementById('description');
    const date = document.getElementById('date');

    // Set form values for edit
    modalTitle.textContent = 'Edit Event';
    formAction.value = 'edit';
    submitButton.textContent = 'Update Event';
    eventId.value = event.id;
    name.value = event.name || '';
    description.value = event.description || '';
    date.value = event.date ? event.date.split(' ')[0] : ''; // Handle date format
    document.getElementById('image').required = false;

    // Show modal
    modal.classList.remove('hidden');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    const modal = document.getElementById('eventModal');
    modal.classList.remove('active');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.getElementById('eventForm').reset(); // Reset form on close
}

// Close modal when clicking outside
document.getElementById('eventModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Set minimum date to today for create mode
document.getElementById('date').min = new Date().toISOString().split('T')[0];

// Ensure modal is hidden on page load
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('eventModal').classList.add('hidden');
});
</script>
</body>
</html>