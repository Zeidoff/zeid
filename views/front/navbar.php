<?php
// Check if user is logged in
$isLoggedIn = isset($_SESSION['user']);
$userRole = $isLoggedIn ? $_SESSION['user']['role'] : null;
?>

<nav class="bg-white shadow-lg sticky top-0 z-50">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-3">
            <!-- Logo -->
            <a href="index.php" class="flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-600" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                </svg>
                <span class="text-xl font-bold text-gray-800">EventSphere</span>
            </a>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="index.php" class="text-gray-600 hover:text-indigo-600 transition">Home</a>
                <a href="events.php" class="text-gray-600 hover:text-indigo-600 transition">Events</a>
                
                <?php if ($isLoggedIn): ?>
                    <?php if ($userRole === 'participant'): ?>
                        <a href="my_participations.php" class="text-gray-600 hover:text-indigo-600 transition">My Participations</a>
                    <?php endif; ?>
                    
                    <div class="relative group">
                        <button class="flex items-center space-x-1 text-gray-600 hover:text-indigo-600 transition">
                            <span><?php echo htmlspecialchars($_SESSION['user']['username']); ?></span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300">
                            <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-100 hover:text-indigo-600">Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="text-gray-600 hover:text-indigo-600 transition">Login</a>
                    <a href="register.php" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">Register</a>
                <?php endif; ?>
            </div>

            <!-- Mobile menu button -->
            <button class="md:hidden text-gray-600 focus:outline-none" id="mobile-menu-button">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                </svg>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div class="md:hidden hidden py-3" id="mobile-menu">
            <a href="index.php" class="block py-2 text-gray-600 hover:text-indigo-600 transition">Home</a>
            <a href="events.php" class="block py-2 text-gray-600 hover:text-indigo-600 transition">Events</a>
            
            <?php if ($isLoggedIn): ?>
                <?php if ($userRole === 'participant'): ?>
                    <a href="index.php" class="block py-2 text-gray-600 hover:text-indigo-600 transition">Dashboard</a>
                    <a href="my_participations.php" class="block py-2 text-gray-600 hover:text-indigo-600 transition">My Participations</a>
                <?php endif; ?>
                
                <div class="pt-2 border-t border-gray-200 mt-2">
                    <p class="px-2 py-1 text-sm text-gray-500">Logged in as <?php echo htmlspecialchars($_SESSION['user']['username']); ?></p>
                    <a href="logout.php" class="block py-2 text-gray-600 hover:text-indigo-600 transition">Logout</a>
                </div>
            <?php else: ?>
                <a href="login.php" class="block py-2 text-gray-600 hover:text-indigo-600 transition">Login</a>
                <a href="register.php" class="block py-2 text-indigo-600 font-medium">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<script>
    // Mobile menu toggle
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
    });
</script>